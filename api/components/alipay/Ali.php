<?php
/**
 * 阿里云操作.
 * User: Pengfan
 * Date: 2018/12/5
 * Time: 18:29
 */
namespace api\components\alipay;

use Yii;
use api\extend\sendSms\top\request\AlibabaAliqinFcSmsNumSendRequest;
use api\extend\sendSms\top\TopClient;
use yii\base\Component;

class Ali extends Component
{

    //配置
    private $config;


    /**
     * 初始化
     */
    public function init()
    {
        $config = require 'config.php';
        $this->config = $config;
    }


    /**
     * 获取短信
     *
     * @param    string    $mobile    手机号
     * @return   object | boolean
     */
    public function getSmsCode($mobile)
    {
        if(empty($mobile)) return false;
        $code_str = mt_rand(100000,999999);
        $redis = new \Redis();
        $redis->connect("127.0.0.1",'6379');
        $redis->set("mobileCode",$code_str,60);
        $c = new TopClient();
        $c->appkey = $this->config['appKey'];
        $c->secretKey = $this->config['access'];
        $req = new AlibabaAliqinFcSmsNumSendRequest();
        $req->setSmsType("normal");
        $req->setSmsFreeSignName("登录验证");
        $req->setSmsParam("{code:'$code_str'}");
        $req->setRecNum($mobile);
        $req->setSmsTemplateCode("SMS_135525174");
        $result = $c->execute($req);
        $result = json_decode(json_encode($result),true);
        if(isset($result['code'])){
            Yii::error($result['sub_msg']);
            return $result['sub_msg'];
        }
        if($result['result']['err_code'] != "0"){
            return "发送失败";
        }
        return true;
    }

}