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
        if ($where)
            $db->where($where);

        $result = $db->offset($page * $limit)->limit($limit)->get();

        return obj2arr($result);

    }

    public function ajaxData($param, $where = [], $field = ['*'])
    {
        if (isset($param['search'])) {
            $where[] = ['u.username', 'like', '%' . $param['search'] . '%', 'OR'];
            $where[] = ['b.title', 'like', '%' . $param['search'] . '%', 'OR'];
        }

        $sort = array_get($param, 'sort') ?: $this->getKeyName();
        $order = array_get($param, 'order', 'desc');
        $rows = DB::table($this->table . ' as a')->join('transaction as b', 'a.transaction_id', '=', 'b.id')
            ->join('user as u', 'u.id', '=', 'b.user_id')
            ->where($where)
            ->orderBy($sort, $order)->offset(array_get($param, 'offset', 0))->limit(array_get($param, 'limit', 10))
            ->get(['a.*', 'b.title', 'u.username']);

        $total = DB::table($this->table . ' as a')->join('transaction as b', 'a.transaction_id', '=', 'b.id')
            ->where('b.channel_id', 3)->count();

        return compact('rows', 'total');

    }
}