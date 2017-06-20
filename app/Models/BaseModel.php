<?php
/**
 * Created by PhpStorm.
 * User: liguanke
 * Date: 17/6/20
 * Time: 下午3:22
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use DB;
class BaseModel extends Model
{
    public function ajaxData($tableName,$where=false,$searchField='name',$fields=['*'])
    {
        $where = $where ?: [];
        if (isset($param['search']))
            $where[] = [$searchField, 'like', '%' . $param['search'] . '%'];

        $sort = array_get($param, 'sort') ?: $this->getKeyName();
        $order = array_get($param, 'order', 'desc');
        $rows = DB::table($tableName)->where($where)->orderBy($sort, $order)->offset(array_get($param, 'offset', 0))->limit(array_get($param, 'limit', 10))->get($fields);
        $rows = obj2arr($rows);
        $total = DB::table($tableName)->where($where)->count();

        return compact('rows', 'total');
    }


}