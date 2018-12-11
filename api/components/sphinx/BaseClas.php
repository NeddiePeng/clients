<?php
/**
 * 全文检索类.
 * User: Pengfan
 * Date: 2018/12/10
 * Time: 18:30
 */
namespace api\components\sphinx;

use yii\base\Component;

class BaseClas extends Component
{

    //sphinx连接
    protected $client;

    //关键词
    protected $keywords;

    //数据库链接
    private static $dbConnection = null;

    //数据库config
    public static $database = [
        'host' => '127.0.0.1',
        'field' => 'id',
        'table' => 'user',
        'db_name' => 'test',
        'pwd' => 'root',
        'user' => 'root'
    ];

    //默认词库路径
    private $def_dict_path = './dict.utf8.xdb';

    //自定义词库路径
    private $custom_dict_path = 'D:/files/c.txt';

    //默认规则
    private $def_rule = './rules.utf8.ini';


    /**
     * 初始化
     *
     * @param     array    $options   sphinx参数
     * @param     array    $database  数据库
     */
    public function Initialization($options = array(),$database = array())
    {
        //sphinx默认配置
        $defaults = array(
            'query_mode' => SPH_MATCH_EXTENDED2,
            'sort_mode' => SPH_SORT_EXTENDED,
            'ranking_mode' => SPH_RANK_PROXIMITY_BM25,
            'field_weights' => array(),
            'max_matches' => 1000,
            'snippet_enabled' => true,
            'snippet_index' => 'items',
            'snippet_fields' => array(),
        );

        $this->options = array_merge($defaults, $options);
        self::$database = $database;
        $this->client = new \SphinxClient();
        $this->client->setMatchMode($this->options['query_mode']);
        if ($this->options['field_weights'] !== array()) {
            $this->client->setFieldWeights($this->options['field_weights']);
        }
    }




    /**
     * 中文分词
     * @param      string    $keywords   关键词
     * @return     string
     */
    public function wordSplit($keywords = '')
    {
        //默认词库路径
        $fpath = ini_get('scws.default.fpath');
        $so = scws_new();
        //字符集
        $so->set_charset('utf-8');
        $so->add_dict($fpath . $this->def_dict_path);
        //自定义词库
        if($this->custom_dict_path)
        {
            $so->add_dict($this->custom_dict_path, SCWS_XDICT_TXT);
        }
        $so->set_rule($fpath . $this->def_rule);
        $so->set_ignore(true);
        $so->set_multi(false);
        $so->set_duality(false);
        $so->send_text($keywords);
        $words = [];
        $results =  $so->get_result();
        foreach ($results as $res) {
            $words[] = '(' . $res['word'] . ')';
        }
        $words[] = '(' . $keywords . ')';
        return join('|', $words);
    }




    /**
     * 结果查询
     * @param     string    $keywords    关键字
     * @param     string    $offset      偏移量
     * @param     string    $limit       长度
     * @param     string    $index
     * @return    array
     */
    public function query($keywords, $offset = 0,$limit = 10, $index = '*')
    {
        $this->keywords = $keywords;
        $max_matches = $limit > $this->options['max_matches'] ? $limit : $this->options['max_matches'];
        $this->client->setLimits($offset, $limit, $max_matches);
        $query_results = $this->client->query($keywords, $index);
        if ($query_results === false) {
            $this->log('error:' . $this->client->getLastError());
        }
        $res = [];
        if ( empty($query_results['matches']) ) {
            return $res;
        }
        $res['total'] = $query_results['total'];
        $res['total_found'] = $query_results['total_found'];
        $res['time'] = $query_results['time'];
        $doc_ids = array_keys($query_results['matches']);
        unset($query_results);
        $res['data'] = $this->fetch_data($doc_ids);
        if ($this->options['snippet_enabled']) {
            $this->buildExcerptRows($res['data']);
        }

        return $res;
    }


    /**
     * 数据提取
     * @param   array  $doc_ids
     * @return  array
     **/
    protected function fetch_data($doc_ids) {
        $table = self::$database['table'];
        $field = self::$database['field'];
        $selectField = isset(self::$database['selectField']) ? self::$database['selectField'] : '*';
        $ids = implode(',', $doc_ids);
        $queries = self::getDBConnection()->query("SELECT {$selectField} FROM {$table} WHERE {$field} in ($ids)", \PDO::FETCH_ASSOC);
        return iterator_to_array($queries);
    }


    /**
     * 创建高亮文本摘要创建高亮文本摘要
     * @param   array   $rows
     * @return  array
     **/
    protected function buildExcerptRows(&$rows) {
        $options = array(
            'before_match' => '<b style="color:red">',
            'after_match'  => '</b>',
            'chunk_separator' => '...',
            'limit' => 256,
            'around'  => 3,
            'exact_phrase' => false,
            'single_passage' => true,
            'limit_words' => 5,
        );
        $scount = count($this->options['snippet_fields']);
        foreach ($rows as &$row) {
            foreach ($row as $fk => $item) {
                if (!is_string($item) || ($scount && !in_array($fk, $this->options['snippet_fields']))) continue;
                $item = preg_replace('/[\r\t\n]+/', '', strip_tags($item));
                $res = $this->client->buildExcerpts(array($item), $this->options['snippet_index'], $this->keywords, $options);
                $row[$fk] = $res === false ? $item : $res[0];
            }
        }
        return $rows;
    }



    /**
     * custom sorting
     * @return    bool
     */
    public function setSortBy($sortBy = '', $mode = 0) {
        if ($sortBy) {
            $mode = $mode ?: $this->options['sort_mode'];
            $this->client->setSortMode($mode, $sortBy);
        } else {
            $this->client->setSortMode(SPH_SORT_RELEVANCE);
        }
    }




    /**
     * 数据库连接
     *
     * @return resource
     */
    private static function getDBConnection() {
        $host = self::$database['host'];
        $db_name = self::$database['db_name'];
        $dsn = "mysql:host={$host};dbname={$db_name}";
        $user = self::$database['user'];
        $pass = self::$database['pwd'];
        if (!self::$dbConnection) {
            try {
                self::$dbConnection = new \PDO($dsn, $user, $pass);
            } catch (\PDOException $e) {
                die('Connection failed: ' . $e->getMessage());
            }
        }
        return self::$dbConnection;
    }



    /**
     * magic methods
     */
    public function __call($method, $args) {
        $rc = new \ReflectionClass('SphinxClient');
        if ( !$rc->hasMethod($method) ) {
            throw new \Exception('invalid method :' . $method);
        }
        return call_user_func_array(array($this->client, $method), $args);
    }




}



$key = "这是测试";
$sphinx_config = [
    'snippet_fields' => ['hot_terms','search_num'],
    'field_weights' => ['hot_terms' => 20,'search_num' => 20]
];
$pdo = [
    'host' => '127.0.0.1',
    'field' => 'id',
    'table' => 'pay_hot_terms',
    'db_name' => 'payment_master',
    'pwd' => 'root',
    'user' => 'root'
];
$s = new searchClass($sphinx_config,$pdo);
$s->setSortBy(SPH_SORT_EXTENDED);
$words = $s->wordSplit("{$key}");
$res = $s->query($words, 0, 10, 'users');
$data['listData'] = $res;