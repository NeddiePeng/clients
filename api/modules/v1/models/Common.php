<?php
/**
 * 共用model.
 * User: Pengfan
 * Date: 2018/12/5
 * Time: 16:57
 */
namespace api\modules\v1\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

class Common extends ActiveRecord
{

    //其他字段
    public $s_id;
    public $adCode;
    public $top_sort;

    //数据表
    public static function tableName()
    {
        return 'master_advert';
    }


    //验证规则
    public function rules()
    {
        return [
            [['mask'],'required','on' => 'advert'],
            [['s_id','type'],'required','on' => 'like-share'],
            [['adCode','top_sort'],'required','on' => 'business']
        ];
    }


    //字段名称
    public function attributeLabels()
    {
        return [
            'mask' => Yii::t('app','mask'),
            's_id' => Yii::t('app','s_id'),
            'type' => Yii::t('app','type')
        ];
    }



    /**
     * 分类
     *
     * @return   array | null
     */
    public function sortData()
    {
        $data = (new Query())
                ->select("id,sort_name")
                ->from("pay_store_sort")
                ->where(['parent_id' => $this->top_sort])
                ->andWhere(['status' => 1])
                ->all();
        return $data;
    }



    /**
     * 商圈
     */
    public function businessData()
    {
        $addressData = file_get_contents('./address.json');
        $addressData = json_decode($addressData,true);
        $addressData = $addressData['data']['regionData'];
        $areaData = [];
        foreach ($addressData as $k => $v)
        {
            if(!$v['cityData']) continue;
            foreach ($v['cityData'] as $key => $val)
            {
                if(isset($val['adCode']) && $val['adCode'] == $this->adCode)
                {
                    $areaData = $val['countyData'];
                }
            }
        }
        $areaData = $this->areaNextData($areaData);
        return $areaData;
    }



    /**
     * 附近数据
     *
     * @param    array   $areaData   市区数据
     * @return   array | null
     */
    public function areaNextData($areaData)
    {
        $api = 'https://restapi.amap.com/v3/geocode/regeo?';
        $key = "4708669116e5304cb7ec6cec5ff4212b";
        if($areaData)
        {
            foreach ($areaData as $k => $v)
            {
                $location = $v['center'];
                $radius = 3000;
                $url = $api."location=$location&key=$key&radius=$radius&extensions=all&roadlevel=1&batch=true";
                $data = file_get_contents($url);
                $data = json_decode($data,true);
                if($data && $data['status'] == 1) $areaData[$k]['businessData'] = $data['regeocodes'][0]['addressComponent']['businessAreas'];
            }
        }
        return $areaData;
    }



    /**
     * 优化数据
     *
     * @param    array    $data   数据集合
     * @return   array | null
     */
    public function optimize($data)
    {
        $store = new Store(['scenario' => 'store']);
        if($data)
        {
            foreach ($data as $k => $v)
            {
                $last_data = [];
                if($v['businessData']) {
                    foreach ($v['businessData'] as $key => $val) {
                        $location = explode(',', $val['location']);
                        $store->load(['lat' => end($location), 'lng' => $location[0]], '');
                        $storeData = $store->nearbyPraise();
                        $count = $storeData ? count($storeData) : 0;
                        if($val)
                        {
                            $last_data[] = [
                                'areaName' => $val['name'],
                                'lng' => $location[0],
                                'lat' => $location[1],
                                'center' => $val['location'],
                                'count' => $count,
                                'id' => 0
                            ];
                        }
                    }
                }
                $data[$k]['businessData'] = $last_data;
            }
        }
        return $data;
    }


    /**
     * 获取advert数据
     *
     * @return   array | null
     */
    public function findAdvert()
    {
        $mask = $this->mask;
        $data = (new Query())
                ->select("id,img_url,jump,type")
                ->from(self::tableName())
                ->where(['mask' => $mask,'status' => 1])
                ->all();
        return $data ? $data : null;
    }


}