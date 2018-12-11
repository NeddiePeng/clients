<?php
/**
 * 一些项目中需要的条件查询.
 * User: Pengfan
 * Date: 2018/12/7
 * Time: 11:13
 */
namespace api\components;

use api\modules\Base;
use yii\base\Component;

class searchWhere extends Component
{



    /**
     * 搜索
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
            if($param['one_sort'] == 'hot') return ['and', ['=','top_sort',$param['top_sort']],];
            $where = [
                'and',
                ['=','one_sort',$param['sort_two']]
            ];
            return $where;
        }
        $where = [
            'and',
            ['=','top_sort',$param['top_sort']],
            ['=','one_sort',$param['sort_two']]
        ];
        return $where;
    }




    /**
     * 订单条件
     *
     * @param    array    $param    参数集合
     * @return   array
     */
    public function order($param)
    {
        switch ($param['nav_type'])
        {
            case 'all':
                $where = "1=1";
                break;
            case 0:
                $where = [
                    'and',
                    ['=','order.is_pay_success',2]
                ];
                break;
            case 1:
                $where = [
                    'and',
                    ['=','code.is_use',2]
                ];
                break;
            case 2:
                $where = [
                    'and',
                    ['=','order.is_comment',2]
                ];
                break;
            case 3:
                $where = [
                    'and',
                    ['<>','order.is_refund',0],
                    ['=','is_pay_success',1]
                ];
                break;
            default:
                $where = "1=1";
        }
        if(!isset($param['sort_id'])) return $where;
        $sortWhere = $this->orderSort($param['sort_id']);
        if(!$sortWhere) return $where;
        $where = array_push($where,$sortWhere);
        return $where;
    }




    /**
     * 订单分类
     *
     * @param     int    $sort_id    分类id
     * @return    array | null
     */
    public function orderSort($sort_id)
    {
        if(!$sort_id) return null;
        $where = ['=','order.sort_id',$sort_id];
        return $where;
    }




    /**
     * 门店分类条件
     *
     * @param    array    $params    搜索参数集合
     * @return   array
     */
    public function storeSort($params)
    {
        $where = [
            ['=','top_sort',$params['sort_id']]
        ];
        if(!$params['sort_two']) return $where;
        array_push($where,['=','sort_two',$params['sort_two']]);
        return $where;
    }



    /**
     * 门店地区筛选
     *
     * @param    array    $params    参数集合
     * @return   array | null
     */
    public function storeAddr($params)
    {
        $scope = $this->Range($params,2000);
        if(!isset($params['addr']))
        {
            return $scope;
        }
        #TODO:addr数据格式   【{'addr_top':'hot','addr_two':12}】
        if(is_array($params['addr']))
        {
            if($params['addr']['addr_top'] === 'nearby')
            {
                if(!isset($params['addr']['addr_two'])) return $scope;
                switch ($params['addr']['addr_two'])
                {
                    case 0:
                        return $scope;
                        break;
                    case 1:
                        $scope = $this->Range($params,1000);
                        return $scope;
                        break;
                    case 2:
                        $scope = $this->Range($params,3000);
                        return $scope;
                        break;
                    case 3:
                        $scope = $this->Range($params,5000);
                        return $scope;
                        break;
                    case 4:
                        $scope = $this->Range($params,10000);
                        return $scope;
                        break;
                    case 5:
                        return [['=',1,1]];
                        break;
                    default:
                        return $scope;
                }
            }
            elseif ($params['addr']['addr_top'] === 'hot')
            {
                $where = [
                    ['=','area',$params['addr']['addr_two']]
                ];
                return $where;
            }
            else
            {
                $where = [
                    ['=','city',$params['addr']['addr_top']],
                    ['=','area',$params['addr']['addr_two']]
                ];
                return $where;
            }
        }
        return [];
    }





    /**
     * 范围
     *
     * @param     array    $params   参数集合
     * @param     int      $range    范围
     * @return    array
     */
    public function Range($params,$range)
    {
        $scope = Base::calcScope($params['lat'],$params['lng'],$range);
        $where = [
            ['>=','lat',$scope['minLat']],
            ['<=','lat',$scope['maxLat']],
            ['>=','lng',$scope['minLng']],
            ['<=','lng',$scope['maxLng']]
        ];
        return $where;
    }




    /**
     * 其他筛选
     *
     * @param    array   $params   参数集合
     * @return   array
     */
    public function storeOther($params)
    {

    }






}