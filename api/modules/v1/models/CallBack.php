<?php
/**
 * 异步回掉.
 * User: Pengfan
 * Date: 2018/12/18
 * Time: 10:30
 */
namespace api\modules\v1\models;

use Yii;
use yii\db\ActiveRecord;

class CallBack extends ActiveRecord
{

    public $order_id;


    //验证规则
    public function rules()
    {
        return [
            [['order_id'],'required','on' => 'order-query']
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => Yii::t('app','order_id')
        ];
    }


    /**
     * 订单查询
     *
     * @param   boolean | string
     */
    public function orderQuery()
    {
        $params = ['order_id' => $this->order_id];
        $jsonData = Yii::$app->wx->orderQuery($params);
        if(!$jsonData) return false;
        if($jsonData['return_code'] === 'SUCCESS') return true;
        return $jsonData['return_msg'];
    }


    /**
     * 微信支付回调
     */
    public function callBack()
    {
        Yii::$app->wx->notify_url();
    }



    /**
     * 更新订单
     *
     * @param    array   $params   订单参数
     * @return   boolean
     */
    public function updateOrder($params)
    {
        $order_id = $params['out_trade_no'];
        $update_data = [
            'pay_time' => time(),
            'is_pay_success' => 1
        ];
        $res = Yii::$app->db->createCommand()
               ->update("pay_store_order",$update_data,['order_id' => $order_id])
               ->execute();
        return $res;
    }




}