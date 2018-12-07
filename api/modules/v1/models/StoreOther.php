<?php
/**
 * 商家门店model.
 * User: Pengfan
 * Date: 2018/12/4
 * Time: 19:51
 */
namespace api\modules\v1\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

class StoreOther extends ActiveRecord
{
	

	//数据库选择
	public static function getDb()
	{
		return Yii::$app->db2;
	}

	//数据表
	public static function tableName ()
	{
		return "pay_store_info";
	}



	/**
	 * 门店信息统一
     *
     * @param   array   $storeData   门店数据集合
     * @return  array
     */
	public function unified($storeData)
    {
        $last_data = [];
        $obj = new Store();
        foreach ($storeData as $k => $v)
        {
            $headerData = $obj->headerImg($v['id']);
            $otherData = $this->otherInfo($v['top_sort'],$v['score'],$v['two_sort']);
            $last_data[] = [
                's_id' => $v['id'],
                'store_name' => $v['store_name'],
                'address' => $v['address'],
                'lat' => $v['lat'],
                'lng' => $v['lng'],
                'headerImg' => $headerData ? $headerData['img_url'] : "",
                'one_sort' => $v['one_sort'],
                'two_sort' => $v['two_sort'],
                'pro_name' => $otherData[1],
                'score' => $v['score'],
                'exp' => $otherData[0],
                'type' => $otherData[2]
            ];
        }
        return $last_data;
    }



    /**
     * 重写store model里面的 其他信息方法
     *
     * @return  array
     */
    public function otherInfo($top_sort,$score,$sort_two)
    {
        $obj = new Store();
        if($top_sort == 2)
        {
            $exp = "良好";
            if($score >= 4)
            {
                $exp = "好";
            }
            $type = 'hotel';
            $storePro = $obj->storeSort($type,$sort_two);
            if($storePro['is_brand'] === 1)
            {
                $proSort = $storePro['brand_name'];
            }
            else
            {
                $proSort = "一般酒店";
                if($storePro['star_num'] >= 4) $proSort = "高档型";
            }
        }
        else {
            $exp = '';
            $type = 'other';
            $storePro = $obj->storeSort($type, $sort_two);
            $proSort = $storePro['sort_name'];
        }
        return [$exp,$proSort,$type];
    }



    /**
	 * 获取门店产品
     *
     * @param    array    $storeData    门店数据
     * @return   array | null
     */
	public function getPro($storeData)
    {
        $last_data = [];
        foreach ($storeData as $key => $val)
        {
            if($val['top_sort'] === 2)
            {
                $roomDate = $this->hotelPro($val['id'], 3);
                if($roomDate)$last_data[] = $this->roomDetails($roomDate);
            }
            else
            {
                $last_data[] = $this->pro($val['id']);
            }
        }
        if($last_data) return (new StoreOther())->unified($last_data);
        return null;
    }



    /**
     * 房间详情
     */
    public function roomDetails($roomDate)
    {
        $data = [];
        foreach ($roomDate as $k => $v)
        {
            $data[] = [
                'type' => $v['is_all_pay'],
                'name' => $v['spec_title'],
                'old' => $v['weekend_price'],
                'now' => $v['week_discount_price'],
                's_type' => 2
            ];
        }
        return $data;
    }



    /**
     * 门店产品【美食】
     *
     * @param    int    $s_id   门店id 【代金券  购物车   团购  买单】
     * @return   array | null
     */
    public function pro($s_id)
    {
        $obj = new Store();
        $data = [];
        //购物车活动
        $advert = $obj->storeAdvert($s_id);
        if($advert) $data[] = ['name' => $advert,'old' => 0,'now' => 0,'type' => 4,'s_type' => 1];

        //产品详情
        $proData = $obj->storePro($s_id);
        $data[] = [
            'name' => $proData[0],
            'now' => $proData[2],
            'old' => $proData[1],
            'type' => $proData[3],
            's_type' => 1
        ];
        return $data;
    }




    /**
     * 酒店产品
     *
     * @param    int     $s_id   门店id
     * @return   array | null
     */
    public function hotelPro($s_id,$limit = 0)
    {
        $data = (new Query())
                ->select("*")
                ->from("pay_hotel_room_spec")
                ->where(['x_id' => $s_id])
                ->andWhere(['status' => 4]);
        if($limit) return $data = $data->limit($limit)->all();
        return $data->all();
    }



    /**
     * 代金券详情
     */
    public function vouchers($s_id)
    {

    }


    /**
     * 团购详情
     */
    public function group($s_id)
    {

    }


    /**
     * 买单详情
     */
    public function check()
    {

    }


    /**
     * 单点详情
     */
    public function shopping()
    {

    }







}
