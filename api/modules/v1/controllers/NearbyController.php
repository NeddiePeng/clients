<?php
/**
 * 附近controller.
 * User: Pengfan
 * Date: 2018/12/6
 * Time: 15:10
 */
namespace api\modules\v1\controllers;

use api\behaviors\TokenBehavior;
use api\modules\Base;
use api\modules\v1\models\StoreOther;

class NearbyController extends Base
{


    //model类
    public $modelClass = 'api\modules\v1\models\Store';


    //检查有权限访问
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
     * 附近门店
     */
    public function actionIndex()
    {
        $params = $this->params;
        $model = new $this->modelClass(['scenario' => 'nearby']);
        $loadParam = $model->load($params,'');
        if($loadParam && $model->validate())
        {
            $storeData = $model->nearbyPage();
            if(!$storeData) return $this->returnData(0,'数据为空');
            $obj = new StoreOther();
            $data = $obj->getPro($storeData,'index');
            if(!$data) return $this->returnData(0,'数据为空');
            foreach ($data as $k => $v) {
                $dis = $this->sum($params['lat'],$params['lng'],$v['lat'],$v['lng']);
                $data[$k]['distance'] = $dis;
            }
            return $this->returnData(200,'获取成功',$data);

        }
        return $this->returnRuleErr($model);
    }



}