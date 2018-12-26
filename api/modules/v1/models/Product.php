<?php
/**
 * 商品model.
 * User: Pengfan
 * Date: 2018/12/7
 * Time: 14:17
 */
namespace api\modules\v1\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

class Product extends ActiveRecord
{



    /**
     * 酒店房间数据
     *
     * @param   int   $r_id   房间id
     * @return  array | null
     */
    public static function roomData($r_id)
    {
        $data = (new Query())
                ->select("*")
                ->from("pay_hotel_room_spec")
                ->where(['id' => $r_id])
                ->one(Yii::$app->db2);
        return $data;
    }



    /**
     * 所有单品
     *
     * @param    int    $s_id   门店id
     * @return   array | null
     */
    public static function dishesData($s_id)
    {
        $data = (new Query())
                ->select("*")
                ->from("pay_store_dishes")
                ->where(['x_id' => $s_id])
                ->andWhere(['status' => 1])
                ->all();
        return $data;
    }




    /**
     * 代金券数据
     *
     * @param    int    $p_id    产品id
     * @return   array | null
     */
    public static function vou($p_id)
    {
        $data = (new Query())
                ->select("*")
                ->from("pay_store_vouchers")
                ->where(['id' => $p_id])
                ->one();
        return $data;
    }




    /**
     * 团购数据
     *
     * @param    int    $p_id    产品id
     * @return   array | null
     */
    public static function group($p_id)
    {
        $data = (new Query())
                ->select("*")
                ->from("pay_store_group")
                ->where(['id' => $p_id])
                ->one();
        return $data;
    }



    /**
     * 团购内容商品图
     *
     * @param    string    $p_id    商品id
     * @return   array | null
     */
    public static function groupContent($p_id)
    {
        $data = (new Query())
                ->select("*")
                ->from("pay_store_group_content as content")
                ->leftJoin("pay_store_dishes as dishes",'dishes.id=content.p_id')
                ->where(['content.g_id' => $p_id])
                ->all();
        return $data;
    }




    /**
     * 买单数据
     *
     * @param    int    $p_id    产品id
     * @return   array | null
     */
    public static function check($p_id)
    {
        $data = (new Query())
                ->select("*")
                ->from("pay_store_check")
                ->where(['id' => $p_id])
                ->one();
        return $data;
    }




    /**
     * 购物车数据
     *
     * @param    string    $p_id    产品id
     * @return   array | null
     */
    public static function shopping($p_id)
    {
        if(is_numeric($p_id)) {
            $idList = [$p_id];
        }else
        {
            $idList = explode(',',trim($p_id,','));
        }
        $where = [
            'in','id',$idList
        ];
        $data = (new Query())
                ->select("*")
                ->from("pay_store_shopping")
                ->where($where)
                ->one();
        return $data;

    }



    /**
     * 统一数据
     *
     * @param    array    $data    数据集合
     * @param    int      $type    数据类型
     * @param    int      $number  购买数量
     * @param    int      $s_id    门店id
     * @param    int      $status  状态
     * @return   array | null
     */
    public static function unified($data, $type, $number, $status, $time,$s_id,$price)
    {
        if(!$data) return null;
        $header_img = (new Store())->headerImg($s_id);
        switch ($type)
        {
            case 1:
                $name = $data['vouchers_name'];
                break;
            case 2:
                $name = $data['group_name'];
                break;
            case 3:
                $name = "买单消费";
                break;
            case 4:
                $s_data = Store::findOne($s_id);
                $name = $s_data ? $s_data['store_name'] : "";
                break;
            default:
                $name = "未知商品";
        }
        $data = [
            'name' => $name,
            'price' => $price,
            'num' => $number,
            'status' => $status,
            'buy_time' => $time,
            'type' => $type,
            'header_img' => $header_img ? $header_img['img_url'] : "",
        ];
        return $data;
    }



}