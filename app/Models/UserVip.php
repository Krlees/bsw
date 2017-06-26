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

class UserVip extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'user_vip';

    protected $primaryKey = 'uer_id';

    public function checkExpires($labelId,$userId,$nowTime=false)
    {
        if(!$userId)
            return false;

        if(!$nowTime){
            $nowTime = date('Y-m-d H:i:s');
        }

        $count = DB::table($this->table)->where('label_id',$labelId)->where('user_id',$userId)->where('created_at','>=',$nowTime)->where('expires_at','<=',$nowTime)->count();
        return $count ? true : false;
    }

    public function get($userId)
    {
        return DB::table($this->table)->find($userId);
    }
    
}