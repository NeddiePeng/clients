<?php
namespace api\modules\v1\controllers;

use api\behaviors\TokenBehavior;
use Yii;
use yii\db\Exception;
use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;

class SiteController extends ActiveController
{


    //model类
    public $modelClass = 'api\modules\v1\models\Guide';



    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items'
    ];



    /**
     * 初始化
     */
    public function init()
    {
        parent::init();

        //认证状态不通过session来保持
        Yii::$app->user->enableSession = false;
    }



    public function behaviors()
    {
        /*$behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::className(),
            'tokenParam' => 'accessToken'
        ];
        return $behaviors;*/
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
        unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view']);
        return $actions;
    }




    public function actionIndex()
    {
        $this->getBehavior('TokenBehavior')->checkAccessToken();
        $param = Yii::$app->request->get();
        $modelClass = $this->modelClass;
        $query = $modelClass::find()->where(['id' => $param['id']]);
        return new ActiveDataProvider([
            'query' => $query
        ]);
    }




    public function actionCreate()
    {
        $model = new $this->modelClass();
        $params = Yii::$app->request->post();
        $model->load($params,'');
        if (! $model->save()) {
            var_dump($model->getFirstErrors());
            exit;
        }
        return $model;
    }




    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->attributes = Yii::$app->request->post();
        if (! $model->save()) {
            return array_values($model->getFirstErrors())[0];
        }
        return $model;
    }




    public function actionDelete($id)
    {
        return $this->findModel($id)->delete();
    }



    public function actionView($id)
    {
        return $this->findModel($id);
    }



    protected function findModel($id)
    {
        $modelClass = $this->modelClass;
        if (($model = $modelClass::findOne($id)) !== null) {
            return $model;
        } else {
            throw new Exception('The requested page does not exist.');
        }
    }



    public function checkAccess($action, $model = null, $params = [])
    {
        // 检查用户能否访问 $action 和 $model
        // 访问被拒绝应抛出ForbiddenHttpException
        // var_dump($params);exit;
    }
}