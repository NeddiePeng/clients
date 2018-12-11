<?php
/**
 * 点赞&分享.
 * User: Pengfan
 * Date: 2018/12/10
 * Time: 16:55
 */
namespace api\components;

use Yii;
use api\extend\RedisCache;
use yii\base\Component;

class likeRedis extends Component
{


    //redis数据库
    public $db_id = 5;

    //实例
    private $instance = null;

    //初始化
    public function init()
    {
        parent::init();
        $this->instance = RedisCache::getInstance(['host' => '127.0.0.1'],['db_id' => $this->db_id]);
    }



    /**
     * 获取缓存
     *
     * @param    int    $s_id    门店id
     * @param    int    $uid     用户id
     * @param    string $type    类型
     * @return   array | null
     */
    public function getStoreLike($s_id, $type = 'like', $uid = 0)
    {
        if($type !== 'like') $type = "share";
        $key = "like:store:$s_id:counts";
        $counts = $this->instance->get($key);
        if(!$uid) return ['count' => $counts,"is_$type" => 0];
        $like_user_key = "$type:store:$uid:user";
        $is_like = $this->instance->hGet($like_user_key,$s_id);
        if(!$is_like) return ['count' => $counts,"is_$type" => 0];
        return ['count' => $counts,"is_$type" => 1];
    }



    /**
     * 写入点赞缓存
     *
     * @param    int    $s_id    门店id
     * @param    int    $uid     用户uid
     * @param    string $type    类型
     * @return   boolean
     */
    public function setLikeRedis($s_id, $type = 'like', $uid)
    {
        if($type !== 'like') $type = "share";
        $key = "$type:store:$s_id:counts";
        $oldCount = $this->instance->get($key);
        $nowCount = (int)$oldCount + 1;
        $this->instance->set($key,$nowCount);
        $like_user_key = "$type:store:$uid:user";
        $data = [
            's_id' => $s_id,
            'uid' => $uid,
            'time' => time()
        ];
        $this->instance->hSet($like_user_key,$s_id,serialize($data));
        $cacheKey = "$type:store";
        $this->instance->zAdd($cacheKey,time(),serialize($data));
        return true;
    }




    /**
     * 数据同步到mysql
     *
     * @return   array | boolean
     */
    public function synchro()
    {
        $key = "like:store";
        $likeData = $this->instance->zRange($key, 0, -1);
        $shareKey = "share:store";
        $shareData = $this->instance->zRange($shareKey, 0, -1);
        if(!$likeData && !$shareData) return false;
        $last_data = [];
        if($likeData)
        {
            foreach ($likeData as $key => $val)
            {
                $last_data['likeData'][] = unserialize($val);
            }
        }
        else
        {
            $last_data['likeData'] = null;
        }
        if($shareData)
        {
            foreach ($shareData as $key => $val)
            {
                $last_data['shareData'][] = unserialize($val);
            }
        }
        else
        {
            $last_data['shareData'] = null;
        }
        return $last_data;
    }







    //后续操作【测试】
    public function actionIndex()
    {
        $get = $_GET;
        $rs = ['code' => 0, 'msg' => 'ok', 'data' => true];
        set_time_limit(0);
        ob_end_clean();
        header("Content-Type: application/json;charset=utf-8");
        ob_start();
        // 输出结果到前端
        echo json_encode($rs);
        $size = ob_get_length();
        header("Content-Length: $size");
        ob_end_flush();
        flush();
        // yii或yaf默认不会立即输出，加上此句即可（前提是用的fpm）
        if (function_exists("fastcgi_finish_request"))
        {
            // 响应完成, 立即返回到前端,关闭连接
            fastcgi_finish_request();
        }
        sleep(2);
        // 在关闭连接后，继续运行php脚本
        ignore_user_abort(true);
        set_time_limit(0);
        Yii::$app->session['test'] = $get;
    }







}