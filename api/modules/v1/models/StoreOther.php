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
	public function unified($storeData,$type = null)
    {
        $last_data = [];
        $obj = new Store();
        foreach ($storeData as $k => $v)
        {
            $headerData = $obj->headerImg($v['id']);
            //$otherData = $this->otherInfo($v['top_sort'],$v['score'],$v['two_sort']);
            $otherData = Store::instance()->getOtherInfo($v['id'],$v['top_sort'],$v['one_sort'],$type);
            $last_data[] = [
                's_id' => $v['id'],
                'store_name' => $v['store_name'],
                'address' => $v['address'],
                'lat' => $v['lat'] ? $v['lat'] : 0,
                'lng' => $v['lng'] ? $v['lng'] : 0,
                'headerImg' => $headerData ? [$headerData['img_url']] : null,
                'sort_name' => $otherData[0] ? $otherData[0]['sort_name'] : "",
                'score' => $v['score'],
                'type' => $v['top_sort'] == 2 ? 'hotel' : 'other',
                'like_num' => $v['like_num'] ? $v['like_num'] : 0,
                'share_num' => $v['share_num'] ? $v['share_num'] : 0,
                'proData' => $otherData[1],
                'msgData' => Store::instance()->msgData($v['id'])
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
	public function getPro($storeData,$type = null)
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
                $storeData[$key]['proData'] = $this->pro($val['id']);
            }
        }
        if($storeData) return (new StoreOther())->unified($storeData,$type);
        return null;
    }



    /**
     * 房间详情
     *
     * @param    array   $roomDate   房间数据
     * @return   array | null
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
            's_type' => 1,
            's_id' => $s_id
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
        if($limit) return $data = $data->limit($limit)->all(Yii::$app->db2);
        return $data->all(Yii::$app->db2);
    }



    /**
     * 附近商品格式化
     *
     * @param   int   $s_id    门店id
     * @param   int   $type    门店类型
     * @return   array | null
     */
    public function proFormat($s_id,$type)
    {
        if($type == 2)
        {
            $allDays = $this->allDays($s_id);
            $last_all = '';
            $all_old = $all_now = 0;
            if ($allDays)
            {
                foreach ($allDays as $key => $val)
                {
                    $last_all = $val['spec_title'] . '￥' . $val['week_discount_price'];
                    $all_old = 100;
                    $all_now = 50;
                }
            }
            $hourDays = $this->hourDays($s_id);
            $hour_data = '';
            $old = $now = 0;
            if($hourDays)
            {
                foreach ($hourDays as $k => $val)
                {
                    $hour_data = $val['spec_title'] . '￥' . $val['week_discount_price'];
                    $old = 100;
                    $now = 50;
                }
            }
            return [
                [
                    'type' => 'all',
                    'name' => $last_all,
                    'old' => $all_old,
                    'now' => $all_now
                ],
                [
                    'type' => 'hour',
                    'name' => $hour_data,
                    'old' => $old,
                    'now' => $now
                ]
            ];

        }
        else
        {
            $vouData = $this->vouchers($s_id);
            $last_vou = '';
            $v_old = $v_now = $old = $now = 0;
            if($vouData)
            {
                foreach ($vouData as $k => $v)
                {
                    $last_vou = $v['vouchers_name'];
                    $v_old = 100;
                    $v_now = 50;
                }
            }
            $groupData = $this->group($s_id);
            $last_group = '';
            if($groupData)
            {
                foreach ($groupData as $k => $v)
                {
                    $last_group = $v['group_name'].$v['group_price'] ? $v['group_name'].$v['group_price'].'元' : "";
                    $old = 100;
                    $now = 50;
                }
            }
            $shopAdvert = Store::instance()->storeAdvert($s_id);
            $checkData = $this->check($s_id);
            $last_check = '';
            if($checkData)
            {
                $dis_num = $this->getDiscount($checkData['dis_id']);
                $last_check = '买单'.$dis_num.'折';
            }
            $last_return_data = [];
            if(trim($last_vou,',')) $last_return_data[] = ['type' => 1,'name' => trim($last_vou,','),'now' => $v_now,'old' => $v_old];
            if(trim($last_group,',')) $last_return_data[] = ['type' => 2,'name' => trim($last_group,','),'now' => $now,'old' => $old];
            if(trim($shopAdvert,',')) $last_return_data[] = ['type' => 4,'name' => trim($shopAdvert,',')];
            if($last_check) $last_return_data[] = ['type' => 3,'name' => $last_check];
            return $last_return_data;
        }

    }


    /**
     * 门店产品
     *
     * @param   int   $s_id    门店id
     * @param   int   $type    门店类型
     * @return  array | null
     */
    public function storePro($s_id,$type)
    {
        if($type == 2)
        {
            $allDays = $this->allDays($s_id);
            $last_all = '';
            if ($allDays)
            {
                foreach ($allDays as $key => $val)
                {
                    $last_all .= $val['spec_title'] . '￥' . $val['week_discount_price'];
                }
            }
            $hourDays = $this->hourDays($s_id);
            $hour_data = '';
            if($hourDays)
            {
                foreach ($hourDays as $k => $val)
                {
                    $hour_data .= $val['spec_title'] . '￥' . $val['week_discount_price'];
                }
            }
            return [
                ['type' => 'all','name' => $last_all],
                ['type' => 'hour','name' => $hour_data]
            ];

        }
        else
        {
            $vouData = $this->vouchers($s_id);
            $last_vou = '';
            if($vouData)
            {
                foreach ($vouData as $k => $v)
                {
                    $last_vou .= $v['vouchers_name'] . ',';
                }
            }
            $groupData = $this->group($s_id);
            $last_group = '';
            if($groupData)
            {
                foreach ($groupData as $k => $v)
                {
                    $last_group .= $v['group_name'].$v['group_price'].'元,';
                }
            }
            $shopAdvert = Store::instance()->storeAdvert($s_id);
            $checkData = $this->check($s_id);
            $last_check = '';
            if($checkData)
            {
                $dis_num = $this->getDiscount($checkData['dis_id']);
                $last_check = '买单'.$dis_num.'折';
            }
            $last_return_data = [];
            /*if(trim($last_vou,',')) $last_return_data[] = ['type' => 1,'name' => trim($last_vou,',')];
            if(trim($last_group,',')) $last_return_data[] = ['type' => 2,'name' => trim($last_group,',')];
            if(trim($shopAdvert,',')) $last_return_data[] = ['type' => 4,'name' => trim($shopAdvert,',')];
            if($last_check) $last_return_data[] = ['type' => 3,'name' => $last_check];
            return $last_return_data;*/
            return [
                ['type' => 1,'name' => trim($last_vou,',')],
                ['type' => 2,'name' => trim($last_group,',')],
                ['type' => 4,'name' => trim($shopAdvert,',')],
                ['type' => 3,'name' => $last_check]
            ];
        }


    }



    /**
     * 详情商品
     *
     * @param    int    $s_id    门店id
     * @param    int    $type    门店类型
     * @return   array | null
     */
    public function detailsPro($s_id, $type)
    {
        if($type == 2)
        {
            return null;
        }
        else
        {
            $checkData = $this->check($s_id);
            $lastCheck = null;
            if($checkData) $lastCheck = $this->dataFormat($checkData);
            $vouData = $this->vouchers($s_id);
            $lastVou = null;
            if($vouData) $lastVou = $this->dataFormatV($vouData);
            $groupData = $this->group($s_id);
            $lastGroup = null;
            if($groupData) $lastGroup = $this->dataFormatG($groupData);
            return [
                'check' => $lastCheck,
                'vouchers' => $lastVou,
                'group' => $lastGroup
            ];
        }
    }




    /**
     * 买单数据格式化
     *
     * @param    array    $checkData   买单数据
     * @return   array | null
     */
    public function dataFormat($checkData)
    {
        $last_data = [];
        foreach ($checkData as $key => $val)
        {
            $dis_num = $this->getDiscount($val['dis_id']);
            $last_data['name'] = $dis_num.'折扣优惠';
            $rules = $this->proRules($val['id'],3);
            if(!$rules) $last_data['rules'] = "每天00:00-24:00";
            $last_data['rules'] = $this->checkRules($rules);
        }
        return $last_data;
    }



    /**
     * 买单使用规则
     *
     * @param    array   $rules   使用规则
     * @return   array | null
     */
    public function checkRules($rules)
    {
        $last_data = [];
        if($rules['is_available'] === 1) $last_data['use_time'] = "每天00:00-24:00";
        $ruleTime = $this->timeRules($rules['id']);
        if($rules)
        {
            foreach ($ruleTime as $key => $val)
            {
                if($val['type'] == 2) $last_data['use_time'] = '每天'.date('H:i',$val['start_time']).'-'.date('H:i',$val['end_time']);
            }
        }
        return $last_data;
    }




    /**
     * 团购数据格式化
     *
     * @param    array    $groupData    团购数据
     * @return   array | null
     */
    public function dataFormatG($groupData)
    {
        if(!$groupData) return null;
        $last_data = [];
        foreach ($groupData as $key => $val)
        {
            $rules = $this->proRules($val['project_id'],2);
            $last_rules = $this->groupRules($rules);
            $last_data[] = [
                'name' => $val['use_max'].'人'.$val['group_name'],
                'price' => $val['group_price'],
                'buy_num' => $val['pay_num'],
                'old_price' => $val['price'],
                'rules' => $last_rules,
                'id' => $val['project_id'],
                'headerImg' => $val['img_url']
            ];
        }
        return $last_data;
    }




    /**
     * 团购使用规则
     *
     * @param     array    $rules   使用规则
     * @return    array | null
     */
    public function groupRules($rules)
    {
        $last_data = [];
        if(!$rules) return null;
        if($rules['is_available'] === 1) $last_data['use_time'] = "周一至周日";
        $ruleTime = $this->timeRules($rules['id']);
        if($ruleTime)
        {
            foreach ($ruleTime as $key => $val)
            {
                $last_data['use_time'] = date('Y-m-d',$val['start_time']);
                $last_data['use_time'] .= '至'.date("Y-m-d",$val['end_time']);
            }
        }
        return $last_data;
    }



    /**
     * 代金券数据格式化
     *
     * @param    array   $vouData   代金券数据
     * @return   array | null
     */
    public function dataFormatV($vouData)
    {
        $last_data = null;
        foreach ($vouData as $key => $val)
        {
            $rules = $this->proRules($val['project_id'],1);
            if(!$rules) $last_rules = null;else $last_rules = $this->vouRules($rules);
            $last_data[] = [
                'name' => $val['vouchers_name'],
                'price' => $val['buy_price'],
                'buy_num' => $val['sale_num'],
                'old_price' => $val['face_val'],
                'rules' => $last_rules,
                'id' => $val['project_id']
            ];
        }
        return $last_data;
    }



    /**
     * 代金券使用规则
     *
     * @param    array   $rules   规则数据
     * @return   array | null
     */
    public  function vouRules($rules)
    {
        $last_data = [];
        if($rules['is_available'] === 1) $last_data['use_time'] = "周一至周日";
        $ruleTime = $this->timeRules($rules['id']);
        if($rules)
        {
            foreach ($ruleTime as $key => $val)
            {
                if($val['type'] == 2) $last_data['use_time'] = '每天'.date('H:i',$val['start_time']).'-'.date('H:i',$val['end_time']);
            }
        }
        if($rules['is_overlying'] == 1) $last_data['overlaying'] = $rules['overlying_other']; else $last_data['overlaying'] = "不可叠加使用";

        return $last_data;
    }



    /**
     * 代金券数据
     *
     * @param   int    $s_id    门店id
     * @return  array | null
     */
    public function vouchers($s_id)
    {
        $data = (new Query())
                ->select("*")
                ->from("pay_store_project_s_id as project")
                ->leftJoin("pay_store_vouchers as vou",'vou.id=project.project_id')
                ->where(['project.x_id' => $s_id])
                ->andWhere(['project.type' => 1])
                ->all();
        return $data;
    }


    /**
     * 团购数据
     *
     * @param   int   $s_id   门店id
     * @return  array | null
     */
    public function group($s_id)
    {
        $data = (new Query())
            ->select("*")
            ->from("pay_store_project_s_id as project")
            ->leftJoin("pay_store_group as group",'group.id=project.project_id')
            ->where(['project.x_id' => $s_id])
            ->andWhere(['project.type' => 2])
            ->all();
        return $data;
    }


    /**
     * 买单数据
     *
     * @param   int   $s_id   门店id
     * @return  array | null
     */
    public function check($s_id)
    {
        $data = (new Query())
            ->select("*")
            ->from("pay_store_project_s_id as project")
            ->leftJoin("pay_store_check as group",'group.id=project.project_id')
            ->where(['project.x_id' => $s_id])
            ->andWhere(['project.type' => 3])
            ->all();
        return $data;
    }



    /**
     * 获取折扣
     *
     * @param   int    $dis_id    折扣id
     * @return  string
     */
    public function getDiscount($dis_id)
    {
        $data = (new Query())
                ->select("*")
                ->from("pay_store_discount")
                ->where(['id' => $dis_id])
                ->one();
        return $data ? $data['dis_num'] /10 : 0;
    }



    /**
     * 全天房数据
     *
     * @param   int    $s_id    门店id
     * @return  array | null
     */
    public function allDays($s_id)
    {
        $data = (new Query())
                ->select("*")
                ->from("pay_hotel_room_spec")
                ->where(['x_id' => $s_id])
                ->andWhere(['is_all_pay' => 1])
                ->all(Yii::$app->db2);
        return $data;
    }



    /**
     * 钟点房数据
     *
     * @param    int    $s_id    门店id
     * @return   array | null
     */
    public function hourDays($s_id)
    {
        $data = (new Query())
                ->select("*")
                ->from("pay_hotel_room_spec")
                ->where(['x_id' => $s_id])
                ->andWhere(['is_all_pay' => 2])
                ->all(Yii::$app->db2);
        return $data;
    }



    /**
     * 产品使用规则
     *
     * @param    int    $p_id    产品id
     * @param   int    $type    产品类型
     * @return   array | null
     */
    public function proRules($p_id, $type)
    {
        $data = (new Query())
                ->select("*")
                ->from("pay_store_project_rule")
                ->where(['p_id' => $p_id])
                ->andWhere(['type' => $type])
                ->one();
        return $data;
    }




    /**
     * 使用时间限制
     *
     * @param   int   $rule_id    规则id
     * @return  array | null
     */
    public function timeRules($rule_id)
    {
        $data = (new Query())
                ->select("*")
                ->from("pay_store_rule_time")
                ->where(['rule_id' => $rule_id])
                ->all();
        return $data;
    }








}
