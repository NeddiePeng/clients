<?php
/**
 * 门店数据model.
 * User: Pengfan
 * Date: 2018/12/10
 * Time: 11:13
 */
namespace api\modules\v1\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

class StoreActions extends ActiveRecord
{

    //其他字段
    public $lat;
    public $lng;
    public $accessToken;
    public $page = 1;
    public $sort_two;
    public $s_id;


    //数据表
    public static function tableName()
    {
        return 'pay_store_info';
    }


    //验证规则
    public function rules()
    {
        return [
            [['lat','lng','page','top_sort'],'required','on' => 'store-list'],
            [['s_id','lng','lat'],'required','on' => 'storeDetails']
        ];
    }


    //字段信息
    public function attributeLabels()
    {
        return [
            'lat' => Yii::t('app','lat'),
            'lng' => Yii::t('app','lng'),
            'page' => Yii::t('app','page'),
            'top_sort' => Yii::t('app','top_sort'),
            's_id' => Yii::t('app','s_id')
        ];
    }



    /**
     * 获取各分类商家数据
     *
     * @param     array   $params   参数集合
     * @return    array | null
     */
    public function getStoreData($params)
    {
        $where = ['and'];
        $sortWhere = Yii::$app->where->select('storeSort',$params);
        $addrWhere = Yii::$app->where->select('storeAddr',$params);
        $otherWhere = Yii::$app->where->select("storeOther",$params);
        $where = array_merge($where,$sortWhere,$addrWhere,$otherWhere);

        $per = ($this->page - 1) * 10;
        $store = (new Query())
                 ->select("*")
                 ->from(static::tableName())
                 ->where($where)
                 ->offset($per)
                 ->limit(10)
                 ->all();
        return $store;
    }



    /**
     * 门店详情
     *
     * @return   array | null
     */
    public function storeDetails()
    {
        $data = (new Query())
                ->select("*")
                ->from(static::tableName())
                ->where(['id' => $this->s_id])
                ->one();
        return $data;
    }



    /**
     * 门店手机号
     *
     * @param    int     $s_id    门店id
     * @return   array | null
     */
    public function storeMobile($s_id)
    {
        $data = (new Query())
                ->select("*")
                ->from("pay_store_mobile")
                ->where(['x_id' => $s_id])
                ->all();
        return $data;
    }


}