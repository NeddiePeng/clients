<?php
/**
 * 订单model.
 * User: Pengfan
 * Date: 2018/12/7
 * Time: 13:54
 */
namespace api\modules\v1\models;

use api\models\User;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

class Order extends ActiveRecord
{

    //其他字段
    public $accessToken;
    public $page = 1;
    public $limit = 5;


    //数据表
    public static function tableName()
    {
        return 'pay_store_order';
    }


    //验证规则
    public function rules()
    {
        return [
            [['accessToken'],'required','on' => 'new-order']
        ];
    }


    //字段信息
    public function attributeLabels()
    {
        return [
            'accessToken' => Yii::t("app",'accessToken')
        ];
    }



    /**
     * 订单数据【美食】
     */
    public function orderList()
    {
        $userData = User::findIdentityByAccessToken($this->accessToken);
        if(!$userData) return null;
        $uid = $userData['id'];
        $param = [];
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
            switch ($val['type'])
            {
                case 1:
                    $data = Product::vou($val['project_id']);
                    continue;
                case 2:
                    $data = Product::group($val['project_id']);
                    continue;
                case 3:
                    $data = Product::check($val['project_id']);
                    continue;
                case 4:
                    $data = Product::shopping($val['project_id']);
                    continue;
                default:
                    $data = null;
            }
            if($val['is_pay_success'] == 1 && $val['is_refund'] == 0)
            {
                switch ($val['is_refund'])
                {
                    case 0:
                        if ($val['is_comment']) $status = 1; else $status = 5;
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
            $proData = Product::unified($data, $val['type'], $val['number'],$status,$val['pay_time'],$val['x_id']);
            if($proData) $last_data[] = $proData;
        }
        return $last_data;
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