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
    public function ajaxData($tableName, $param, $where = false, $searchField = 'name', $fields = ['*'])
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

    public function createData($tbName, $data)
    {
        try {
            $id = DB::table($tbName)->insertGetId($data);

            return $id ?: false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateData($tbName, $id, $data)
    {
        try {
            $b = DB::table($tbName)->where('id', $id)->update($data);

            return $b !== false ? true : false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function delData($tbName, $ids)
    {
        try {
            $b = DB::table($tbName)->whereIn('id', $ids)->delete();

            return $b !== false ? true : false;
        } catch (\Exception $e) {

        }
    }

    public function getList($tbName, $page=0, $limit = 20, $where = null)
    {
        $where = $where ?: [];
        $result = DB::table($tbName)->offset($page * $limit)->limit($limit)->where($where)->get();
        return obj2arr($result);
    }

    public function getInfo($tbName, $id)
    {
        return DB::table($tbName)->find($id);
    }

    public function getOnlyField($tbName, $id, $field)
    {
        $res = DB::table($tbName)->find($id, [$field]);
        if (empty($res)) {
            return '';
        }

        return $res->{$field};
    }


}