<?php
/**
 * 共用model.
 * User: Pengfan
 * Date: 2018/12/5
 * Time: 16:57
 */
namespace api\modules\v1\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

class Common extends ActiveRecord
{


    //数据表
    public static function tableName()
    {
        return 'master_advert';
    }


    //验证规则
    public function rules()
    {
        return [
            [['mask'],'required','on' => 'advert'],
            [['s_id','type'],'required','on' => 'like-share']
        ];
    }


    //字段名称
    public function attributeLabels()
    {
        return [
            'mask' => Yii::t('app','mask'),
            's_id' => Yii::t('app','s_id'),
            'type' => Yii::t('app','type')
        ];
    }



    /**
     * 获取advert数据
     *
     * @return   array | null
     */
    public function findAdvert()
    {
        $mask = $this->mask;
        $data = (new Query())
                ->select("id,img_url,jump,type")
                ->from(self::tableName())
                ->where(['mask' => $mask,'status' => 1])
                ->all();
        return $data ? $data : null;
    }


}