<?php
/**
 * 用户操作.
 * User: Pengfan
 * Date: 2018/12/5
 * Time: 19:30
 */
namespace api\modules\v1\models;

use api\models\User;
use yii\db\ActiveRecord;
use yii\db\Query;

class UserActions extends ActiveRecord
{

    //数据表
    public static function tableName()
    {
        return 'pay_user';
    }


    //验证规则
    public function rules()
    {
        parent::rules();
    }

    /**
     * 浏览记录
     *
     * @param    string    $accessToken    授权token
     * @param    string    $clients_id     设备唯一标识
     * @return   array | null
     */
    public static function browseHistory($accessToken,$clients_id)
    {
        $userData = User::findIdentityByAccessToken($accessToken);

        //更具设备唯一标识
        if(!$userData) return static::clientsIdStore($clients_id);
        $uid = $userData['id'];
        $where = [
            'and',
            ['=','uid',$uid]
        ];
        $historyData = (new Query())
                       ->select("*")
                       ->from("master_history_browse")
                       ->where($where)
                       ->all();
        return $historyData;
    }



    /**
     * 设备获取门店
     *
     * @param    string    $clients_id   设备唯一标示
     * @return   array | null
     */
    #TODO:门店唯一标识
    public static function clientsIdStore($clients_id)
    {
        $where = [
            'and',
            ['=','clients_id',$clients_id]
        ];
        $historyData = (new Query())
            ->select("*")
            ->from("master_history_browse")
            ->where($where)
            ->all();
        return $historyData;
    }


    /**
     * 购买记录
     */
    public static function browseOrder($accessToken)
    {

    }

}