<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use DB;

class UserSign extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'user_sign';

    protected $primaryKey = 'id';

    public function create($data)
    {
        try{
            $id = DB::table($this->table)->insertGetId($data);

            return $id ?: false;
        }
        catch (\Exception $e){
            return false;
        }
    }
    
}