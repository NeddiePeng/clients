<?php
/**
 * 商家门店model.
 * User: Pengfan
 * Date: 2018/12/4
 * Time: 19:51
 */
namespace api\modules\v1\models;

use api\modules\Base;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

class Store extends ActiveRecord
{

    //其他字段
    public $clients_id;
    public $lat;
    public $lng;
    public $accessToken;
    public $page;
    public $sort_one;
    public $sort_two;
    public $top_sort;


    //数据表
    public static function tableName()
    {
        return "pay_store_info";
    }


    //字段验证规则
    public function rules()
    {
        return [
            [['clients_id','lat','lng'],'required','on' => 'index-like'],
            [['lat','lng','page','top_sort','sort_two'],'required','on' => 'nearby']
        ];
    }


    //字段信息
    public function attributeLabels()
    {
        return [
            'clients_id' => Yii::t('app','clients_id'),
            'lat' => Yii::t('app','lat'),
            'lng' => Yii::t('app','lng'),
            'page' => Yii::t('app','page'),
            'top_sort' => Yii::t('app','top_sort'),
            'sort_two' => Yii::t('app','sort_two')
        ];
    }


    /**
     * 获取商家列表
     *
     * @return    array | null
     */
    public function getStoreList()
    {
        $history = UserActions::browseHistory($this->accessToken, $this->clients_id);
        if($history)
        {
            $storeData = null;
            foreach ($history as $key => $val)
            {
                $data = $this->storeData($val['s_id']);
                if($data) $storeData[] = $data;
                if($data)
                {
                    foreach ($data as $k => $v)
                    {
                        $storeList = $this->conform($v['sort_one'],$v['sort_two']);
                        if($storeList) $storeData[] = $storeList;
                    }
                }
            }
            $storeData = $this->matching($storeData);
            return $storeData;
        }

        #TODO:没有历史记录的门店根据用户的地址
        $storeData = $this->nearbyPraise();
        if(!$storeData) return null;
        $last_data = null;
        foreach ($storeData as $key => $val)
        {
            $data = $this->storeData($val['id']);
            if($data) $last_data[] = $data;
        }
        return $last_data;
    }




    /**
     * 地理位置匹配
     *
     * @param   array   $storeData
     * @return  array | null
     */
    public function matching($storeData) {
        $scope = Base::calcScope($this->lat, $this->lng);
        if (!$storeData) return null;
        $last_data = null;
        foreach ($storeData as $key => $val)
        {
            if ($scope['maxLat'] > $val['lat'] && $scope['minLat'] < $val['lat'] && $scope['maxLng'] > $val['lng'] && $scope['minLng'] < $val['lng'])
                $last_data[] = $val;
        }
        return $last_data;
    }




    /**
     * 用户位置获取附近门店
     *
     * @return   array | null
     */
    public function nearbyPraise()
    {
        $scope = Base::calcScope($this->lat, $this->lng);
        $sql = 'SELECT * FROM `'. static::tableName() .'` WHERE `lat` <= '.$scope['maxLat'].' and `lat` >= '.$scope['minLat'];
        $sql .= ' and `lng` <= '.$scope['maxLng'].' and `lng` >= '.$scope['minLng'];
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        return $data;
    }



    /**
     * 附近分页
     *
     * @return   array | null
     */
    public function nearbyPage()
    {
        $scope = Base::calcScope($this->lat, $this->lng);
        $where = [
            'and',
            ['>=','lat',$scope['minLat']],
            ['<=','lat',$scope['maxLat']],
            ['>=','lng',$scope['minLng']],
            ['<=','lng',$scope['maxLng']],
            //['=','status',6]
        ];
        $params = [
            'top_sort' => $this->top_sort,
            'one_sort' => $this->sort_two
        ];
        $otherWhere = Yii::$app->where->select('nearby',$params);
        $offset = ($this->page - 1) * 10;
        $data = (new Query())
                ->select("*")
                ->from(static::tableName())
                ->where($where)
                ->andWhere($otherWhere)
                ->offset($offset)
                ->limit(10)
                ->all();
        return $data;
    }



    /**
     * 相似门店
     *
     * @param    int    $sort_one    一级分类
     * @param    int    $sort_two    二级分类
     * @return   array | null
     */
    public function conform($sort_one, $sort_two)
    {
        $where = [
            'and',
            ['=','one_sort',$sort_one],
            ['=','two_sort',$sort_two],
            ['=','status',6]
        ];
        $data = static::findAll($where);
        if(!$data) return null;
        $last_data = null;
        foreach ($data as $k => $v)
        {
            $headerData = $this->headerImg($v['id']);
            $otherData = $this->otherInfo($v['top_sort'],$v['id'],$v['score'],$v['two_sort']);
            $last_data[] = [
                's_id' => $v['id'],
                'store_name' => $v['store_name'],
                'address' => $v['address'],
                'lat' => $v['lat'],
                'lng' => $v['lng'],
                'headerImg' => $headerData ? $headerData['img_url'] : "",
                'one_sort' => $v['one_sort'],
                'two_sort' => $v['two_sort'],
                'pro_name' => $otherData[2],
                'score' => $v['score'],
                'exp' => $otherData[0],
                'money_name' => $otherData[1],
                'type' => $otherData[3]
            ];
        }
        return $last_data;
    }




    /**
     * 门店活动
     *
     * @param    int   $s_id   门店id
     * @return   string
     */
    public function storeAdvert($s_id)
    {
        $data = (new Query())
                ->select("*")
                ->from("pay_store_activity")
                ->where(['x_id' => $s_id,'status' => 1])
                ->all();
        if(!$data) return '';
        $name = '';
        foreach ($data as $k => $v)
        {
            if($v['type'] === 1)
            {
                $name .= $v['activity_title'].',';
            }
        }
        return trim($name,',');
    }




    /**
     * 酒店金额
     *
     * @param   int    $s_id    门店id
     * @return  int
     */
    public function hotelMoney($s_id)
    {
        $data = (new Query())
                ->select("*")
                ->from("pay_hotel_room_spec")
                ->where(['x_id' => $s_id])
                ->min("week_discount_price");
        return $data ? $data : 0;
    }




    /**
     * 门店商品
     *
     * @param   int   $sort_id   分类id
     * @return  array | null
     */
    public function storeSort($type, $sort_id)
    {
        $table = "pay_hotel_brand_star";
        $db = Yii::$app->db2;
        if($type !== 'hotel')
        {
            $table = "pay_store_sort";
            $db = Yii::$app->db;
        }
        $data = (new Query())
            ->select("*")
            ->from("$table")
            ->where(['id' => $sort_id])
            ->one($db);
        return $data;
    }



    /**
     * 商家详情
     *
     * @param    int        $s_id   门店id
     * @return   array | null
     */
    public function storeData($s_id)
    {
       return $this->storeDetails($s_id);
    }



    /**
     * 门店数据
     *
     * @param    int    $s_id   门店id
     * @return   array | null
     */
    public function storeDetails($s_id)
    {
        $data = (new Query())
                ->select("*")
                ->from(static::tableName())
                ->where(['id' => $s_id])
                ->one();
        if(!$data) return null;
        $headerData = $this->getStoreAlbum($s_id);
        $otherData = $this->getOtherInfo($s_id,$data['top_sort'],$data['one_sort']);
        //$otherData = $this->otherInfo($data['top_sort'],$s_id,$data['score'],$data['two_sort']);
        $msgData = $this->msgData($s_id);
        return [
            's_id' => $data['id'],
            'store_name' => $data['store_name'],
            'address' => $data['address'],
            'lat' => $data['lat'] ? $data['lat'] : 0,
            'lng' => $data['lng'] ? $data['lng'] : 0,
            'headerImg' => $headerData,
            'sort_name' => $otherData[0] ? $otherData[0]['sort_name'] : "",
            'score' => $data['score'],
            'type' => $data['top_sort'] == 2 ? 'hotel' : 'other',
            'like_num' => $data['like_num'] ? $data['like_num'] : 0,
            'share_num' => $data['share_num'] ? $data['share_num'] : 0,
            'proData' => $otherData[1],
            'msgData' => $msgData
        ];

    }



    /**
     * 门店消费信息
     *
     * @param   int   $s_id    门店id
     * @return  array  | null
     */
    public function msgData($s_id)
    {
        $data = [
            '测试数据_1',
            '测试数据_2',
            '测试数据_3'
        ];
        return $data;
    }




    /**
     * 门店商品
     *
     * @param    int    $s_id   门店id
     * @return   array | null
     */
    public function storePro($s_id)
    {
        $data = (new Query())
                ->select("*")
                ->from('pay_store_project_s_id')
                ->where(['x_id' => $s_id,'type' => mt_rand(1,3)])
                ->all();
        if(!$data) return null;
        $pro = [];
        $type = 0;
        foreach ($data as $k => $v)
        {
            $pro = $this->proData($v['project_id'],$v['type']);
            $type = $v['type'];
        }
        if(empty($pro)) return null;
        if($type == 1)
        {
            $name = $pro['vouchers_name'] ? $pro['vouchers_name'] : '代金券';
            $old = $pro['face_val'];
            $now = $pro['buy_price'];
        }
        else
        {
            $name = $pro['group_name'] ? $pro['group_name'] : "团购";
            $now = $pro['group_price'];
            $old = $pro['price'];
        }
        return [$name, $old, $now,$type];
    }




    /**
     * 商品信息
     *
     * @param    int    $p_id    商品id
     * @param    int    $type    类型
     * @return   array | null
     */
    public function proData($p_id, $type)
    {
        switch ($type)
        {
            case 1:
                $table = 'pay_store_vouchers';
                break;
            case 2:
                $table = 'pay_store_group';
                break;
            case 3:
                $table = 'pay_store_check';
                break;
            default:
                return null;
        }
        return $this->proDetails($table,$p_id);
    }



    /**
     * 商品详情
     *
     * @param     string    $table   数据表
     * @param     int       $p_id    商品id
     * @return    array | null
     */
    public function proDetails($table, $p_id)
    {
        $data = (new Query())
                ->select("*")
                ->from("$table")
                ->where(['id' => $p_id])
                ->one();
        return $data;
    }


    /**
     * 重写门店其他信息
     *
     * @param    int    $s_id       门店id
     * @param    int    $top_sort   顶级分类
     * @param    int    $sort_two   二级分类
     * @return   array | null
     */
    public function getOtherInfo($s_id, $top_sort, $sort_two, $type_select = null)
    {
        //分类
        $type = 'other';
        if ($top_sort == 2) $type = 'hotel';
        $sortData = $this->storeSort($type,$sort_two);
        if($type_select){
            $sortPro = StoreOther::instance()->proFormat($s_id,$top_sort);
        }else{
            $sortPro = StoreOther::instance()->storePro($s_id,$top_sort);
        }
        return [$sortData, $sortPro];
    }


    /**
     * 其他信息
     *
     * @return  array
     */
    public function otherInfo($top_sort,$s_id,$score,$sort_two)
    {
        if($top_sort == 2)
        {
            $exp = "良好";
            if($score >= 4)
            {
                $exp = "好";
            }
            $money_name = $this->hotelMoney($s_id);
            $type = 'hotel';
            $storePro = $this->storeSort($type,$sort_two);
            if($storePro['is_brand'] === 1)
            {
                $proSort = $storePro['brand_name'];
            }
            else
            {
                $proSort = "一般酒店";
                if($storePro['star_num'] >= 4) $proSort = "高档型";
            }
        }
        else
        {
            $exp = '';
            $type = 'other';
            $money_name = $this->storeAdvert($s_id);
            if (!$money_name) {
                $pro = $this->storePro($s_id);
                $proSort = $pro[0];
                $old = $pro[1];
                $now = $pro[2];
                $money_name = [
                    'old' => $old ? $old : 0,
                    'now' => $now ? $now : 0
                ];
            } else {
                $storePro = $this->storeSort($type, $sort_two);
                $proSort = $storePro['sort_name'];
            }
        }
        return [$exp,$money_name,$proSort,$type];
    }



    /**
     * 门店图册
     *
     * @param   int    $s_id   门店id
     * @param   int    $limit  显示数量
     * @return  array | null
     */
    public function getStoreAlbum($s_id, $limit = 3)
    {
        $where = [
            'x_id' => $s_id
        ];
        $data = (new Query())
            ->select("img_url")
            ->from("pay_store_album")
            ->where($where);
        if($limit == 3) return $data->limit(3)->all();
        return $data->all();
    }



    /**
     * 门店首图
     *
     * @param    int    $s_id   门店id
     * @return   array | null
     */
    public function headerImg($s_id)
    {
        $where = [
            'x_id' => $s_id
        ];
        $data = (new Query())
                ->select("img_url")
                ->from("pay_store_album")
                ->where(['type' => 1])
                ->andWhere($where)
                ->one();
        return $data;
    }


}