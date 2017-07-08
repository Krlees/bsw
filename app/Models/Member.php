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

class Member extends Model
{
    use BaseModelTraits;

    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'user';

    protected $primaryKey = 'id';

    public function get($id)
    {
        $result = DB::table($this->table)->find($id);
        $userLevel = DB::table('user_level')->find($result->user_level_id);
        if ($userLevel)
            $result->level = $userLevel->name;

        return obj2arr($result);
    }

    /**
     * 获取用户列表
     * @param $page
     * @param $limit
     * @param bool $where
     * @param int $status
     * @return array
     */
    public function getList($page, $limit, $where = [], $status = 1)
    {
        $db = DB::table($this->table)->where('status', $status);
        if (!empty($where)) {
            $db->where($where);
        }
        $result = $db->offset($page * $limit)->limit($limit)->get();

        return obj2arr($result);
    }

    /**
     * 创建用户
     */
    public function create($data)
    {
        if (!array_has($data, 'nickname')) {
            $data['nickname'] = $data['username'];
        }
        $data['tel'] = "";
        if (!array_has($data, 'tel'))
            $data['tel'] = $data['username'];

        try {
            $id = DB::table($this->table)->insertGetId($data);
            if ($id) {
                DB::table('user_wallet')->insert([
                    'user_id' => $id,
                    'pwd' => '',
                    'money' => 0,
                ]);
                return $id;
            }


            return false;
        } catch (\Exception $e) {
            return false;
        }

    }

    public function updateData($id, $data)
    {
        try {
            $b = DB::table($this->table)->where('id', $id)->update($data);

            return $b !== false ? true : false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function oauthCreate($data)
    {

    }

    public function forgetPwd($mobile, $pwd)
    {
        return DB::table($this->table)->where('username', $mobile)->update(['pwd' => $pwd]);
    }

    public function resetPwd($id, $pwd)
    {
        return DB::table($this->table)->where('id', $id)->update(['password' => $pwd]);
    }

    /**
     * 检测手机号码是否存在
     * @param $mobile
     */
    public function checkMobileExists($mobile)
    {
        $count = DB::table($this->table)->where('username', $mobile)->count();
        if ($count) {
            return true;
        }

        return false;
    }

    /**
     * 检测登录信息
     * @param $username
     * @param $pwd
     * @return array|bool|null|\stdClass
     */
    public function checkLogin($username, $pwd)
    {
        $users = DB::table($this->table)->where('username', $username)->first();
        if (empty($users)) {
            return false;
        } elseif ($users->password != password($pwd)) {
            return false;
        }

        $userLevel = DB::table('user_level')->find($users->user_level_id);
        if ($userLevel)
            $users->level = $userLevel->name;

        return $users;
    }

    public function checkOpenID($openid)
    {
        $count = DB::table($this->table)->where('openid', $openid)->count();

        return $count > 0 ? true : false;
    }

    public function getForOpenID($openid)
    {
        $result = DB::table($this->table)->where('openid', $openid)->first();

        return $result;
    }

}