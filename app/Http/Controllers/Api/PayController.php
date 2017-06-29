<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Order;
use App\Models\Product;
use App\Models\UserWallet;
use Illuminate\Http\Request;
use DB;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order as wxOrder;
use wechat;


class PayController extends BaseController
{
    public function __construct(Request $request)
    {

        $this->middleware('api.token') or $this->responseApi(1000);
        parent::__construct();
        if (empty($this->user_ses))
            $this->responseApi(1000);


    }

    /**
     * 余额支付
     * @param Request $request
     * @param Order $order
     * @param UserWallet $userWallet
     */
    public function wallet(Request $request, UserWallet $userWallet, Order $order)
    {
        $order_id = $request->input('order_id') or $this->responseApi(1004);
        $orderData = $this->getOrderData($order_id);
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

    /**
     * 支付宝PC支付
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function alipayPc(Request $request, Order $order)
    {
        $order_id = 2;
        $order_id = $request->input('order_id') or $this->responseApi(1004);

        $orderData = $order->get($order_id);

        // 创建支付单。
        $alipay = app('alipay.web');
        $alipay->setOutTradeNo($orderData->order_sn);
        $alipay->setTotalFee($orderData->price);
        $alipay->setSubject($orderData->order_sn);
        $alipay->setBody($orderData->order_sn);

        //$alipay->setQrPayMode('4'); //该设置为可选，添加该参数设置，支持二维码支付。

        // 跳转到支付页面。
        return redirect()->to($alipay->getPayLink());
    }

    public function alipayWap(Request $request, Order $order, Product $product)
    {
        $data = $request->all();
        if (!isset($data['product_id']))
            $this->responseApi(1004);
        elseif (!isset($data['order_sn']))
            $this->responseApi(1004);


        $proInfo = $product->get($data['product_id']);
        if (empty($proInfo))
            $this->responseApi(80001, '产品不存在');

        $data['price'] = $proInfo->price;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['user_id'] = 1;
        unset($data['token']);
        $id = $order->create($data);
        if (!$id)
            $this->responseApi(80001, '订单创建失败');

        // 创建支付单。
        $alipay = app('alipay.mobile');
        $alipay->setOutTradeNo($data['order_sn']);
        $alipay->setTotalFee($data['price']);
        $alipay->setSubject($proInfo->name);
        $alipay->setBody($proInfo->desc);


        // 返回签名后的支付参数给支付宝移动端的SDK。
        $result = $alipay->getPayPara();
        $this->responseApi(0, '', $result);
    }

    public function alipayApp(Request $request, Order $order)
    {

    }

    public function wxpayPc(Request $request, Order $order)
    {

    }

    public function wxpayWap(Request $request, Order $order)
    {

    }

    public function wxpayApp(Request $request, Order $order, Product $product)
    {
        $data = $request->all();
        if (!isset($data['product_id']))
            $this->responseApi(1004);
        elseif (!isset($data['order_sn']))
            $this->responseApi(1004);


        $proInfo = $product->get($data['product_id']);
        if (empty($proInfo))
            $this->responseApi(80001, '产品不存在');

        $data['price'] = $proInfo->price;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['user_id'] = 1;
        unset($data['token']);
        $id = $order->create($data);
        if (!$id)
            $this->responseApi(80001, '订单创建失败');

        $time = (string)time();
        $attributes = [
            'trade_type' => 'APP', // JSAPI，NATIVE，APP...
            'body' => $proInfo->name,
            'detail' => $proInfo->desc,
            'out_trade_no' => $data['order_sn'],
            'total_fee' => ceil($data['price'] * 100), // 单位：分
            'notify_url' => url('Api/callback/wx-notify'), // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'openid' => '', // trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，
            // ...
        ];

        config(['wechat.app_id' => 'wx06a9ceef6a0738d2', 'wechat.payment.merchant_id' => '1310729701']);
        $app = new Application(config('wechat'));
        $order = new wxOrder($attributes);

        $result = $app->payment->prepare($order);
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS') {
//            $prepayId = $result->prepay_id;
            $result->partner_id = $result->mch_id;
            $result->timestamp = $time;
            $this->responseApi(0, '', $result);
        }

        $this->responseApi(80001, '支付失败', $result);


    }

    private function getOrderData($order_id)
    {
        return app('App\Models\Order')->get($order_id);
    }

}