<?php
/**
 * 门店数据model.
 * User: Pengfan
 * Date: 2018/12/10
 * Time: 11:13
 */
namespace api\modules\v1\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

class StoreActions extends ActiveRecord
{

    //其他字段
    public $lat;
    public $lng;
    public $accessToken;
    public $page = 1;
    public $sort_two;
    public $s_id;
    public $img_type;

    //数据表
    public static function tableName()
    {
        return 'pay_store_info';
    }


    //验证规则
    public function rules()
    {
        return [
            [['lat','lng','page','top_sort'],'required','on' => 'store-list'],
            [['s_id','lng','lat'],'required','on' => 'storeDetails'],
            [['s_id','img_type'],'required','on' => 'store-img']
        ];
    }


    //字段信息
    public function attributeLabels()
    {
        return [
            'lat' => Yii::t('app','lat'),
            'lng' => Yii::t('app','lng'),
            'page' => Yii::t('app','page'),
            'top_sort' => Yii::t('app','top_sort'),
            's_id' => Yii::t('app','s_id')
        ];
    }



    /**
     * 门店活动
     *
     * @param   int    $s_id   门店id
     * @return  array | null
     */
    public static function activilyData($s_id)
    {
        return [
            [
                'type' => 'full',
                'text' => Store::instance()->storeAdvert($s_id)
            ],
            [
                'type' => 'full',
                'text' => Store::instance()->storeAdvert($s_id,3)
            ]
        ];
    }




    /**
     * 门店相册数据
     */
    public function getImgList()
    {
        switch ($this->img_type)
        {
            case 'all':
                $dataList = [];
                $data = $this->getStoreAlbum($this->s_id,'all');
                if ($data){
                    foreach ($data as $key => $val)
                    {
                        $dataList[] = $val['img_url'];
                    }
                }
                $licenseData = $this->licenseData($this->s_id);
                if($licenseData)
                {
                    foreach ($licenseData as $k => $v)
                    {
                        $dataList[] = $v['card_just_img'];
                        $dataList[] = $v['card_back_img'];
                    }
                }
                break;
            case 'pro':
                $dataList = null;
                $proData = Product::dishesData($this->s_id);
                if($proData)
                {
                    foreach ($proData as $k => $v)
                    {
                        $dataList[] = $v['img_url'];
                    }
                }
                break;
            case 'scen':
                $dataList = null;
                $data = $this->getStoreAlbum($this->s_id,2);
                if ($data){
                    foreach ($data as $key => $val)
                    {
                        $dataList[] = $val['img_url'];
                    }
                }
                break;
            case 'license':
                $dataList = null;
                $licenseData = $this->licenseData($this->s_id);
                if($licenseData)
                {
                    foreach ($licenseData as $k => $v)
                    {
                        $dataList[] = $v['card_just_img'];
                        $dataList[] = $v['card_back_img'];
                    }
                }
                break;
            default:
                $dataList = null;

        }
        return $dataList;
    }



    /**
     * 资质数据
     *
     * @param   int    $s_id    门店id
     * @return  array | null
     */
    public function licenseData($s_id)
    {
        $data = (new Query())
                ->select("*")
                ->from("pay_store_license")
                ->where(['x_id' => $s_id])
                ->all();
        return $data ? $data : [];
    }



    /**
     * 获取各分类商家数据
     *
     * @param     array   $params   参数集合
     * @return    array | null
     */
    public function getStoreData($params)
    {
        $where = ['and'];
        $sortWhere = Yii::$app->where->select('storeSort',$params);
        $addrWhere = Yii::$app->where->select('storeAddr',$params);
        $otherWhere = Yii::$app->where->select("storeOther",$params);
        $where = array_merge($where,$sortWhere,$addrWhere,$otherWhere);

        $per = ($this->page - 1) * 10;
        $store = (new Query())
                 ->select("*")
                 ->from(static::tableName())
                 ->where($where)
                 ->offset($per)
                 ->limit(10)
                 ->all();
        return $store;
    }



    /**
     * 门店详情
     *
     * @return   array | null
     */
    public function storeDetails()
    {
        $data = (new Query())
                ->select("*")
                ->from(static::tableName())
                ->where(['id' => $this->s_id])
                ->one();
        return $data;
    }



    /**
     * 门店手机号
     *
     * @param    int     $s_id    门店id
     * @return   array | null
     */
    public function storeMobile($s_id)
    {
        $data = (new Query())
                ->select("*")
                ->from("pay_store_mobile")
                ->where(['x_id' => $s_id])
                ->all();
        return $data;
    }



    /**
     * 分类门店数
     *
     * @param    array    $sortData   分类数据
     * @return   array | null
     */
    public function sortStore($sortData)
    {
        if($sortData)
        {
            foreach ($sortData as $k => $v)
            {
                $store = (new Query())
                         ->select("*")
                         ->from(static::tableName())
                         ->where(['one_sort' => $v['id']])
                         ->count('id');
                $sortData[$k]['count'] = $store;
            }
        }
        return $sortData;
    }



    /**
     * 门店图册
     *
     * @param   int    $s_id   门店id
     * @param   int    $type   相册类型
     * @return  array | null
     */
    public function getStoreAlbum($s_id, $type)
    {
        $where = [
            'x_id' => $s_id,
            'type' => $type
        ];
        if($type == 'all')
        {
            $where = [
                'x_id' => $s_id
            ];
        }
        $data = (new Query())
            ->select("img_url")
            ->from("pay_store_album")
            ->where($where)->all();
        return $data ? $data : [];
    }


}