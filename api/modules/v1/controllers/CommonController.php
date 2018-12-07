<?php
/**
 * 公共Controller.
 * User: Pengfan
 * Date: 2018/12/5
 * Time: 16:01
 */
namespace api\modules\v1\controllers;

use Yii;
use api\behaviors\TokenBehavior;
use api\modules\Base;

class CommonController extends Base
{


    //model类
    public $modelClass = 'api\modules\v1\models\Common';


    //检查有权限访问
    public function behaviors()
    {
        return [
            'TokenBehavior' => [
                'class' => TokenBehavior::className(),
                'tokenParam' => 'accessToken',
            ]
        ];
    }


    public function actions()
    {
        $actions = parent::actions();
        // 注销系统自带的实现方法
        unset($actions['index']);
        return $actions;
    }



    /**
     * 获取banner数据
     */
    public function actionIndex()
    {
        $params = $this->params;
        $model = new $this->modelClass(['scenario' => 'advert']);
        $loadParam = $model->load($params,'');
        if($model->validate() && $loadParam)
        {
            $limit_key = "advert:limit:{$params['mask']}";
            $data_key = "advert:data:{$params['mask']}";
            Yii::$app->redisPage->db_id = 4;
            $cacheData = Yii::$app->redisPage->getCache($limit_key,$data_key,'id');
            if(!$cacheData)
            {
                $data = $model->findAdvert();
                if($data)
                {
                    return $this->returnData(200,'获取成功',$data);
                }
                return $this->returnData(0,'数据为空');
            }
            else
            {
                return $this->returnData(200,'获取成功',$cacheData);
            }
        }
        else
        {
            return $this->returnRuleErr($model);
        }

    }



    /**
     * 获取短信验证码
     */
    public function actionSmsCode()
    {
        $params = $this->params;
        if(!$params['mobile']) return $this->returnData(400,'手机号错误');
        $res = Yii::$app->ali->getSmsCode($params['mobile']);
        if(!is_bool($res))
        {
            return $this->returnData(400,$res);
        }
        return $this->returnData(200,'发送成功');
    }








}