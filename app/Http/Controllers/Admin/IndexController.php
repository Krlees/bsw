<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Menu;
use Illuminate\Http\Request;
use Auth;
use DB;

class IndexController extends BaseController
{
    public function index(Menu $menu)
    {
        $menus = DB::table($menu->getTable())->where('pid', 0)->where('is_show',1)->orderBy('sort','desc')->get();
        foreach ($menus as $k => $v) {
            $v->sub = DB::table($menu->getTable())->where('pid', $v->id)->where('is_show',1)->get();
        }

        return view('admin/index', compact('menus'));
    }

    public function dashboard()
    {

        return view('admin/dashboard');
    }


}
