<?php
/**
 * 阿里云操作.
 * User: Pengfan
 * Date: 2018/12/5
 * Time: 18:29
 */
namespace api\components\alipay;

use api\AliPay\aop\request\AlipayTradeWapPayRequest;
use api\extend\AliPay\aop\AopClient;
use api\extend\AliPay\aop\request\AlipayTradeAppPayRequest;
use api\extend\AliPay\aop\request\AlipayTradeRefundRequest;
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




    /**
     * 统一下单
     * 包含app支付和手机端wap支付【type:app | page】
     *
     * @param    array    $params   支付参数【参数设置严格按照文档】
     * @return   string
     */
    public function alipay($params)
    {
        $aop = new AopClient();
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = $this->config['app_id'];
        $aop->rsaPrivateKey = $this->config['rsaPriKey'];
        $aop->alipayrsaPublicKey = $this->config['pubKey'];
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset='UTF-8';
        $aop->format='json';
        if($params['type'] === 'app')
        {
            $request = new AlipayTradeAppPayRequest();
        }
        else
        {
            $request = new AlipayTradeWapPayRequest();
        }

        $bizContent = "{";
        foreach ($params as $key => $val)
        {
            if($key === 'type')continue;
            $bizContent .= "\"{$key}\":\"{$val}\",";
        }
        $bizContent .= "}";
        $request->setNotifyUrl($this->config['notify_url']);
        $request->setBizContent($bizContent);
        if($params['type'] == 'app')
        {
            $result = $aop->sdkExecute($request);
            return htmlspecialchars($result);
        }
        else
        {
            $result = $aop->pageExecute($request);
            $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
            $resultCode = $result->$responseNode->code;
            if(!empty($resultCode)&&$resultCode == 10000) return true;
            $msg = $request->$responseNode->sub_msg;
            Yii::warning("$resultCode:$msg",'pay');
            return false;
        }


    }



    /**
     * 异步回调
     *
     * @param   array   $params   支付宝异步通知参数
     * @return  array | boolean
     */
    public function notify_url($params)
    {
        $aop = new AopClient();
        $aop->alipayrsaPublicKey = $this->config['pubKey'];
        $flag = $aop->rsaCheckV1($params, NULL, "RSA2");
        if($flag)
        {
            //处理业务，并从$_POST中提取需要的参数内容
            if($params['trade_status'] == 'TRADE_SUCCESS'
                || $params['trade_status'] == 'TRADE_FINISHED'){
                //获取订单号
                $orderId = $params['out_trade_no'];
                //交易号
                $trade_no = $params['trade_no'];
                //订单支付时间
                $gmt_payment = $params['gmt_payment'];
                //转换为时间戳
                $gtime = strtotime($gmt_payment);
                echo "success";
                return true;
            }
        }
        return false;
    }





    /**
     * 退款
     *
     * @param    array    $params    退款参数【严格按照官网文档配置】
     * @return   boolean
     */
    public function refund($params)
    {
        $aop = new AopClient();
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = $this->config['app_id'];
        $aop->rsaPrivateKey = $this->config['rsaPriKey'];
        $aop->alipayrsaPublicKey = $this->config['pubKey'];
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset='UTF-8';
        $aop->format='json';
        $request = new AlipayTradeRefundRequest();
        $bizContent = "{";
        foreach ($params as $key => $val)
        {
            $bizContent .= "\"{$key}\":\"{$val}\",";
        }
        $bizContent .= "}";
        $request->setBizContent($bizContent);
        $result = $aop->execute ( $request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if(!empty($resultCode)&&$resultCode == 10000) return true;
        $msg = $result->$responseNode->sub_msg;
        Yii::warning("$resultCode:$msg",'pay');
        return false;
    }






}