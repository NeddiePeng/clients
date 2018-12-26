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
    public $sort_id;
    public $keyWords;
    public $starPrice;
    public $s_id;
    public $r_id;
    public $hotelRoomUse = [
        '直接消费，不需要减付宝券，请携带入住人身份证，凭姓名和预定手机号直接办理入住'
    ];


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
            [['lat','lng','page','top_sort','sort_two'],'required','on' => 'nearby'],
            [['lat','lng'],'required','on' => 'store'],
            [['sort_id','page','lat','lng'],'required','on' => 'hotel-sort'],
            [['s_id'],'required','on' => 'hotel-details'],
            [['r_id'],'required','on' => 'room-details']
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
            'sort_two' => Yii::t('app','sort_two'),
            'sort_id' => Yii::t('app','sort_id'),
            's_id' => Yii::t('app','s_id'),
            'r_id' => Yii::t('app','r_id')
        ];
    }


    /**
     * 酒店房间详情
     *
     * @return   array | null
     */
    public function roomDetails()
    {
        $data = (new Query())
                ->select("spec.id as spec_id,spec.week_discount_price as price,spec.*,type.*")
                ->from("pay_hotel_room_spec as spec")
                ->leftJoin("pay_hotel_house_type as type",'type.id=spec.h_id')
                ->where(['spec.id' => $this->r_id])
                ->one(Yii::$app->db2);
        if(!$data) return null;
        $imgData = $this->headerImg($data['spec_id']);
        $desc = $data['refund_type'] == 1 ? "订单确认后不可取消/变更，如未入住，酒店将扣除全额房费" : "可取消";
        $hotelRoomUse = $this->hotelRoomUse;
        $last_data = [
            'room_id' => $data['spec_id'],
            'headerImg' => $imgData ? $imgData['img_url'] : "",
            'room_name' => $data['house_title']."({$data['spec_title']})",
            'inter' => "WIFI和宽带",
            'bathroom' => '独立卫生间',
            'is_window' => $data['is_window'] == 1 ? "含窗" : $data['is_window'] == 2 ? "部分含窗" : "不含窗",
            'acreage' => $data['acreage'],
            'floor_num' => $data['floor_num'],
            'contain_breakfast' => $data['is_contain_breakfast'] == 1 ? "含早" : $data['is_contain_breakfast'] == 2 ? "含双早" : "不含早",
            'refund' => [
                'is_refund' => $data['refund_type'] == 1 ? "不可取消" : "可取消",
                'desc' => $desc
            ],
            'useRule' => $hotelRoomUse,
            'price' => $data['price']
        ];
        return $last_data;
    }






    /**
     * 酒店详情
     *
     * @return  array | null
     */
    public function hotelDetails()
    {
        $data = (new Query())
                ->select("*")
                ->from("pay_store_info")
                ->where(['id' => $this->s_id])
                ->one();
        if(!$data) return null;
        $sortData = $this->storeSort($data['top_sort'],$data['one_sort']);
        $storeProData = StoreOther::instance()->detailsPro($data['id'],$data['top_sort']);
        $storeAlbum = $this->getStoreAlbum($data['id']);
        $storeMobile = StoreActions::instance()->storeMobile($data['id']);
        $hotelRoomRules = $this->roomRules();
        $infoData = [
            's_id' => $data['id'],
            'store_name' => $data['store_name'],
            'score' => $data['score'],
            'sort_name' => $sortData ? $sortData['sort_name'] : "",
            'per_capita' => 100,
            'mobile' => $storeMobile ? $storeMobile[0]['mobile'] : '',
            'address' => $data['address'],
            'Notice' => $data['Notice'],
            'headerImgData' => [
                'imgUrl' => $storeAlbum ? $storeAlbum[0]['img_url'] : "",
                'count' => count($storeAlbum ? $storeAlbum : [])
            ],
            'distance' => $this->sum($this->lat,$this->lng,$data['lat'],$data['lng']),
            'lat' => $data['lat'],
            'lng' => $data['lng']
        ];
        $commentScore = [
            'totalScore' => $data['score'],
            'otherScore' => [
                'flavor' => 0,
                'service' => 0,
                'scenario' => 0
            ]
        ];
        $storeInfo = null;
        $last_data['infoData'] = $infoData;
        $last_data['otherInfo'] = $hotelRoomRules;
        $last_data['commentScore'] = $commentScore;
        $last_data['proData'] = $storeProData;
        return $last_data;
    }



    /**
     * 订房须知
     *
     * @return  array | null
     */
    public function roomRules()
    {
        $facility = $this->hotelFacility();
        $hotelInfo = (new Query())
                     ->select("*")
                     ->from("pay_hotel_other_info")
                     ->where(['x_id' => $this->s_id])
                     ->one(Yii::$app->db2);
        $lastInfo = null;
        if($hotelInfo) {
            $lastInfo = [
                'Decoration' => date("Y", $hotelInfo['last_fixture_time']),
                'openTime' => date("Y",$hotelInfo['opening_time']),
                'floor_number' => $hotelInfo['floor_number'],
                'roomTotal' => $hotelInfo['rom_number']
            ];
        }
        $traffic = $this->hotelTraffic();
        return [
            'facility' => $facility,
            'otherInfo' => $lastInfo,
            'descriptions' => $hotelInfo['hotel_descriptions'],
            'traffic' => $traffic
        ];
    }



    /**
     * 交通信息
     *
     * @return  array | null
     */
    public function hotelTraffic()
    {
        return [
            [
                'name' => '市图书馆',
                'distance' => '1.2Km'
            ],
            [
                'name' => '地铁四号线常青路站',
                'distance' => '0.1km'
            ]
        ];
    }



    /**
     * 酒店设施
     *
     * @return  array | null
     */
    public function hotelFacility()
    {
        $data = (new Query())
                ->select("id,facilities_title,parent_id")
                ->from("pay_hotel_service_facilities")
                ->where(['status' => 1])
                ->all(Yii::$app->db2);
        if(!$data) return null;
        $idList = [];
        foreach ($data as $k => $v)
        {
            $idList[] = $v['id'];
        }
        $allRoom = $this->hotelRoomAll();
        if(!$allRoom) return null;
        $last_id_list = [];
        foreach ($allRoom as $k => $v)
        {
            if($v['unified_facilities']){
                $fIdList = explode(",",trim($v['unified_facilities'],','));
                foreach ($fIdList as $val)
                {
                    if(in_array($val,$idList) && !in_array($val,$last_id_list))
                    {
                        $last_id_list[] = $val;
                    }
                }
            };
        }
        $last_data = [];
        if($last_id_list)
        {
            foreach ($data as $k => $v)
            {
                if(in_array($v['id'],$last_id_list))
                {
                    $last_data[] = $v;
                }
            }
        }
        return $last_data;
    }



    /**
     * 酒店所有房型
     *
     * @return   array | null
     */
    public function hotelRoomAll()
    {
        $data = (new Query())
                ->select("*")
                ->from("pay_hotel_house_type")
                ->where(['x_id' => $this->s_id])
                ->andWhere(['status' => 1])
                ->all(Yii::$app->db2);
        return $data;
    }



    /**
     * 搜索酒店
     */
    public function searchHotel()
    {
        $params = [
            'page' => $this->page,
            'keyWords' => $this->keyWords,
            'starPrice' => $this->starPrice,
            'lat' => $this->lat,
            'lng' => $this->lng
        ];
        $where = Yii::$app->where->select('selectHotel',$params);
        $per = ($this->page - 1) * 10;
        $data = (new Query())
                ->select("*")
                ->from("pay_store_info")
                ->where(['top_sort' => 2])
                ->andWhere($where)
                ->offset($per)
                ->limit(10)
                ->all();
        if(!$data) return null;
        return $this->hotelDataUnification($data);
    }



    /**
     * 分类酒店数据
     *
     * @return   array  | null
     */
    public function hotelList()
    {
        $params = [
            'sort_id' => $this->sort_id
        ];
        $where = Yii::$app->where->select('hotel',$params);
        $data = (new Query())
                ->select("*")
                ->from("pay_store_info")
                ->all();
        if(!$data) return null;
        $data = $this->hotelSelect($data,$where);
        if(!$data) return null;
        return $this->hotelDataUnification($data);
    }



    /**
     * 酒店数据格式化
     *
     * @param   array   $data  数据集合
     * @return  array | null
     */
    public function hotelDataUnification($data)
    {
        $last_data = [];
        foreach ($data as $k => $v)
        {
            $dis = $this->sum($this->lat,$this->lng,$v['lat'],$v['lng']);
            $headerImg = $this->headerImg($v['id']);
            $last_data[] = [
                's_id' => $v['id'],
                'store_name' => $v['store_name'],
                'score' => $v['score'],
                'consume_num' => $v['consume'],
                'address' => $v['address'],
                'distance' => $dis,
                'headerImg' => $headerImg ? [$headerImg] : null,
                'price' => 100
            ];
        }
        return $last_data;
    }



    /**
     * 计算两点间的距离
     *
     * @param    string   $lat_1 | $lat_2    纬度
     * @param    string   $lng_1 | $lng_2    经度
     * @return   string
     */
    public function sum($lat_1, $lng_1, $lat_2, $lng_2)
    {
        // 将角度转为狐度
        //deg2rad()函数将角度转换为弧度
        $radLat1 = deg2rad($lat_1);
        $radLat2 = deg2rad($lat_2);
        $radLng1 = deg2rad($lng_1);
        $radLng2 = deg2rad($lng_2);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000;
        return round(($s / 1000),1) . 'km' ;
    }




    /**
     * 酒店筛选
     *
     * @param    array   $data   酒店数据
     * @param    array   $where  条件
     * @return   array | null
     */
    #TODO:数据量大的时候可以考虑走redis缓存，这里暂时不做redis的读取
    public function hotelSelect($data,$where)
    {
        $sortData = (new Query())
                    ->select("*")
                    ->from("pay_hotel_brand_star as sort")
                    ->where($where)
                    ->all(Yii::$app->db2);
        if(!$sortData) return null;
        $star = [];
        foreach ($sortData as $k => $v)
        {
            $star[] = $v['id'];
        }
        $last_data = [];
        $i = 0;
        $limit = $this->page * 10;
        foreach ($data as $k => $v)
        {
            if(in_array($v['one_sort'],$star))
            {
                if($i <= $limit)
                {
                    $last_data[] = $v;
                }
                ++$i;
            }
        }
        return $last_data;
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
    public function storeAdvert($s_id,$type = 0)
    {
        $data = (new Query())
                ->select("*")
                ->from("pay_store_activity")
                ->where(['x_id' => $s_id,'status' => 1]);
        if($type)
        {
            $data = $data->where(['type' => $type])->all();
        }
        else
        {
            $data = $data->all();
        }
        if(!$data) return '';
        $name = '';
        foreach ($data as $k => $v)
        {
            $name .= $v['activity_title'].',';
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
     * @param   int      $top_sort   顶级分类
     * @param   int      $s_id       商家分类
     * @param   int      $score      商家分数
     * @param   int      $sort_two   二级分类id
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