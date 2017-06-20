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

    public function ajaxList($param)
    {
        $where = [];
        if (isset($param['search']))
            $where[] = ['name','like','%'.$param['search'].'%'];
        if( isset($param['pid']))
            $where[] = ['pid','=',$param['pid']];

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

    public function create()
    {

    }

}
