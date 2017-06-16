<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    public function getList(Request $request, Product $product)
    {
        $cateId = $request->input('cate_id',0);

        $result = $product->getList($cateId,$fields=['id','price','name']);

        $this->responseApi(0,null,$result);
    }


}