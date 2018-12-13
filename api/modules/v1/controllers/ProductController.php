<?php
/**
 * 产品controller.
 * User: PengFan
 * Date: 2018/12/12
 * Time: 14:53
 */
namespace api\modules\v1\controllers;

use api\modules\v1\models\Comment;
use Yii;
use api\behaviors\TokenBehavior;
use api\modules\Base;

class ProductController extends Base
{


    //model类
    public $modelClass = 'api\modules\v1\models\ProductActions';


    /**
     * 检测是否有权访问
     */
    public function behaviors()
    {
        return [
            'TokenBehavior' => [
                'class' => TokenBehavior::className(),
                'tokenParam' => 'accessToken',
            ],
        ];
    }


    /**
     * 商品详情
     */
    public function actionProDetails()
    {
        $params = $this->params;
        $headerParam = Yii::$app->request->headers->get('accessToken');
        $headerParam = $headerParam ? ['accessToken' => $headerParam] : [];
        $lastPrams = array_merge($params,$headerParam);
        $model = new $this->modelClass(['scenario' => 'pro-details']);
        $loadParam = $model->load($lastPrams,'');
        if($loadParam && $model->validate())
        {
            $data = $model->productDetails();
            if(!$data) return $this->returnData(0,'数据为空');
            return $this->returnData(200,'获取成功',$data);
        }
        return $this->returnRuleErr($model);
    }




    /**
     * 门店评论
     */
    public function actionComment()
    {
        $params = $this->params;
        $model = new Comment(['scenario' => 'comments']);
        $loadParam = $model->load($params,'');
        if($loadParam && $model->validate())
        {
            $data = $model->getComments($params);
            if(!$data) return $this->returnData(0,'数据为空');
            $comment = $model->unification($data);
            $last_data = $model->commentPro($comment);
            return $this->returnData(200,'获取成功',$last_data);
        }
        return $this->returnRuleErr($model);
    }



    /**
     * 各类型产品列表数据
     */
    public function actionProList()
    {
        $params = $this->params;
        $model = new $this->modelClass(['scenario' => 'pro-list']);
        $loadParam = $model->load($params,'');
        if($loadParam && $model->validate())
        {
            $proData = $model->proData();
            if(!$proData)return $this->returnData(0,'数据为空');
            return $this->returnData(200,'获取成功',$proData);
        }
        return $this->returnRuleErr($model);
    }




    /**
     * 记录浏览记录
     */
    public function actionCreateHistory()
    {

    }






}