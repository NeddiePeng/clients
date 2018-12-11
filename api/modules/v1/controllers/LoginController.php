<?php
/**
 * 授权登陆.
 * User: Pengfan
 * Date: 2018/12/4
 * Time: 10:59
 */
namespace api\modules\v1\controllers;

use api\models\LoginForm;
use api\models\User;
use Yii;
use yii\web\Controller;

class LoginController extends Controller
{


    //model类
    public $modelClass = 'api\models\User';


    public $enableCsrfValidation = false;



    /**
     * 手机号登录
     *
     * @return  object
     */
    public function actionCreate()
    {
        $model = new LoginForm(['scenario' => 'login']);
        $param = Yii::$app->request->post();
        $loadParam = $model->load($param,'');
        if ($loadParam && $model->validate())
        {
            $loginRes = $model->login();
            if($loginRes)
            {
                $data['accessToken'] = LoginForm::$accessToken;
                return $this->returnData(200,'登录成功',$data);
            }
            else
            {
                $register = $model->register();
                if($register)
                {
                    $data['accessToken'] = LoginForm::$accessToken;
                    return $this->returnData(200,'登录成功',$data);
                }
                else
                {
                    return $this->returnData(400,'登录失败');
                }
            }
        }
        else
        {
            return $this->returnRuleErr($model);
        }
    }



    /**
     * 三方登录获取信息
     */
    public function actionOtherLogin()
    {
        $result = Yii::$app->weChat->getAccessToken('csacwevcewve');
        $result = json_decode($result,true);
        if($result && !isset($result['errcode']))
        {
            $access_token = $result['access_token'];
            $openId = $result['openid'];
            $userInfo = Yii::$app->weChat->getUserInfo($access_token,$openId);
            $userInfo = json_decode($userInfo,true);
            if($userInfo && !isset($userInfo['errcode']))
            {
                //信息入库
                $enterInfo = $this->UserEnterDb($userInfo);
                if($enterInfo)
                {
                    return $this->returnData(200,'授权成功',$enterInfo);
                }
                return $this->returnData(400,'授权失败');
            }
            else
            {
                //记录日志
                Yii::warning($result['errcode'].':'.$result['errmsg'],'login');
                return $this->returnData(400,'授权失败');
            }
        }
        else
        {
            //记录日志
            Yii::warning($result['errcode'].':'.$result['errmsg'],'login');
            return $this->returnData(400,'授权失败');
        }
    }




    /**
     * 微信信息入库
     *
     * @param     array     $InfoData   用户信息
     * @return    boolean | array
     */
    public function UserEnterDb($InfoData)
    {
        $enter = LoginForm::enterOtherInfo($InfoData);
        if($enter)
        {
            return [
                'accessToken' => $enter['accessToken'],
                'is_bind_mobile' => $enter['is_bind']
            ];
        }
        return false;
    }



    /**
     * 绑定手机号
     */
    public function actionBindMobile()
    {
        $postParams = Yii::$app->request->post();
        $headers = Yii::$app->request->headers;
        $accessToken =  $headers->get('accessToken');
        $params = array_merge($postParams,['accessToken' => $accessToken]);
        $model = new LoginForm(['scenario' => 'bind-mobile']);
        $loadParam = $model->load($params,'');
        if($model->validate() && $loadParam)
        {
            $bind = LoginForm::bindMobile($params);
            if(!$bind)
            {
                return $this->returnData(200,'绑定成功');
            }
            else
            {
                return $this->returnData(400,'绑定失败');
            }
        }
        else
        {
            return $this->returnRuleErr($model);
        }
    }







    /**
     * 处理字段验证错误信息
     *
     * @param      object      $model   model对象
     * @return     object
     */
    public function returnRuleErr($model)
    {
        $err = $model->getFirstErrors();
        $erInfo = '';
        if($err)
        {
            foreach ($err as $val)
            {
                $erInfo = $val;
            }
        }
        return $this->returnData(400,$erInfo);
    }



    /**
     * 返回信息
     *
     * @param    int      $code   状态
     * @param    string   $msg    返回信息
     * @param    array    $data   返回数据
     * @return   object
     */
    public function returnData($code = 200, $msg = '', $data = null)
    {
        $response = Yii::$app->response;
        $response->format = yii\web\Response::FORMAT_JSON;
        if(!$data) $data = new \stdClass();
        $last['code'] = $code;
        $last['msg'] = $msg;
        $last['data'] = $data;
        return $last;
    }



    /**
     * 刷新accessToken
     */
    public function actionRefresh()
    {
        $accessToken = Yii::$app->request->headers->get('accessToken');
        if(!$accessToken) return $this->returnData(400,'凭证错误');
        $user = User::findIdentityByAccessToken($accessToken);
        if(!$user) return $this->returnData(400,'凭证错误');
        (new User())->apiToken();
        $accessToken = LoginForm::$accessToken;
        $data['accessToken'] = $accessToken;
        Yii::$app->cache->set($accessToken,$user,7200);
        Yii::$app->db->createCommand()
        ->update("pay_user",['accessToken' => $accessToken],['id' => $user['id']])
        ->execute();
        return $this->returnData(200,'刷新成功',$data);
    }


}