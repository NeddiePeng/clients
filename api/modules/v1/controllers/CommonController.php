<?php
/**
 * 公共Controller.
 * User: Pengfan
 * Date: 2018/12/5
 * Time: 16:01
 */
namespace api\modules\v1\controllers;

use api\models\User;
use api\modules\v1\models\StoreActions;
use api\modules\v1\models\UserActions;
use Yii;
use api\behaviors\TokenBehavior;
use api\modules\Base;

class CommonController extends Base
{


    //model类
    public $modelClass = 'api\modules\v1\models\Common';


    //门店点赞数据
    public $likeData;

    //门店分享数据
    public $shareData;

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
     * 二级分类 & 商圈
     */
    public function actionSortDistrict()
    {
        $params = $this->params;
        $model = new $this->modelClass(['scenario' => 'business']);
        $loadParam = $model->load($params,'');
        if($loadParam && $model->validate())
        {
            $data = $model->businessData();
            $lastData = $model->optimize($data);
            $sortData = $model->sortData();
            $sortStoreData = StoreActions::instance()->sortStore($sortData);
            $returnData = [
                'addrData' => $lastData,
                'sortData' => $sortStoreData
            ];
            return $this->returnData(200,'success',$returnData);
        }
        return $this->returnRuleErr($model);
    }



    /**
     * 获取banner数据
     */
    public function actionAdvert()
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




    /**
     * 门店点赞 & share
     */
    public function actionStoreLike()
    {
        $this->getBehavior('TokenBehavior')->checkAccessToken();
        $params = $this->params;
        $accessToken = Yii::$app->request->headers->get('accessToken');
        $model = new $this->modelClass(['scenario' => 'like-share']);
        $loadParam = $model->load($params,'');
        if($loadParam && $model->validate())
        {
            $userData = User::findIdentityByAccessToken($accessToken);
            $uid = $userData['id'];
            $isLikeShare = UserActions::instance()->isLikeShare($params['s_id'],$uid);
            if($isLikeShare) return $this->returnData(400,'已操作过该门店');
            Yii::$app->likeShare->setLikeRedis($params['s_id'],$params['type'],$uid);
            file_get_contents("http://clients.qmwjj.cc/v1/synchro");
            return $this->returnData(200,'点赞成功');
        }
        return $this->returnRuleErr($model);
    }




    /**
     * 数据同步
     */
    public function actionSynchro()
    {
        $cacheData = Yii::$app->likeShare->synchro();
        if(!$cacheData) return false;
        $likeData = $cacheData['likeData'];
        $shareData = $cacheData['shareData'];
        if($likeData)
        {
            foreach ($likeData as $k => $v)
            {
                $this->likeData[$v['s_id']][] = $v;
            }
            if($this->likeData) {
                foreach ($this->likeData as $key => $val) {
                    $update_data = ['like_num' => count($val)];
                    Yii::$app->db->createCommand()
                    ->update("pay_store_info",$update_data,['id' => $key])
                    ->execute();
                }
                $this->insert_data();
            }
        }
        if($shareData)
        {
            foreach ($shareData as $k => $v)
            {
                $this->shareData[$v['s_id']][] = $v;
            }
            if($this->shareData) {
                foreach ($this->shareData as $key => $val) {
                    $update_data = ['share_num' => count($val)];
                    Yii::$app->db->createCommand()
                        ->update("pay_store_info",$update_data,['id' => $key])
                        ->execute();
                }
                $this->insert_data_share();
            }
        }
    }



    /**
     * 点赞信息写入数据到数据库
     */
    public function insert_data()
    {
        $likeData = $this->likeData;
        $insert_data = [];
        foreach ($likeData as $k => $v)
        {
            foreach ($v as $key => $val)
            {
                $insert_data[] = [
                    'x_id' => $k,
                    'uid' => $val['uid'],
                    'hanld_time' => $val['time'],
                    'type' => 'like'
                ];
            }
        }
        $filed = ['x_id','uid','hanld_time','type'];
        Yii::$app->db3->createCommand()
        ->batchInsert("user_like_share",$filed,$insert_data)
        ->execute();
    }




    /**
     * 分享信息写入数据库
     */
    public function insert_data_share()
    {
        $shareData = $this->shareData;
        $insert_data = [];
        foreach ($shareData as $k => $v)
        {
            if(!$v) continue;
            foreach ($v as $key => $val)
            {
                $insert_data[] = [
                    'x_id' => $k,
                    'uid' => $val['uid'],
                    'hanld_time' => $val['time'],
                    'type' => 'share'
                ];
            }
        }
        $filed = ['x_id','uid','hanld_time','type'];
        Yii::$app->db->createCommand()
            ->batchInsert("user_like_share",$filed,$insert_data)
            ->execute();
    }




    /**
     * 全文检索
     */
    public function actionRetrieval()
    {

    }











}