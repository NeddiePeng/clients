<?php
/**
 * 提交订单.
 * User: Pengfan
 * Date: 2018/12/17
 * Time: 11:49
 */
namespace api\modules\v1\controllers;

use api\modules\Base;
use api\modules\v1\actions\CreateAction;
use api\modules\v1\models\CallBack;
use api\modules\v1\models\Order;

class OrderOtherController extends Base
{


    public function actions()
    {
        return [
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => Order::className(),
                'scenario' => 'create-order',
                'modelActions' => 'createOrder'
            ],
            'order-pay' => [
                'class' => CreateAction::className(),
                'modelClass' => Order::className(),
                'scenario' => 'order-pay',
                'modelActions' => 'orderPay'
            ],
            'order-return' => [
                'class' => CreateAction::className(),
                'modelClass' => Order::className(),
                'scenario' => 'order-return',
                'modelActions' => 'orderReturn'
            ],
            'asyn-callback' => [
                'class' => CreateAction::className(),
                'modelClass' => Order::className(),
                'modelActions' => 'callBack'
            ],
            'order-query' => [
                'class' => CreateAction::className(),
                'modelClass' => CallBack::className(),
                'modelActions' => 'orderQuery'
            ]
        ];
    }


}