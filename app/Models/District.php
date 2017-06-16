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

class District extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'district';

    protected $primaryKey = 'id';

    public function nameGetId($name,$level)
    {
        $district = DB::table('district')->where(['name'=>$name,'level'=>$level])->first(['id']);
        return $district ? $district->id : 0;
    }

    public function get($id)
    {
        $district = DB::table('district')->find($id);
        return $district ? $district->name : '';
    }
    
}