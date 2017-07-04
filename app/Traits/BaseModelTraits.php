<?php

namespace App\Traits;

use DB;

trait BaseModelTraits
{
    public function ajaxData($tableName, $param, $where = false, $searchField = ['name'], $fields = ['*'])
    {
        $where = $where ?: [];
        if (isset($param['search'])) {
            if (!is_array($searchField))
                $searchField[] = $searchField;

            foreach ($searchField as $k => $v) {
                $where[$k] = [$v, 'like', '%' . $param['search'] . '%', 'OR'];
            }
        }

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

    public function getList($tbName, $page = 0, $limit = 20, $where = null)
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

    public function dbWhere($tbName)
    {
        return DB::table($tbName)->where();
    }

    public function getAll($tbName, $where = null, $field = ['*'])
    {
        $where = $where ?: [];
        $result = DB::table($tbName)->where($where)->get($field);
        return obj2arr($result);
    }

    public function getCount($tbName, $where = null)
    {
        $where = $where ?: [];
        $result = DB::table($tbName)->where($where)->count();
    }

}