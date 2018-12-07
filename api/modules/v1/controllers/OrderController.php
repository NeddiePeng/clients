<?php
/**
 * 订单controller.
 * User: Pengfan
 * Date: 2018/12/7
 * Time: 11:35
 */
namespace api\modules\v1\controllers;

use api\behaviors\TokenBehavior;
use api\modules\Base;
use Yii;

class OrderController extends Base
{

    //model类
    public $modelClass = 'api\modules\v1\models\Order';


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
     * 订单
     */
    public function actionIndex()
    {
        $this->getBehavior('TokenBehavior')->checkAccessToken();
        $getParams = $this->params;
        $headerParams = Yii::$app->request->headers->get('accessToken');
        $params = array_merge($getParams,$headerParams);
        $model = new $this->modelClass(['scenario' => 'new-order']);
        $loadParam = $model->load($params,'');
        if($loadParam && $model->validate())
        {
            $order = $model->orderList();
            if(!$order) return $this->returnData(0,'暂没有订单数据');
            $last_order = $model->orderDetails($order);
            if(!$last_order) return $this->returnData(0,'暂没有订单数据');
            return $this->returnData(200,'获取成功',$last_order);
        }
        return $this->returnRuleErr($model);
    }



    /**
     * 订单详情
     */
    public function actionOrderDetails()
    {
        $this->getBehavior("TokenBehavior")->checkAccessToken();
        $getParams = $this->params;
        $model = new $this->modelClass(['scenario' => 'order-details']);
        $loadParam = $model->load($getParams,'');
        if($loadParam && $model->validate())
        {
            $order = $model->orderDetailsOne();
            if(!$order) return $this->returnData(0,'获取未知数据');
            $orderPart = $model->Part($order);
            return $this->returnData(200,'获取成功',$orderPart);
        }
        return $this->returnRuleErr($model);
    }



}