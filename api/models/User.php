<?php
/**
 * 用户授权Model.
 * User: Pengfan
 * Date: 2018/12/4
 * Time: 10:17
 */
namespace api\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{

    const STATUS_DELETED = 0;

    const STATUS_ACTIVE = 10;

    //手机验证码字段
    public $sms_code;

    public $authKey;

    public $accessToken = '';

    private static $users = null;

    public $status = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pay_user';
    }



    //验证规则
    public function rules()
    {
        return [
            [['mobile','sms_code'],'required','on' => 'login'],
            [['sms_code'],'validateCode','on' => 'login']
        ];
    }



    /**
     * 验证手机验证码
     */
    public function validateCode($attribute)
    {
        $redis = new \Redis();
        $redis->connect("127.0.0.1",'6379');
        $codeData = $redis->get("mobileCode");
        if(!$codeData)
        {
            $this->addError($attribute, "验证码错误.");
        }
        else
        {
            if($this->$attribute != $codeData)
            {
                $this->addError($attribute, "验证码错误.");
            }
        }
        return;
    }



    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['accessToken' => $token]);
    }


   /**
    * 调用认证
    *
    * @return   static
    */
    public function loginByAccessToken($accessToken, $type) {
        //查询数据库中有没有存在这个token
        return static::findIdentityByAccessToken($accessToken, $type);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        $usersData = static::findOne(['mobile' => $username, 'status' => self::STATUS_ACTIVE]);
        if($usersData)
        {
            Yii::$app->db->createCommand()
            ->update(self::tableName(),['accessToken' => LoginForm::$accessToken],['mobile' => $username])
            ->execute();
        }
        return static::$users = $usersData;
    }



    /**
     * 注册
     *
     * @param   string $mobile  手机号
     * @return  boolean
     */
    public function register($mobile)
    {
        $this->apiToken();
        $username = $this->randName();
        $insert_data = [
            'mobile' => $mobile,
            'accessToken' => LoginForm::$accessToken,
            'status' => self::STATUS_ACTIVE,
            'username' => $username,
            'create_time' => time(),
            'nickname' => $username
        ];
        $save = Yii::$app->db->createCommand()
                ->insert(self::tableName(),$insert_data)
                ->execute();
        return $save > 0 ? true : false;

    }



    /**
     * 随机用户名
     */
    public function randName()
    {
        $str = "QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm";
        $str = 'jfb_'.substr(str_shuffle($str),5,8);
        return $str;
    }


    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }




    public function apiToken()
    {
        $this->accessToken = Yii::$app->security->generateRandomString().'_'.time();
        //存储到缓存里面,第二个参数为该用户的对象
        LoginForm::$accessToken = $this->accessToken;
        Yii::$app->cache->set($this->accessToken,self::$users,7200);
    }



    public static function getUser($id)
    {
        return static::findOne(['unionId' => $id]);

    }



    public static function apiTokenIsValid($token)
    {
        if (empty($token)) {
            return false;
        }
        return Yii::$app->cache->get($token);
    }

}

