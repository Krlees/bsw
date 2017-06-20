<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Menu extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'menu';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public $timestamps = false;

    public function get($id)
    {
        return DB::table($this->table)->find($id);
    }

    public function ajaxList($param)
    {
        $where = [];
        if (isset($param['search']))
            $where[] = ['name', 'like', '%' . $param['search'] . '%'];
        if (isset($param['pid']))
            $where[] = ['pid', '=', $param['pid']];

        $sort = array_get($param, 'sort') ?: $this->getKeyName();
        $order = array_get($param, 'order', 'desc');
        $rows = DB::table($this->table)->where($where)->orderBy($sort, $order)->offset(array_get($param, 'offset', 0))->limit(array_get($param, 'limit', 10))->get();
        $rows = obj2arr($rows);
        $total = DB::table($this->table)->where($where)->count();

        return compact('rows', 'total');
    }

    /**
     *  获取菜单
     */
    public function getList($pid = 0)
    {
        return DB::table($this->table)->where('pid', $pid)->get(['id', 'name']);
    }

    public function create($data)
    {
        try {
            $id = DB::table($this->table)->insertGetId($data);

            return $id ?: false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateData($id, $data)
    {
        try {
            $b = DB::table($this->table)->where('id', $id)->update($data);

            return $b !== false ? true : false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function delData($ids)
    {
        DB::beginTransaction();
        try {

            $b1 = DB::table($this->table)->whereIn('id', $ids)->delete();
            $b2 = DB::table($this->table)->whereIn('pid', $ids)->delete();

            if($b1!==false && $b2 !== false){
                DB::commit();
                return true;
            }

        } catch (\Exception $e) {

        }

        DB::rollBack();
        return false;
    }
}
