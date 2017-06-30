<?php
/**
 * Created by PhpStorm.
 * User: liguanke
 * Date: 17/6/15
 * Time: 下午1:22
 */

namespace App\Models;


use App\Traits\BaseModelTraits;
use Illuminate\Database\Eloquent\Model;
use DB;

class Comment extends Model
{
    use BaseModelTraits;

    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'comment';

    protected $primaryKey = 'id';

    public function commentImgTb()
    {
        return 'comment_img';
    }

    /**
     * 获取评论列表
     * @param $page
     * @param $limit
     * @param $where
     * @return array
     */
    public function getList($page, $limit, $where)
    {
        $db = DB::table($this->table);
        if($where)
            $db->where($where);

        $result = $db->offset($page*$limit)->limit($limit)->get();

        return obj2arr($result);

    }
}