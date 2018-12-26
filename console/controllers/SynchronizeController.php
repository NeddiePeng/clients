<?php
/**
 * 数据同步.
 * User: Pengfan
 * Date: 2018/12/20
 * Time: 15:41
 */
namespace console\controllers;

use yii\helpers\Console;
use yii\web\Controller;

class SynchronizeController extends Controller
{

    public $enableCsrfValidation = false;

    //shell
    protected $shell = [
        'killSphinx' => 'pkill searchd',
        //索引启动服务
        'startSphinx' => "/usr/local/sphinx2/bin/indexer --config /usr/local/sphinx2/etc/sphinx.conf --all",
        'searchSphinx' => '/usr/local/sphinx2/bin/searchd --config /usr/local/sphinx2/etc/sphinx.conf',
        //增量索引
        'addSphinx' => '/opt/server/sphinx/bin/indexer test1stemmed --rotate',
        //合并索引
        'mergeSphinx' => '/opt/server/sphinx/bin/indexer --merge test1 test1stemmed --rotate'
    ];


    /**
     * sphinx索引同步
     */
    public function actionSphinx()
    {
        $params = \Yii::$app->request->post('name');
        $shell = $this->shell[$params];
        Console::output("数据同步中...");
        echo '<pre>';
        system($shell, $status);
        echo '</pre>';
        if($status) Console::output("数据同步失败!");
        Console::output("数据同步成功!");

    }



}