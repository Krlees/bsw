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

class NavLabel extends Model
{
    use BaseModelTraits;

    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'nav_label';

    protected $primaryKey = 'id';

    public function getByName($id)
    {
        $result = DB::table($this->table)->find($id, ['name']);
        return $result ? $result->name : '';
    }

    public function nameById($name)
    {
        $result = DB::table($this->table)->where('name', $name)->first(['id']);
        return $result ? $result->id : 0;
    }

}