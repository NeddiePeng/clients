<?php
/**
 * 所有程序基类.
 * User: Pengfan
 * Date: 2018/12/4
 * Time: 13:34
 */
namespace api\modules;

use Yii;
use yii\rest\ActiveController;

class Base extends ActiveController
{

    //参数集合
    public $params;

    //禁止加载公共头
    public $layout = false;

    public $modelClass = '';

    //restful 风格
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
        $postParam = Yii::$app->request->post();
        if($postParam)
        {
            $this->params = $postParam;
        }
        else
        {
            $this->params = Yii::$app->request->get();
        }
        //认证状态不通过session来保持
        Yii::$app->user->enableSession = false;
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
        if(!empty($err))
        {
            foreach ($err as $k => $v)
            {
                $erInfo = $k.':'.$v;
            }
        }
        else
        {
            $erInfo = '未知错误信息';
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
        if(!$data)
        {
            $data = new \stdClass();
        }
        $last['code'] = $code;
        $last['msg'] = $msg;
        $last['data'] = $data;
        return $last;

    }




    /**
     * 计算两点间的距离
     *
     * @param    string   $lat_1 | $lat_2    纬度
     * @param    string   $lng_1 | $lng_2    经度
     * @return   string
     */
    public function sum($lat_1, $lng_1, $lat_2, $lng_2)
    {
        // 将角度转为狐度
        //deg2rad()函数将角度转换为弧度
        $radLat1 = deg2rad($lat_1);
        $radLat2 = deg2rad($lat_2);
        $radLng1 = deg2rad($lng_1);
        $radLng2 = deg2rad($lng_2);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000;
        return round(($s / 1000),1) . 'km' ;
    }



    /**
     * 根据经纬度和半径计算出范围
     * @param string $lat 纬度
     * @param String $lng 经度
     * @param float $radius 半径
     * @return array 范围数组
     */
    public static function calcScope($lat, $lng, $radius = 1000)
    {
        $degree = (24901*1609)/360.0;
        $dpmLat = 1/$degree;

        $radiusLat = $dpmLat*$radius;
        $minLat = $lat - $radiusLat;    // 最小纬度
        $maxLat = $lat + $radiusLat;    // 最大纬度

        $mpdLng = $degree * cos($lat * (M_PI/180));
        $dpmLng = 1 / $mpdLng;
        $radiusLng = $dpmLng * $radius;
        $minLng = $lng - $radiusLng;   // 最小经度
        $maxLng = $lng + $radiusLng;   // 最大经度

        /** 返回范围数组 */
        $scope = array(
            'minLat'  => $minLat,
            'maxLat'  => $maxLat,
            'minLng'  => $minLng,
            'maxLng'  => $maxLng
        );
        return $scope;
    }



    /**
     * 二维数组排序
     *
     * @param     array    $array   需要排序的数组
     * @param     string   $key     数组字段
     * @return    array
     */
    public function array2dSort($array, $key)
    {
        foreach ($array as $key => $val)
        {

        }
    }





}