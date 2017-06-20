<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Product;
use Illuminate\Http\Request;
use Auth;
use DB;

class ProductController extends BaseController
{
    private $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function ajaxList(Request $request)
    {
        if ($request->ajax()) {

            // 过滤参数
            $param = $this->cleanAjaxPageParam();
            $results = $this->product->ajaxData($this->product->getTable());

            return $this->responseAjaxTable($results['total'], $results['rows']);

        } else {
            $reponse = $this->responseTable(url('admin/product/index'), [
                'addUrl' => url('admin/product/add'),
                'editUrl' => url('admin/product/edit'),
                'removeUrl' => url('admin/product/del'),
                'autoSearch' => true
            ]);

            return view('admin/Menu/index', compact('reponse'));
        }

    }

    public function add(Request $request)
    {

    }

    public function edit($id, Request $request)
    {

    }

    public function del()
    {
        $ids = $this->getDelIds();

    }

}
