<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    public function getList(Request $request, Product $product,Transaction $transaction)
    {
        $cateId = $request->input('cate_id', 0);
        $id = $request->input('id') or $this->responseApi(1004);

        $list = $product->getList($cateId, $fields = ['id', 'price', 'name']);
        $result = $transaction->get($id);
        $result['list'] = $list;

        $this->responseApi(0, null, $result);
    }


}