<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/17
 * Time: 11:04
 */
namespace api\modules\v1\controllers;

use api\modules\v1\actions\CreateAction;
use api\modules\v1\actions\IndexAction;
use yii\web\Controller;

class DemoController extends Controller
{


    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className()
            ],
            'create' => [
                'class' => CreateAction::className()
            ]
        ];
    }


}