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

class Label extends BaseModel
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'label';

    protected $primaryKey = 'id';

    public function getByName($id)
    {
        $result = DB::table($this->table)->find($id,['name']);
        return $result ? $result->name : '';
    }
    
}