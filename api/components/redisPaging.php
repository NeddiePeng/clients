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
        //$this->instance->flushDB();
        //exit;
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
            $data = $this->instance->hGet($data_key,$val);
            $returnDara[] = unserialize($data);
        }
        return $returnDara;
    }


    //分页数据前缀
    public $prefix = 'limit_';

    //缓存数据前缀
    public $cachePrefix = 'cache_';

    //缓存order
    public $cacheOrder = 'id';


    /**
     * 写入分页数据
     *
     * @param   array   $data       需要写入cache的数据
     * @param   string  $cacheKey   缓存key
     * @param   string  $limitKey   分页key
     * @return  boolean
     */
    public function WriteRedis($data, $cacheKey, $limitKey)
    {
        if(!$data || !is_array($data)) return false;
        $limitKey .= $this->prefix.$limitKey;
        $cacheKey .= $this->cachePrefix.$cacheKey;
        foreach ($data as $k => $v)
        {
            $this->instance->zAdd($limitKey,$v[$this->cacheOrder],$v[$this->cacheOrder]);
            //序列化
            $this->instance->hSet($cacheKey,$v[$this->cacheOrder],serialize($v));
        }
        return true;
    }



    /**
     * 读取分页数据
     *
     * @param   string   $cacheKey   缓存key
     * @param   string   $limitKey   分页key
     * @return  array | null
     */
    public function ReadCache($cacheKey,$limitKey)
    {
        if(!is_numeric($this->page) || !is_numeric($this->limit)) return null;
        $limit_s = ($this->page-1) * $this->limit;
        $limit_e = ($limit_s + $this->limit) - 1;
        $limitData = $this->instance->zRange($limitKey,$limit_s,$limit_e);
        if(!$limitData) return null;
        $returnDara = [];
        foreach ($limitData as $k => $val)
        {
            $data = $this->instance->hGet($cacheKey, $val);
            //反序列化
            $returnDara[] = unserialize($data);
        }
        return $returnDara;
    }






}