<?php
/**
 * 首页Controller.
 * User: Pengfan
 * Date: 2018/12/4
 * Time: 19:48
 */
namespace api\modules\v1\controllers;

use Yii;
use api\behaviors\TokenBehavior;
use api\modules\Base;

class IndexController extends Base
{

    //model类
    public $modelClass = 'api\modules\v1\models\Store';


    //检查有权限访问
    public function behaviors()
    {
        return [
            'TokenBehavior' => [
                'class' => TokenBehavior::className(),
                'tokenParam' => 'accessToken',
            ],
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
     * 猜你喜欢
     */
    public function actionStoreList()
    {
        $getParams = $this->params;
        $model = new $this->modelClass(['scenario' => 'index-like']);
        $headers = Yii::$app->request->headers;
        $accessToken =  $headers->get('accessToken');
        $params = array_merge($getParams,['accessToken' => $accessToken]);
        $loadParam = $model->load($params,'');
        if($loadParam && $model->validate())
        {
            $storeData = $this->getCache($getParams['clients_id'],$params['lng'],$params['lat']);
            if(!$storeData)
            {
                $query = $model->getStoreList();
                $this->setCache($query, $getParams['clients_id'],$params['lng'],$params['lat']);
                $storeData = $this->getCache($getParams['clients_id'],$params['lng'],$params['lat']);
                if(!$storeData)
                {
                    return $this->returnData(0,'数据为空');
                }
                foreach ($storeData as $k => $v)
                {
                    $dis = $this->sum($params['lat'],$params['lng'],$v['lat'],$v['lng']);
                    $storeData[$k]['distance'] = $dis;
                }
                return $this->returnData(200,'获取成功',$storeData);
            }
            foreach ($storeData as $k => $v)
            {
                $dis = $this->sum($params['lat'],$params['lng'],$v['lat'],$v['lng']);
                $storeData[$k]['distance'] = $dis;
            }
            return $this->returnData(200,'获取成功',$storeData);
        }
        else
        {
            return $this->returnRuleErr($model);
        }

    }


    /**
     * 写入缓存
     *
     * @param     array   $data         门店数据
     * @param     string  $clients_id   设备唯一标识
     */
    public function setCache($data, $clients_id, $lng, $lat)
    {
        Yii::$app->redisPage->db_id = 1;
        $limit_key = "like:index:{$clients_id}:{$lng}:{$lat}";
        $data_key = "like:index:data:{$clients_id}:{$lng}:{$lat}";
        Yii::$app->redisPage->setCache($limit_key, $data_key, $data, 's_id');
    }



    /**
     * 获取缓存
     *
     * @param   string   $clients_id   设备id
     * @return  array | null
     */
    public function getCache($clients_id, $lng, $lat)
    {
        $limit_key = "like:index:{$clients_id}:{$lng}:{$lat}";
        $data_key = "like:index:data:{$clients_id}:{$lng}:{$lat}";
        Yii::$app->redisPage->db_id = 1;
        $cacheData = Yii::$app->redisPage->getCache($limit_key,$data_key,'s_id');
        return $cacheData;
    }










}