<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/5
 * Time: 15:04
 */
namespace api\behaviors;

use yii\base\Behavior;
use yii\web\Controller;
use yii\web\Response;
use Yii;

class TokenBehavior extends Behavior
{

    /**
     * @var string the parameter name for passing the access token
     */
    public $tokenParam = 'access-token';



    //让行为响应组件的事件触发
    public function events()
    {
        return [
            //控制器方法执行后触发事件，调用returnData函数
            Controller::EVENT_AFTER_ACTION => 'returnData',
        ];
    }


    //返回数据
    public function returnData()
    {
        YII::$app->response->format = Response::FORMAT_JSON;
    }

    /**
     * 验证accessToken
     */
    public function checkAccessToken()
    {
        header("Content-type:application/json;charset=utf-8");
        $accessToken = Yii::$app->request->headers->get('accessToken');
        if(is_string($accessToken))
        {
            $data = Yii::$app->session->get($accessToken);
            if(!$data)
            {
                echo json_encode([
                    'code' => 401,
                    'msg' => '请求凭证无效',
                    'data' => new \stdClass()
                ]);
                die();
            }
        }
        else
        {
            echo json_encode([
                'code' => 401,
                'msg' => '请求凭证无效',
                'data' => new \stdClass()
            ]);
            die();
        }

    }


}