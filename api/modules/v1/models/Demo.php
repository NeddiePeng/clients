<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/18
 * Time: 20:31
 */
namespace api\modules\v1\models;

use api\modules\Base;
use yii\db\ActiveRecord;

class Demo extends ActiveRecord
{


    public static function tableName()
    {
        return 'pay_store_info';
    }


    public function index()
    {
        $arra = [
            [
                'id' => 1,
                'name' => 'ok'
            ],
            [
                'id' => 1,
                'name' => 'success'
            ],
            [
                'id' => 2,
                'name' => 'what'
            ]
        ];
        $data = Base::array2dUnqied($arra,'id');
        var_dump($data);
    }


}