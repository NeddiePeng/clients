<?php
/**
 * 评论.
 * User: PengFan
 * Date: 2018/12/12
 * Time: 20:13
 */
namespace api\modules\v1\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

class Comment extends ActiveRecord
{

    //其他字段
    public $page = 1;
    public $nav_type;
    public $s_id;

    //数据表
    public static function tableName()
    {
        return 'pay_store_comment';
    }


    //数据验证
    public function rules()
    {
        return [
            [['page','s_id','nav_type'],'required','on' => 'comments']
        ];
    }


    /**
     * 商品评论
     *
     * @return array | null
     */
    public function getComments($params)
    {
        $where = Yii::$app->where->select('comments',$params);
        $per = ($this->page - 1) * 10;
        $data = (new Query())
                ->select("*")
                ->from(static::tableName() . ' as comment')
                ->leftJoin("pay_user as user",'user.id=comment.uid')
                ->where(['comment.x_id' => $this->s_id])
                ->andWhere(['comment.status' => 1,'comment.is_report' => 2])
                ->andWhere(['comment.be_reply_id' => 0])
                ->andWhere($where)
                ->offset($per)
                ->limit(10)
                ->all();
        return $data;
    }



    /**
     * 评论商品
     *
     * @param   array    $comments   评论
     * @return  array | null
     */
    public function commentPro($comments)
    {
        foreach ($comments as $key => $val)
        {
            switch ($val['type'])
            {
                case 1:
                    $proData = Product::vou($val['pro_id']);
                    $lastPro = $proData ? $proData['vouchers_name'] : "";
                    continue;
                case 2:
                    $proData = Product::group($val['pro_id']);
                    $lastPro = $proData ? $proData['group_name'] : "";
                    continue;
                case 3:
                    $proData = Product::check($val['pro_id']);
                    $lastPro = StoreOther::instance()->getDiscount($proData['dis_id']);
                    $lastPro = $lastPro ? '买单'.$lastPro.'折' : "买单";
                    continue;
                case 4:
                    $proData = Product::shopping($val['pro_id']);
                    $lastPro = $proData ? $proData['dishes_name'] : "";
                    continue;
                default:
                    $lastPro = '';
            }
            $comments[$key]['proName'] = $lastPro;
        }
        return $comments;
    }




    /**
     * 数据格式化
     *
     * @param   array   $data   评论数据
     * @return  array
     */
    public function unification($data)
    {
        $last_data = null;
        foreach ($data as $key => $val)
        {
            $replayData = $this->replayData($val['id']);
            $replayList = null;
            if($replayData){
                foreach ($replayData as $k => $v)
                {
                    $replayList[] = [
                        'nickname' => "商家回复",
                        'comment_text' => $v['comment_text'],
                        'time' => date('Y.m.d',$v['add_time'])
                    ];
                }
            }
            $commentImg = $this->commentImg($val['id']);
            $last_data[] = [
                'id' => $val['id'],
                'pro_id' => $val['pro_id'],
                'type' => $val['type'],
                'time' => date('Y.m.d',$val['add_time']),
                'comment_text' => $val['comment_text'],
                'Star_num' => $val['Star_num'],
                'userData' => [
                    'nickname' => $val['nickname'],
                    'header_img' => $val['header_img']
                ],
                'is_be_reply' => $val['is_be_reply'],
                'imgData' => $commentImg,
                'replyData' => $replayList
            ];
        }
        return $last_data;
    }



    /**
     * 评论回复
     *
     * @param   int    $id    评论id
     * @return  array | null
     */
    public function replayData($id)
    {
        $data = (new Query())
                ->select("*")
                ->from(static::tableName())
                ->where(['be_reply_id' => $id])
                ->andWhere('is_report != 1 and is_report != 3 and is_report != 4')
                ->andWhere(['is_bus_reply' => 1])
                ->all();
        return $data;
    }



    /**
     * 评论图片
     *
     * @param   int   $c_id   评论id
     * @return  array | null
     */
    public function commentImg($c_id)
    {
        $data = (new Query())
                ->select("*")
                ->from("pay_comment_img")
                ->where(['comment_id' => $c_id])
                ->all();
        return $data;
    }


}