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

class Adv extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'adv';

    protected $primaryKey = 'id';

    public function advPositionTb()
    {
        return 'adv_position';
    }

    public function get($classify)
    {
        $advPosition = DB::table($this->advPositionTb())->where('classify',$classify)->first(['id']);
        return DB::table($this->table)->where('position_id',$advPosition->id)->get();
    }

    public function getList()
    {
        return DB::table($this->advPositionTb())->get();
    }



}