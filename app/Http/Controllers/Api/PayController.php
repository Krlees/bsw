<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Order;
use App\Models\UserWallet;
use Illuminate\Http\Request;
use DB;

class PayController extends BaseController
{
    public function __construct(Request $request)
    {
        $this->middleware('api.token') or $this->responseApi(1000);
        parent::__construct();
    }

    /**
     * 余额支付
     * @param Request $request
     * @param Order $order
     * @param UserWallet $userWallet
     */
    public function wallet(Request $request, Order $order, UserWallet $userWallet)
    {
        $order_id = $request->input('order_id') or $this->responseApi(1004);
        $orderData = $order->get($order_id);
        if ($orderData->status != 1) {
            $this->responseApi(80001, '该订单状态错误，请重新下单');
        }

        // 1. 判断用户是否有足够的金额支付
        $walletData = $userWallet->get($this->user_ses->id);
        if ($walletData['money'] < $orderData->price) {
            $this->responseApi(80001, '对不起，您的余额不足');
        }

        // 2. 实现余额支付。
        DB::beginTransaction();
        try {
            $res1 = DB::table($userWallet->getTable())->where('user_id', $this->user_ses->id)->decrement('money', $orderData->price);
            $res2 = $order->updateData($order_id, [
                'status' => 2,
                'pay_time' => date('Y-m-d H:i:s'),
                'pay_type' => 'wallet',
            ]);
            if ($res1 && $res2) {
                DB::commit();
                $this->responseApi(0);
            }
        } catch (\Exception $e) {

        }

        DB::rollBack();
        $this->responseApi(9000);

    }


}