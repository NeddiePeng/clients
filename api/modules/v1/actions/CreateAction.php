<?php
/**
 * Created by PhpStorm.
 * User: Pengfan
 * Date: 2018/12/17
 * Time: 11:15
 */
namespace api\modules\v1\actions;

use Yii;
use yii\base\Action;
use Closure;
use yii\web\Response;

class CreateAction extends Action
{

    /**@var array model类**/
    public $modelClass;

    /** @var string 场景值**/
    public $scenario = 'default';

    /** @var array 向试图传值 **/
    public $data = [];

    /** @var string 模板路径，默认为action id  */
    public $viewFile = null;

    /**
     * @var string|array 编辑成功后跳转地址,此参数直接传给yii::$app->controller->redirect()
     */
    public $successRedirect = ['index'];

    /**
     * @var string model执行方法
     */
    public $modelActions = "index";

    /**
     * @var string ajax请求返回数据格式
     */
    public $ajaxResponseFormat = Response::FORMAT_JSON;



    public function run()
    {
        /* @var $model \yii\db\ActiveRecord */
        $model = new $this->modelClass;
        $model->setScenario( $this->scenario );
        if(Yii::$app->getRequest()->getIsPost())
        {
            Yii::$app->getResponse()->format = $this->ajaxResponseFormat;
            $params = Yii::$app->request->post();
            $accessToken = Yii::$app->request->headers->get('accessToken');
            if($accessToken) {
                $params['accessToken'] = $accessToken;
            }
            if(empty($params)) $params['time'] = time();
            if($model->load($params,"") && $model->validate()) {
                /* 执行对应的方法 */
                $modelActions = $this->modelActions;
                $handle = $model->$modelActions();
                if(!$handle) return ['code' => 0,'msg' => 'fail','data' => new \stdClass()];
                return ['code' => 200,'msg' => 'success','data' => $handle];
            } else {
                $errorReasons = $model->getErrors();
                $err = '';
                if(!$errorReasons) return ['code' => 400,'msg' => '未知错误信息','data' => new \stdClass()];;
                foreach ($errorReasons as $errorReason) {
                    $err .= $errorReason[0] . '<br>';
                }
                $err = rtrim($err, '<br>');
                return ['code' => 400,'msg' => $err,'data' => new \stdClass()];
            }

        }
        $model->loadDefaultValues();
        $data = [
            'model' => $model,
        ];
        if( is_array($this->data) ){
            $data = array_merge($data, $this->data);
        }elseif ($this->data instanceof Closure){
            $data = call_user_func_array($this->data, [$model, $this]);
        }
        $this->viewFile === null && $this->viewFile = $this->id;
        return $this->controller->render($this->viewFile, $data);
    }



}