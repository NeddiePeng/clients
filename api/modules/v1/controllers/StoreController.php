<?php
/**
 * 门店Controller.
 * User: Pengfan
 * Date: 2018/12/10
 * Time: 11:03
 */
namespace api\modules\v1\controllers;

use api\behaviors\TokenBehavior;
use api\modules\Base;
use api\modules\v1\models\Store;
use api\modules\v1\models\StoreActions;
use api\modules\v1\models\StoreOther;

class StoreController extends Base
{


    //model类
    public $modelClass = 'api\modules\v1\models\StoreActions';


    /**
     * 检测是否有权访问
     */
    public function behaviors()
    {
        return [
            'TokenBehavior' => [
                'class' => TokenBehavior::className(),
                'tokenParam' => 'accessToken',
            ],
        ];
    }


    public function actions()
    {
        $actions = parent::actions();
        // 注销系统自带的实现方法
        unset($actions['index']);
        return $actions;
    }



    /**
     * 门店数据
     */
    public function actionIndex()
    {
        $params = $this->params;
        $model = new $this->modelClass(['scenario' => 'store-list']);
        $loadParam = $model->load($params,'');
        if($loadParam && $model->validate())
        {
            $store = $model->getStoreData($params);
            if(!$store) return $this->returnData(0,'数据为空');
            $obj = new StoreOther();
            $data = $obj->getPro($store);
            if(!$data) return $this->returnData(0,'数据为空');
            foreach ($data as $key => $val)
            {
                $data[$key]['distance'] = $this->sum($val['lat'],$val['lng'],$params['lat'],$params['lng']);
            }
            return $this->returnData(200,'获取成功',$data);
        }
        return $this->returnRuleErr($model);

    }




    /**
     * 门店详情
     */
    public function actionStoreDetails()
    {
        $params = $this->params;
        $model = new $this->modelClass(['scenario' => 'storeDetails']);
        $loadParam = $model->load($params,'');
        if($loadParam && $model->validate())
        {
            $store = $model->storeDetails();
            if(!$store) return $this->returnData(0,'数据为空');
            $sortData = Store::instance()->storeSort($store['top_sort'],$store['one_sort']);
            $storeProData = StoreOther::instance()->detailsPro($store['id'],$store['top_sort']);
            $storeAlbum = Store::instance()->getStoreAlbum($store['id']);
            $storeMobile = StoreActions::instance()->storeMobile($store['id']);
            $storeInfo = [
                'sort_list' => $store['store_name'],
                'business_time' =>  '早'.date('H:i',$store['open_start']).'-晚'.date('H:i',$store['open_end']),
                'otherBusiness' => "免费",
                'actively' => []
            ];
            $infoData = [
                's_id' => $store['id'],
                'store_name' => $store['store_name'],
                'score' => $store['score'],
                'sort_name' => $sortData ? $sortData['sort_name'] : "",
                'per_capita' => 100,
                'mobile' => $storeMobile ? $storeMobile[0]['mobile'] : '',
                'do_business_time' => '营业中:'.date('H:i',$store['open_start']).'-'.date('H:i',$store['open_end']),
                'address' => $store['address'],
                'Notice' => $store['Notice'],
                'headerImgData' => [
                    'imgUrl' => $storeAlbum ? $storeAlbum[0]['img_url'] : "",
                    'count' => count($storeAlbum ? $storeAlbum : [])
                ],
                'distance' => $this->sum($params['lat'],$params['lng'],$store['lat'],$store['lng']),
                'lat' => $store['lat'],
                'lng' => $store['lng']
            ];
            $commentScore = [
                'totalScore' => $store['score'],
                'otherScore' => [
                    'flavor' => 0,
                    'service' => 0,
                    'scenario' => 0
                ]
            ];
            $data['infoData'] = $infoData;
            $data['otherInfo'] = $storeInfo;
            $data['commentScore'] = $commentScore;
            $data['proData'] = $storeProData;
            return $this->returnData(200,'获取成功',$data);
        }
        return $this->returnRuleErr($model);
    }



    /**
     * 商家相册
     */
    public function actionStoreImg()
    {
        $params = $this->params;
        $model = new $this->modelClass(['scenario' => 'store-img']);
        $loadParam = $model->load($params,'');
        if($loadParam && $model->validate())
        {
            $data = $model->getImgList();
            if(!$data) return $this->returnData(0,'数据为空');
            $lastData['imgList'] = $data;
            return $this->returnData(200,'获取成功',$lastData);
        }
        return $this->returnRuleErr($model);
    }


}