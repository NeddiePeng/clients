<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/4
 * Time: 14:06
 */
namespace api\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

class LoginForm extends ActiveRecord
{

    private $_user = false;

    //授权码
    public static $accessToken = false;

    //手机验证码字段
    public $sms_code;

    public $status = 10;


    public static function tableName()
    {
        return 'pay_user';
    }


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['mobile'],'required','on' => 'login'],
            // username and password are both required
            [['mobile'], 'validateMobile','on' => 'login'],
            [['mobile','sms_code'],'required','on' => 'bind-mobile'],
            [['sms_code'],'validateSmsCode','on' => 'bind-mobile'],
            [['sms_code'],'validateSmsCode','on' => 'login']
        ];
    }



    /**
     * 验证短信码
     */
    public function validateSmsCode($attribute)
    {
        $redis = new \Redis();
        $redis->connect("127.0.0.1",'6379');
        $codeData = $redis->get("smsCode");
        if(!$codeData)
        {
            $this->addError($attribute, "验证码已失效.");
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
     * 字段名称
     */
    public function attributeLabels()
    {
        return [
            'mobile' => Yii::t("app",'mobile'),
            'sms_code' => Yii::t('app','sms_code')
        ];
    }


    /**
     * Validates the mobile.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateMobile($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user)
            {
                $this->register();
            }
        }
    }




    /**
     * 登录校验成功后，为用户生成新的token
     */
    public function onApiToken(){
        if(!User::apiTokenIsValid((new User())->accessToken)){
            (new User())->apiToken();
        }
    }



    /**
     * 记录用户信息
     *
     * @param     array    $params   用户信息
     * @param     int      $type     应用类型
     * @return    boolean
     */
    public static function enterOtherInfo($params, $type = 1)
    {
        $isEnter = self::findUnionID($params['UnionID']);
        if(is_array($isEnter))
        {
            return ['accessToken' => $isEnter['accessToken'],'is_bind' => 1];
        }
        elseif ($isEnter === 1)
        {
            return ['accessToken' => $isEnter['accessToken'],'is_bind' => 0];
        }
        $insert_data = [
            'UnionID' => $params['UnionID'],
            'type' => $type,
            'create_time' => time()
        ];
        $res = Yii::$app->db->createCommand()
               ->insert("other_app_user",$insert_data)
               ->execute();
        if ($res)
        {
            (new User())->apiToken();
            $access_token = LoginForm::$accessToken;
            $enterUser = self::enterInfo($access_token);
            return $enterUser ? ['accessToken' => $enterUser['accessToken'],'is_bind' => 0] : false;
        }
        else
        {
            return false;
        }
    }



    /**
     * 是否已授权
     *
     * @param       string    $UnionID   用户唯一标识
     * @return      boolean
     */
    public function findUnionID($UnionID)
    {
        $data = (new Query())
                ->select("id")
                ->from("other_app_user")
                ->where(['UnionID' => $UnionID])
                ->one();
        if($data)
        {
            $userData = User::getUser($data['id']);
            if($userData && $userData['mobile'])
            {
                return $userData;
            }
            elseif($userData && !$userData['mobile'])
            {
                return 1;
            }
            else
            {
                return 0;
            }
        }
        else
        {
            return 0;
        }
    }




    /**
     * 录入信息
     *
     * @param    string    $access_token   授权token
     * @return   static | boolean
     */
    public static function enterInfo($access_token)
    {
        $insert_data = [
            'accessToken' => $access_token,
            'status' => User::STATUS_ACTIVE
        ];
        $res = Yii::$app->db->createCommand()
               ->insert(User::tableName(),$insert_data)
               ->execute();
        $userData = User::findIdentityByAccessToken($access_token);
        return $res ? $userData : false;

    }



    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return $this->_user;
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->onApiToken();
            $this->_user = User::findByUsername($this->mobile);
        }
        return $this->_user;
    }



    /**
     * 手机号注册
     *
     * @return   boolean
     */
    public function register()
    {
        if(!$this->_user)
        {
            $this->_user = (new User())->register($this->mobile);
        }
        return $this->_user;
    }


    /**
     * 绑定手机号
     *
     * @param    array     $params   参数集合
     * @return   boolean
     */
    public static function bindMobile($params)
    {
        $userData = User::findIdentityByAccessToken($params['accessToken']);
        if($userData)
        {
            $update_data = [
                'mobile' => $params['mobile']
            ];
            $save = Yii::$app->db->createCommand()
                    ->update(User::tableName(),$update_data,['id' => $userData['id']])
                    ->execute();
            return $save > 0 ? true : false;
        }
        else
        {
            return false;
        }
    }



}