<?php
/**
 * 用户controller.
 * User: Pengfan
 * Date: 2018/12/5
 * Time: 19:27
 */
namespace api\modules\v1\controllers;

use api\behaviors\TokenBehavior;
use api\models\User;
use api\modules\Base;
use Yii;

class UserController extends Base
{

    //model类
    public $modelClass = 'api\modules\v1\models\UserActions';


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
     * 我的个人中心
     */
    public function actionIndex()
    {
        $this->getBehavior('TokenBehavior')->checkAccessToken();
        $accessToken = Yii::$app->request->headers->get('accessToken');
        $params = ['accessToken' => $accessToken];
        $model = new $this->modelClass();
        $loadParam = $model->load($params,'');
        if($loadParam && $model->validate())
        {
            $userData = User::findIdentityByAccessToken($accessToken);
            if(!$userData)return $this->returnData(0,'数据为空');
            return $this->returnData(200,'success',$userData);
        }
        return $this->returnRuleErr($model);
    }



}