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

        }
        return $this->returnRuleErr($model);

    }


}