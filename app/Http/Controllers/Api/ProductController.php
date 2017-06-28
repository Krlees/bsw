<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    public function getList(Request $request, Product $product, Transaction $transaction)
    {
        $cateId = $request->input('cate_id', 0);
        $id = $request->input('id') or $this->responseApi(1004);

        $list = $product->getList($cateId);
        $result = $transaction->get($id);
        foreach ($list as &$v){
            $v['ordid'] = create_order_sn(1, $id);
            $v['productid'] = $v['id'];
            $v['totle_fee'] = $v['price'];
            $v['payment_type'] = '1';
            $v['subject'] = $v['name'];
            $v['body'] = $v['desc'];
            $v['needgold'] = '';
            $v['needwallet'] = '';
            $v['product_attr'] = $v['attrs'];
            $v['type'] = '';
            $v['ordid'] = create_order_sn(1, $id);
        }
        $return['data'] = $list;
        $return['data2'] = $result;


        $this->responseApi(0, null, $return);
    }


}