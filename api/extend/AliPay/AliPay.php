<?php
/**
 * 支付宝支付.
 * User: Pengfan
 * Date: 2018/8/21
 * Time: 10:45
 */
header("Content-type: text/html; charset=utf-8");
require_once 'model/builder/AlipayTradePrecreateContentBuilder.php';
require_once 'aop/service/AlipayTradeService.php';
require_once 'aop/service/AlipayTradeServiceWap.php';
require_once 'model/builder/AlipayTradeWapPayContentBuilder.php';
class AliPay
{



    //支付宝服务商签约PID
    private $providerId = "";
    //支付宝配置
    private $config = array (
        //签名方式,默认为RSA2(RSA2048)
        'sign_type' => "RSA2",

        //支付宝公钥
        'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEApIZIAQsDT+lw7oRq/Tdg/9kOx7PYsXxdo5hFDj0dF1fs04F+Kv3u9U0wlOyACyDnpSk5x+UG/Im+8NoZA82wiEGv2L2H/PnFONDud+4zMa1gvtg/6I1pIMRBE1L+idcjvT99MTidpLa0xI7pWmuxeItETllzRIBwd2GYyZ4SkLHVi49dfgM7CGDwwmWQCHgNF76LYAi8sitkrh0CtO9fAcf1mlKo2EJPi9X1Jm6/0/VaMs42msHpCNBdAIzrqdKRll9jQoFx1LwssStJBTe5K+3bJA2IMuQy+GhP8gbbCIb4DtPzZNpdr1+NVuM6SLqzf/joRXWA5ZSY19hAXcZrBQIDAQAB",

        //商户私钥
        'merchant_private_key' => "MIIEogIBAAKCAQEAt89PExW4oVvnJG8DPlMsQwgEpPSpQlFdh528mKN6RkTZAapkVWgMmMPgrBoF/RecmJ0x/kWbNZQhrqgK7Fw+zwHEY14F62ZXspSzo+BijPuwdpQuRbQCT2Nobhm4tK7ZX5bVXBk/UaI9NAtLDNl62Y5rwl5r0eaq4DVKc18S1C7z6OKBJBOMZVEw/vUDIoaBJt9TUtTM0Urv4qj3wLrCPDhh3VWrwVmJ2dNKiPnsGGNA0Hupi4cIKxGqVm/pyVGB0H0VHN3aGMEUw4XfF781fjpzdPeZjPJFtzBaAdC2O+KIn1vMrLolbEpxu2E1ig9syyjuNA44NVP9il4b538jXwIDAQABAoIBAH2S1p3eOA2cwLPGV7vrjJCa2LltIHlbJv+whpjtDmsVPAAETZl/hSOUplhNSwwWZnho5C+nlBqtgblVumixuIMp3OZZ5MdmWsF5D6UEda+Ff4/zOg2Kpg1gh4a4cdSWo5DHdin+YaC+qvt0P6iep2wb/YiDgzuaT+Du51cce7uS0b4dceu8W8veGnaI5M9DFQxjq30wR6r9kIDbG/fpeLZ0TDHLxpuANm+5/EIDtY8q/qATdWOl5vbjD4pmP4PbNLkpgY8vVL8CFj52z/0lYjP+WiEBlNQX98ZZpsqA+aZ13zHI+xRJ8e+yhqd3RlXxcz9dkb72oIw7+Dz6aB7V0ikCgYEA9CJX3/Gxh0MeLPyDBhR4h6vbKmewM+7qdegHDReLQHqVT2UAPiy/RCWF/KKip/5tiv68HKFNrFJGp9rOLCv47x6txmVztMRS1M5kmG8Hb0LIjcP6thMlCVKdjvE+XiKgaCzq/FHNw/iraDHz77/5DsWN0Z2FHKrXQ/NTpJV6zCUCgYEAwL5gIqI/q6wz+W8OqHi7HfrWduRc8MgLneaw9gQkBx4ATUDmhNawbz/rvKzTj32K2NWVmMdTuy4G708KWNm3JBgpXntlb09Bz2gUNVoKLSHPJCw05QtXjecs5wYHjRCzm8lDcFGQNC5BvTAgsjge4UAoEf1yZ79oVqr2p1HBGDMCgYByEyYah1YbzRnpjWgvzBrx0jTLoL2t1qKJy4yX6ntv+peQDLLLWp9Y2Wu9O8VjWDiZbSQ7AIhJz/wh7NTPwRBFs4EhpkAlpGLL+1D4BVFlBMCvtXaN42435/mlVEZ/OBDZ/LskgZjzTFvTiRvh2EMpSthUrRUI6y9BGg7oZcyGXQKBgFXD4K9IlyBizfXODy20Gz8p4MiisSCLQ3ANuOyfxxBLr0KxAGJXzcaTIOih1rng2SnHUHvdJksCHh/agfYrWqz6+12JdwdisxwBagybdi/C/ZNRAHBy7ZC9L2PVcQK6TdGiaxnNkWdGtgXjJolnI4aDr9DhgEjeCSWXiY3GeS+1AoGAZSql0HqOcL7NyMiITl3FbXeTAnjjiivFbNpdKXMmSI1raT9w6MMqPSf0rzoObifsYMvB1CUgwTBwqaQ0ylSSo+/sWLN9AGpyN/YWcILqYW5QlTmXLfH4glz4Lm9FhZGkZZ+M5XIPYaKD2iTGrh+P5W7KSywnGjXTUrnWP1UodsI=",
        //编码格式
        'charset' => "UTF-8",
        //支付宝网关
        'gatewayUrl' => "https://openapi.alipay.com/gateway.do",
        //应用ID
        'app_id' => "2018060260310553",
        //异步通知地址,只有扫码支付预下单可用
        'notify_url' => "https://www.baidu.com",
        //最大查询重试次数
        'MaxQueryRetry' => "10",
        //查询间隔
        'QueryDuration' => "3",
        //同步跳转
		'return_url' => "http://store.qmwjj.com/payment/pay.api",
    );


    /**
     * 支付宝扫码支付
     */
    public function preCreate(){
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            //微信echo '微信';exit;
        } else if(strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false){
            //echo '支付宝浏览';echo '支付宝';exit;}else{echo "不是微信或者支付宝";exit;
        }else{
            echo "什么都不是";
        }
    }







    /**
     * 支付宝刷卡支付
     * @param      string    $outTradeNo      订单号码
     * @param      string    $price           订单金额
     * @param      string    $authCode        授权码
     * @return     array
     */
    public function swingCardPay($outTradeNo,$price,$authCode){
        $subject = "减付宝买单支付";
        $totalAmount = $price;
        $body = "减付宝买单支付";
        $extendParams = new ExtendParams();
        $extendParamsArr = $extendParams->getExtendParams();
        $timeExpress = "5m";
        $barPayRequestBuilder = new AlipayTradePayContentBuilder();
        $barPayRequestBuilder->setOutTradeNo($outTradeNo);
        $barPayRequestBuilder->setTotalAmount($totalAmount);
        $barPayRequestBuilder->setAuthCode($authCode);
        $barPayRequestBuilder->setTimeExpress($timeExpress);
        $barPayRequestBuilder->setSubject($subject);
        $barPayRequestBuilder->setBody($body);
        $barPayRequestBuilder->setExtendParams($extendParamsArr);
        $barPay = new AlipayTradeService($this->config);
        $barPayResult = $barPay->barPay($barPayRequestBuilder);
        switch ($barPayResult->getTradeStatus()) {
            case "SUCCESS":
                $data = $barPayResult->getResponse();
                break;
            case "FAILED":
                $data = $barPayResult->getResponse();
                break;
            case "UNKNOWN":
                $data = $barPayResult->getResponse();
                break;
            default:
                $data = ['msg' => "交易异常"];
        }
        return $data;
    }






    /**
     * 支付宝退款
     * @param    string    $out_trade_no    订单号
     * @param    string    $refund_amount   退款金额
     * @param    string    $out_request_no  标识一次退款请求
     * @return   array
     */
    public function refund($out_trade_no,$refund_amount,$out_request_no){
        $refundRequestBuilder = new AlipayTradeRefundContentBuilder();
        $refundRequestBuilder->setOutTradeNo($out_trade_no);
        $refundRequestBuilder->setRefundAmount($refund_amount);
        $refundRequestBuilder->setOutRequestNo($out_request_no);
        $refundResponse = new AlipayTradeService($this->config);
        $refundResult =	$refundResponse->refund($refundRequestBuilder);
        switch ($refundResult->getTradeStatus()){
            case "SUCCESS":
                $data = $refundResult->getResponse();
                break;
            case "FAILED":
                $data = $refundResult->getResponse();
                break;
            case "UNKNOWN":
                $data = $refundResult->getResponse();
                break;
            default:
                $data = ['' => -1,'msg' => "交易异常"];
        }
        return $data;
    }




    /**
     * 支付宝h5支付
     * @param     string    $out_trade_no    订单号码
     * @param     string    $total_amount    支付金额
     * @return    array
     */
    public function Pay($out_trade_no,$total_amount){
        $subject = "减付宝买单";
        $body = "减付宝买单";
        $timeout_express="1m";
        $payRequestBuilder = new AlipayTradeWapPayContentBuilder();
        $payRequestBuilder->setBody($body);
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setOutTradeNo($out_trade_no);
        $payRequestBuilder->setTotalAmount($total_amount);
        $payRequestBuilder->setTimeExpress($timeout_express);
        $payResponse = new AlipayTradeServiceWap($this->config);
        $result = $payResponse->wapPay($payRequestBuilder,$this->config['return_url'],$this->config['notify_url']);
        return $result;
    }




}