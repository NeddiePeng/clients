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
            $storeProData = "";
        }
        return $this->returnRuleErr($model);
    }


}