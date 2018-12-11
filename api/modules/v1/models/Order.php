<?php
/**
 * 订单model.
 * User: Pengfan
 * Date: 2018/12/7
 * Time: 13:54
 */
namespace api\modules\v1\models;

use api\models\User;
use dosamigos\qrcode\QrCode;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

class Order extends ActiveRecord
{

    //其他字段
    public $accessToken;
    public $page = 1;
    public $limit = 5;
    public $nav_type = 'all';


    //数据表
    public static function tableName()
    {
        return 'pay_store_order';
    }


    //验证规则
    public function rules()
    {
        return [
            [['accessToken'],'required','on' => 'new-order'],
            [['order_id'],'required','on' => 'order-details']
        ];
    }


    //字段信息
    public function attributeLabels()
    {
        return [
            'accessToken' => Yii::t("app",'accessToken'),
            'order_id' => Yii::t("app",'order_id')
        ];
    }




    /**
     * 订单消费码
     */
    public function codeData()
    {
        $order_id = $this->order_id;
        $data = (new Query())
                ->select("*")
                ->from("pay_store_consume_code")
                ->where(['order_id' => $order_id])
                ->one();
        return $data;
    }



    /**
     * 订单详情
     *
     * @return   array | null
     */
    public function orderDetailsOne()
    {
        $order_id = $this->order_id;
        $data = (new Query())
                ->select("*")
                ->from(static::tableName().' as order')
                ->leftJoin("pay_store_order_project as pro",'pro.order_id=order.order_id')
                ->leftJoin("pay_store_consume_code as code",'code.object_id=pro.id')
                ->where("order.order_id=$order_id")
                ->one();
        return $data;
    }



    /**
     * 订单信息
     *
     * @param     array   $orderData   订单数据集合
     * @return    array | null
     */
    public function Part($orderData)
    {
        $data = $this->proData($orderData['type'],$orderData['project_id']);
        $status = $this->orderStatus($orderData['is_pay_success'],$orderData['is_refund'],$orderData['is_comment']);
        $proData = Product::unified($data, $orderData['type'], $orderData['number'],$status,$orderData['pay_time'],$orderData['x_id']);
        $storeData = Store::findOne(['id' => $orderData['x_id']]);
        $last_data = [
            'order_id' => $orderData['order_id'],
            'validate_time' => date("Y-m-d H:i:s",strtotime("last day of last month",$orderData['pay_time'])),
            'code' => $orderData['consum_code'],
            'is_use' => $orderData['is_use'],
            'mobile' => $orderData['mobile'],
            'dis_price' => $orderData['dis_price'],
            'last_price' => $orderData['actual_price'],
            'store_name' => $storeData['store_name'],
            'store_mobile' => $storeData['mobile'],
            'address' => $storeData['address'],
            's_id' => $orderData['x_id']
        ];
        $last_data = array_merge($proData,$last_data);
        return $last_data;
    }


    /**
     * 订单状态
     *
     * @param     int    $is_pay_success   支付状态
     * @param     int    $is_refund        退款状态
     * @param     int    $is_comment       评论状态
     * @return    int
     */
    public function orderStatus($is_pay_success,$is_refund,$is_comment)
    {
        if($is_pay_success == 1 && $is_refund == 0)
        {
            switch ($is_refund)
            {
                case 0:
                    if ($is_comment) $status = 1; else $status = 5;
                    continue;
                case 1:
                    $status = 2;
                    continue;
                case 2:
                    $status = 3;
                    continue;
                case 3:
                    $status = 4;
                    continue;
                default:
                    $status = 0;
            }
        }else{
            $status = 0;
        }
        return $status;
    }


    /**
     * 订单数据【美食】
     */
    public function orderList()
    {
        $userData = User::findIdentityByAccessToken($this->accessToken);
        if(!$userData) return null;
        $uid = $userData['id'];
        $param = [
            'nav_type' => $this->nav_type
        ];
        $otherWhere = Yii::$app->where->select('order',$param);
        $order = (new Query())
                 ->select("*")
                 ->from(static::tableName() .' as order')
                 ->leftJoin("pay_store_order_project as pro",'pro.order_id=order.order_id')
                 ->where(['order.uid' => $uid])
                 ->andWhere($otherWhere)
                 ->offset(($this->page - 1) * $this->limit)
                 ->limit($this->limit)
                 ->all();
        if(!$order) return null;
    }



    /**
     * 订单详情
     *
     * @param    array   $order   订单数据
     * @return   array | null
     */
    public function orderDetails($order)
    {
        $last_data = [];
        foreach ($order as $key => $val)
        {
            $data = $this->proData($val['type'],$val['project_id']);
            $status = $this->orderStatus($val['is_pay_success'],$val['is_refund'],$val['is_comment']);
            $proData = Product::unified($data, $val['type'], $val['number'],$status,$val['pay_time'],$val['x_id']);
            if($proData) $last_data[] = $proData;
        }
        return $last_data;
    }




    /**
     * 订单商品
     *
     * @param    int    $type    订单类型
     * @param    int    $p_id    商品id
     * @return   array | null
     */
    public function proData($type,$p_id)
    {
        switch ($type)
        {
            case 1:
                $data = Product::vou($p_id);
                continue;
            case 2:
                $data = Product::group($p_id);
                continue;
            case 3:
                $data = Product::check($p_id);
                continue;
            case 4:
                $data = Product::shopping($p_id);
                continue;
            default:
                $data = null;
        }
        return $data;
    }




    /**
     * 酒店订单数据
     *
     * @return   array | null
     */
    public function hotelOrder()
    {
        $userData = User::findIdentityByAccessToken($this->accessToken);
        if(!$userData) return null;
        $uid = $userData['id'];

    }


}