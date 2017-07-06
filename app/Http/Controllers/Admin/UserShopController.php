<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\UserShop;
use Illuminate\Http\Request;
use DB;

class UserShopController extends BaseController
{
    private $shop;

    public function __construct(UserShop $shop)
    {
        $this->shop = $shop;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $where[] = ['is_del', '=', 0];
            // 过滤参数
            $param = $this->cleanAjaxPageParam();
            $result = $this->shop->ajaxData($this->shop->getTable(), $param, $where, 'name');

            return $this->responseAjaxTable($result['total'], $result['rows']);

        } else {
            $reponse = $this->responseTable(url('admin/shop/index'), [
                'addUrl' => null,
                'editUrl' => url('admin/shop/edit'),
                'removeUrl' => null,
                'autoSearch' => true
            ]);

            return view('admin/Shop/index', compact('reponse'));
        }

    }

    public function edit($id, Request $request)
    {
        if ($request->ajax()) {
            $data = $request->input('data');

            $b = $this->shop->updateData($this->shop->getTable(), $id, $data);
            return $b ? $this->responseApi(0) : $this->responseApi(9000);

        } else {
            $info = $this->shop->getInfo($this->shop->getTable(), $id);

            $this->createField('text', '标签名', 'data[name]', $info->name);
            $this->createField('text', '排序', 'data[sort]', $info->sort);

            $reponse = $this->responseForm('编辑信息', $this->formField);
            return view('admin/Shop/edit', compact('reponse'));
        }
    }

    public function del()
    {
        $ids = $this->getDelIds();

        $result = DB::table($this->shop->getTable())->whereIn('id', $ids)->delete();
        $result ? $this->responseApi(0) : $this->responseApi(9000);
    }


}
