<?php
/**
 * Created by PhpStorm.
 * User: liguanke
 * Date: 17/6/15
 * Time: 下午1:22
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use DB;

class UserFollow extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'user_follow';

    protected $primaryKey = 'id';

    /**
     * @param $user_id
     * @param $follow_id
     * @return bool
     */
    public function create($user_id, $follow_id)
    {
        $created_at = date('Y-m-d H:i:s');
        try {
            $id = DB::table($this->table)->insertGetId(compact('user_id', 'follow_id', 'created_at'));
            return $id ?: false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $userId
     * @param $followId
     */
    public function del($userId, $followId)
    {
        try {
            $affected = DB::table($this->table)->where('user_id', $userId)->where('follow_id', $followId)->delete();
            return $affected ? true : false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 获取我关注的人
     * @param $userId
     * @return array
     */
    public function getFollow($userId)
    {
        $result = DB::table($this->table)->where('user_id', $userId)->get();
        return obj2arr($result);
    }

    /**
     * 获取我的粉丝【关注我的人】
     */
    public function getFans($followId)
    {
        $result = DB::table($this->table)->where('follow_id', $followId)->get();
        return obj2arr($result);
    }

    public function check($userId, $followId)
    {
        $result = DB::table($this->table)->where('user_id', $userId)->where('follow_id', $followId)->count();
        return $result ? true : false;
    }

}