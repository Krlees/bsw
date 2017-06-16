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

class UserWallet extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'user_wallet';

    protected $primaryKey = 'uesr_id';

    public function userWalletRecordTb()
    {
        return 'user_wallet_record';
    }

    public function get($userId)
    {
        $result = DB::table($this->table)->where('user_id',$userId)->first();

        return obj2arr($result);
    }

    
}