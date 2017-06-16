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

class UserLoginRecord extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'user_login_record';

    protected $primaryKey = 'id';

    /**
     * 记录用户每次登录的位置
     * @param $user_id
     * @param $lng
     * @param $lat
     * @return bool
     */
    public function create($user_id,$lng,$lat)
    {
        $data = compact('user_id','lat','lng');
        $data['created_at'] = date('Y-m-d H:i:s');

        try{
            $id = DB::table($this->table)->insertGetId($data);

            return $id ?: false;
        }catch (\Exception $e){
            return false;
        }
    }



}