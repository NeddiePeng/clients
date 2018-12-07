<?php
/**
 * 一些项目中需要的条件查询.
 * User: Pengfan
 * Date: 2018/12/7
 * Time: 11:13
 */
namespace api\components;

use yii\base\Component;

class searchWhere extends Component
{



    /**
     * 添加一个搜索
     *
     * @param    string    $position   位置
     * @param    array     $param      搜索条件
     * @return   array
     */
    public function select($position,$param)
    {
        return $this->$position($param);
    }


    /**
     * 附近搜索条件
     *
     * @param    array    $param   搜索参数
     * @return   array
     */
    public function nearby($param)
    {
        if($param['top_sort'] == 'all')
        {
            if($param['sort_one'] == 'hot') return ['and', ['=','top_sort',$param['top_sort']],];
            $where = [
                'and',
                ['=','sort_one',$param['sort_one']]
            ];
            return $where;
        }
        $where = [
            'and',
            ['=','top_sort',$param['top_sort']],
            ['=','sort_one',$param['sort_one']]
        ];
        return $where;
    }







}