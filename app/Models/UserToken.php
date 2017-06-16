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

class UserToken extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'user_token';

    protected $primaryKey = 'id';

    public function getForUserId($id, $type = 1)
    {
        $tokens = DB::table($this->table)->where('user_id', $id)->where('type', $type)->first(['token']);
        if (empty($tokens))
            return false;

        return $tokens->token;
    }

    public function create($user_id, $token, $type = 1)
    {
        return DB::table($this->table)->insert(compact('user_id', 'token', 'type'));
    }

    public function updateData($user_id, $token, $type=1)
    {
        return DB::table($this->table)->where('user_id', $user_id)->where('type', $type)->update(['token' => $token]);
    }

}