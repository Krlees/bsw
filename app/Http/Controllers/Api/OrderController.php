<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends BaseController
{
    public function __construct(Request $request)
    {
        $this->middleware('api.token') or $this->responseApi(1000);
        parent::__construct();
    }

    /**
     * 创建订单
     * @param Request $request
     * @param Order $order
     */
    public function create(Request $request, Order $order, Product $product)
    {

        $type = $request->input('type') or $this->responseApi(1004);
        $product_id = $request->input('product_id') or $this->responseApi(1004);
        $transaction_id = $request->input('transaction_id');
        $created_at = date('Y-m-d H:i:s');
        $order_sn = create_order_sn($type, $product_id);
        $status = 1;
        $price = $request->input('price');
        if(!$price){
            $products = $product->get($product_id);
            $price = $products->price;
        }

        $user_id = ($this->user_ses) ? $this->user_ses->id : 0;

        $data = compact('type', 'product_id', 'transaction_id', 'created_at', 'order_sn', 'status', 'user_id');
        $result = $order->create($data);
        $result ? $this->responseApi(0, '', $result) : $this->responseApi(9000);
    }

    /**
     * 获取订单详情
     * @param $id
     * @param Order $order
     */
    public function get($id, Order $order)
    {
        $data = $order->get($id);
        $data = obj2arr($data);

        $this->responseApi(0,'',$data);
    }

    public function getList(Request $request,Order $order)
    {
        $result = $order->getList($this->user_ses->id);

        $this->responseApi(0,'',$result);
    }
}