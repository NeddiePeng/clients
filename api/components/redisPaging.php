<?php
/**
 * redis数据分页.
 * User: Pengfan
 * Date: 2018/12/5
 * Time: 16:06
 */
namespace api\components;

use api\extend\RedisCache;
use yii\base\Component;

class redisPaging extends Component
{

    //数据页码
    public $page = 1;

    //数据显示条数
    public $limit = 12;

    //redis数据库
    public $db_id = 0;

    //实例
    private $instance = null;

    //初始化
    public function init()
    {
        parent::init();
        $this->instance = RedisCache::getInstance(['host' => '127.0.0.1'],['db_id' => $this->db_id]);
    }



    /**
     * 分页存数据
     *
     * @param   string    $limit_key    缓存key
     * @param   string    $data_key     数据key
     * @param   array     $data         缓存数据
     * @param   string    $key          数据键
     * @return  boolean
     */
    public function setCache($limit_key, $data_key, $data, $key)
    {
        if($data)
        {
            foreach ($data as $k => $v)
            {
                $this->instance->zAdd($limit_key,$v[$key],$v[$key]);
                $this->instance->hSet($data_key,$v[$key],serialize($v));
            }
        }
        return true;
    }



    /**
     * 获取分页数据
     *
     * @param   string    $limit_key    缓存key
     * @param   string    $data_key     数据key
     * @param   string    $key         数据键
     * @return  boolean | array
     */
    public function getCache($limit_key, $data_key, $key)
    {
        if(!is_numeric($this->page) || !is_numeric($this->limit)) return false;
        $limit_s = ($this->page-1) * $this->limit;
        $limit_e = ($limit_s + $this->limit) - 1;
        $limitData = $this->instance->zRange($limit_key,$limit_s,$limit_e);
        if(!$limitData) return false;
        $returnDara = [];
        foreach ($limitData as $k => $val)
        {
            $data = $this->instance->hGet($data_key,$val[$key]);
            $returnDara[] = unserialize($data);
        }
        return $returnDara;
    }






}