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


    //设置数据库
    public static function getDb()
    {
        return Yii::$app->db2;
    }



    //数据表
    public static function tableName()
    {
        return 'pay_store_info';
    }


    //验证规则
    public function rules()
    {
        return [
            [['lat','lng','page','sort_id'],'required','on' => 'store-list']
        ];
    }


    //字段信息
    public function attributeLabels()
    {
        return [
            'lat' => Yii::t('app','lat'),
            'lng' => Yii::t('app','lng'),
            'page' => Yii::t('app','page'),
            'sort_id' => Yii::t('app','sort_id')
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
        $where = Yii::$app->where->select('storeSort',$params);
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


}