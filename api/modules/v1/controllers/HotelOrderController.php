<?php
/**
 * 酒店订单Controller.
 * User: Pengfan
 * Date: 2018/12/21
 * Time: 19:26
 */
namespace api\modules\v1\controllers;

use api\modules\Base;
use api\modules\v1\actions\CreateAction;
use api\modules\v1\models\Order;

class HotelOrderController extends Base
{



    public function actions()
    {
        return [
            'index' => [
                'class' => CreateAction::className(),
                'modelClass' => Order::className(),
                '' => ''
            ]
        ];
    }


}