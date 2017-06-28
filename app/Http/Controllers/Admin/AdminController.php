<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use DB;

class AdminController extends BaseController
{
    public function index(Request $request, User $admin)
    {
        if ($request->ajax()) {

            // 过滤参数
            $where = [];
            $param = $this->cleanAjaxPageParam();
            if (array_get($param, 'search'))
                $where[] = ['email', 'like', '%' . array_get($param, 'search') . '%'];

            $rows = DB::table($admin->getTable())->offset($param['offset'])->limit($param['limit'])->where($where)->orderBy('id', 'desc')->get();
            $rows = obj2arr($rows);

            $total = DB::table($admin->getTable())->where($where)->count();

            return $this->responseAjaxTable($total, $rows);

        } else {
            $reponse = $this->responseTable(url('admin/admin/index'), [
                'addUrl' => url('admin/admin/add'),
                'removeUrl' => url('admin/admin/del'),
                'autoSearch' => true
            ]);

            return view('admin/Admin/index', compact('reponse'));
        }

        return view('admin/index', compact('menus'));
    }

    public function add(Request $request, User $admin)
    {
        if ($request->ajax()) {
            $data = $request->input('data');
            $data['password'] = bcrypt($data['password']);

            $b = DB::table($admin->getTable())->insert($data);
            return $b ? $this->responseApi(0) : $this->responseApi(9000);

        } else {

            $this->createField('text', '登录账户', 'data[email]', '', ['dataType' => 's1-30']);
            $this->createField('text', '登录密码', 'data[password]', '', ['dataType' => 's1-30']);

            $reponse = $this->responseForm('添加管理员', $this->formField);
            return view('admin/Admin/add', compact('reponse'));
        }
    }

    public function del()
    {
        $ids = $this->getDelIds();
        foreach ($ids as $v){
            if($v==1)
                $this->responseApi(80001,"不可以删除超级管理员");
        }

        $result = DB::table('users')->whereIn('id',$ids)->delete();
        $result ? $this->responseApi(0) : $this->responseApi(9000);
    }
}
