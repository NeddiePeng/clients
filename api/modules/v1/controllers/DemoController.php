<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/17
 * Time: 11:04
 */
namespace api\modules\v1\controllers;

use api\modules\Base;
use api\modules\v1\actions\CreateAction;
use api\modules\v1\models\Demo;

class DemoController extends Base
{


    public function actions()
    {
        return [
            'demo' => [
                'class' => CreateAction::className(),
                'modelClass' => Demo::className(),
                'modelActions' => 'index'
            ],
            'create' => [
                'class' => CreateAction::className()
            ]
        ];
    }


}