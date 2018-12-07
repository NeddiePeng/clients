<?php
/**
 * 异常处理.
 * User: Pengfan
 * Date: 2018/9/26
 * Time: 17:41
 */
namespace api\components;

use yii\web\ErrorHandler;
use Yii;

class Exception extends ErrorHandler
{


    /**
     * 重写渲染异常页面方法
     * @param type $exception
     */
    public function renderException($exception)
    {

        $response = Yii::$app->response;

        $response->format = yii\web\Response::FORMAT_JSON;

        //异常状态码
        if(isset($exception->statusCode))
        {
            $code = $exception->statusCode;
        }
        else
        {
            $code = 500;
        }
        $data = [
            'code' => $code,
            'msg' => $exception->getMessage(),
            'data' => [
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            ]
        ];
        header("Content-type:application/json;charset=utf-8");
        echo json_encode($data);
        die();
    }


}