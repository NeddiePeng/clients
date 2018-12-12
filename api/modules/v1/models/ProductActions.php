<?php
/**
 * 商品操作.
 * User: PengFan
 * Date: 2018/12/12
 * Time: 15:25
 */
namespace api\modules\v1\models;

use Yii;
use yii\db\ActiveRecord;

class ProductActions extends ActiveRecord
{

    //其他字段
    public $s_id;
    public $pro_type;
    public $sortData = [];
    public $content = [];
    public $id;
    public static function tableName()
    {
        return 'pay_store_group';
    }



    //验证规则
    public function rules()
    {
        return [
            [['s_id','id','pro_type'],'required','on' => 'pro-details']
        ];
    }



    /**
     * 商品详情
     */
    public function productDetails()
    {
        switch ($this->pro_type)
        {
            case 1:
                $data = $this->vouData();
                break;
            case 2:
                $data = $this->groupData();
                break;
            case 3:
                $data = $this->checkDetails();
                break;
            case 4:
                $data = "";
                break;
            default:
                $data = null;
        }
        return $data;
    }




    /**
     * 团购数据
     */
    public function groupData()
    {
        $data = Product::group($this->id);
        if(!$data) return null;
        $rules = StoreOther::instance()->proRules($this->id,2);
        if(!$rules) $rulesData = null;
        $rulesTime = StoreOther::instance()->timeRules($rules['id']);
        $groupImgData = Product::groupContent($this->id);
        $imgData = [];
        if($groupImgData)
        {
            foreach ($groupImgData as $key => $val)
            {
                if($val['dishes_name'] )
                {
                    $imgData[] = [
                    'img' => $val['img_url'],
                    'name' => $val['dishes_name']
                ];
                }
                if(in_array($val['sort_id'],$this->sortData)){
                    if($val['dishes_name'])
                    {
                        $this->content[$val['sort_id']]['content'][] = [
                            'name' => $val['dishes_name'],
                            'num' => $val['number'],
                            'price' => $val['dis_price']
                        ];
                    }
                }else{
                    if($val['dishes_name'])
                    {
                        $this->sortData[] = $val['sort_id'];
                        $sortData = $this->sortData($val['sort_id']);
                        $this->content[$val['sort_id']]['sort_name'] = $sortData ? $sortData['sort_name'] : '团购产品';
                        $this->content[$val['sort_id']]['selectType'] = $val['select_type'];
                        $dishesData = [
                            'name' => $val['dishes_name'],
                            'num' => $val['number'],
                            'price' => $val['dis_price']
                        ];
                        $this->content[$val['sort_id']]['content'][] =$dishesData;
                    }
                }

            }
        }
        if($data['img_url'])
        {
            $imgData[] = [
                'img' => $data['img_url'],
                'name' => $data['group_name']
            ];
        }
        $topData = [
            'overlying' => $rules['is_overlying'] == 2 ? "不可叠加使用" : $rules['overlying_other'] ? $rules['overlying_other'] : "叠加使用限制",
            'bespeak' => $rules['is_bespeak'] == 1 ? '需提前预约' : '无需预约',
            'refund' => $rules['is_refund'] == 1 ? '随时退' : '过期退',
            'id' => $this->id,
            'imgUrl' => ['imgList' => $imgData,'count' => count($imgData)],
            'groupContent' => array_values($this->content)
        ];
        $rulesData = $this->commonRules($rules, $rulesTime,$data);
        return [
            'topData' => $topData,
            'rulesData' => $rulesData
        ];
    }




    /**
     * 代金券数据
     */
    public function vouData()
    {
        $data = Product::vou($this->id);
        if(!$data) return null;
        $rules = StoreOther::instance()->proRules($this->id,1);
        if(!$rules) $rulesData = null;
        $rulesTime = StoreOther::instance()->timeRules($rules['id']);
        $rulesTimeData = $this->commonRules($rules, $rulesTime,$data);
        $topData = [
            'overlying' => $rules['is_overlying'] == 2 ? "不可叠加使用" : $rules['overlying_other'] ? $rules['overlying_other'] : "叠加使用限制",
            'bespeak' => $rules['is_bespeak'] == 1 ? '需提前预约' : '无需预约',
            'refund' => $rules['is_refund'] == 1 ? '随时退' : '过期退',
            'id' => $this->id
        ];
        return [
            'topData' => $topData,
            'rulesData' => $rulesTimeData
        ];


    }


    /**
     * 公共规则
     *
     * @param    array   $rules     规则
     * @param    array   $rulesTime 时间规则
     * @param    array   $data      产品数据
     * @return   array
     */
    public function commonRules($rules, $rulesTime,$data)
    {
        $unavailableTime = $rules['is_available'] == 1 ? "24小时可用," : "";
        $useTime = '';
        if($rulesTime)
        {
            foreach ($rulesTime as $key => $val)
            {
                if($val['type'] == 1)
                {
                    $unavailableTime .= date('Y.m.d',$val['start_time']).'至'.date('Y.m.d',$val['end_time']) .',';
                }
                if($val['type'] == 3)
                {
                    $unavailableTime .= '周'.$val['week_num'] . ',';
                }
                if($val['type'] == 2)
                {
                    $useTime .= date('H:i',$val['start_time']).'~'.date('H:i',$val['end_time']) . ',';
                }
            }
        }

        $unavailableTime .= $rules['is_holiday'] == 1 ? "法定节假日不可用" : "";
        $useRules[] = $rules['is_bespeak'] == 1 ? '需提前预约' : '无需预约，消费高峰时可能需要等位';
        $useRules[] = $rules['is_overlying'] == 2 ? "不可叠加使用" : $rules['overlying_other'] ? $rules['overlying_other'] : "使用叠加限制";
        $useRules[] = $rules['is_give_change'] == 2 ? "不支持找零" : "支持找零";
        $useRules[] = $rules['is_currency'] == 2 ? $rules['currency_other'] : "全场通用";
        $useRules[] = $rules['is_invoice'] == 2 ? '不提供发票' : "不提供发票";
        $useRules[] = $rules['is_other_discount'] == 1 ? $rules['other_discount'] : "不可同时享受商家其他优惠";
        $rulesData = [
            'validityTime' => $rules['is_effective'] == 1 ? "长期有效" : date("Y.m.d",$data['create_time']).'至'.date('Y.m.d',$rules['effective_time']),
            'unavailableTime' => trim($unavailableTime,','),
            'useTime' => trim($useTime,','),
            'useRules' => $useRules
        ];
        return $rulesData;
    }



    /**
     * 买单详情
     *
     * @return   string
     */
    public function checkDetails()
    {
        $data = Product::check($this->id);
        if(!$data) return '';
        $parData = StoreOther::instance()->getDiscount($data['dis_id']);
        if(!$parData) return '';
        return $parData.'折';

    }


    /**
     * 分类数据
     *
     * @param   int   $id   分类id
     * @param   int   $type 分类类型
     * @return  array | null
     */
    public function sortData($id,$type = 2)
    {
        $data = (new \yii\db\Query())
                ->select("*")
                ->from("pay_store_build_sort")
                ->where(['id' => $id])
                ->andWhere(['is_shopping' => $type])
                ->one();
        return $data;

    }

}