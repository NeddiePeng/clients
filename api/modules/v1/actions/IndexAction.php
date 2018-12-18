<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/17
 * Time: 11:00
 */
namespace api\modules\v1\actions;


use yii\base\Action;
use \Closure;

class IndexAction extends Action
{


    public $data = [];


    public $viewFile = null;


    public function run()
    {
        $data = $this->data;
        if($data instanceof Closure)
        {
            $data = call_user_func($this->data);
        }
        $this->viewFile === null && $this->viewFile = $this->id;
        return $this->controller->render($this->viewFile, $data);
    }


}