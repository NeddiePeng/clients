<?php
namespace api\modules\v1\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%guide}}".
 *
 * @property integer $id
 * @property string $imgurl
 * @property integer $status
 * @property integer $flag
 */
class Guide extends ActiveRecord
{



    public static function tableName()
    {
        return '{{%mxq_guide}}';
    }



    //返回字段
    public function fields(){
        return [
            'id',
            'imgurl' => 'imgurl',
            'status' => function($model) {
                return $model->status == 1 ? 'success' : 'fail';
            }
        ];
    }


    //验证规则
    public function rules()
    {
        return [
            [
                ['imgurl', 'status', 'flag'], 'required'
            ],
            [
                ['status', 'flag'], 'integer'
            ],
            [
                ['imgurl'], 'string', 'max' => 255
            ]
        ];
    }



    //表字段名称
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'imgurl' => Yii::t('app', 'imgurl'),
            'status' => Yii::t('app', 'status'),
            'flag' => Yii::t('app', 'flag')
        ];
    }
}