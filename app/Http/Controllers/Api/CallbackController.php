<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Log;
use EasyWeChat\Foundation\Application;


class CallbackController extends BaseController
{
    public function __construct()
    {
    }

    /**
     * 异步通知
     */
    public function webNotify(Request $request)
    {
        // 验证请求。
        if (! app('alipay.web')->verify()) {
            Log::notice('Alipay notify post data verification fail.', [
                'data' => Request::instance()->getContent()
            ]);
            return 'fail';
        }

        // 判断通知类型。
        switch ($request->input('trade_status')) {
            case 'TRADE_SUCCESS':
            case 'TRADE_FINISHED':
                // TODO: 支付成功，取得订单号进行其它相关操作。
                Log::debug('Alipay notify post data verification success.', [
                    'out_trade_no' => $request->input('out_trade_no'),
                    'trade_no' => $request->input('trade_no')
                ]);

                // 处理订单逻辑
                break;
        }

        return 'success';
    }

    /**
     * 同步通知
     */
    public function webReturn(Request $request)
    {
        // 验证请求。
        if (! app('alipay.web')->verify()) {
            Log::notice('Alipay return query data verification fail.', [
                'data' => Request::getQueryString()
            ]);
            return view('alipay.fail');
        }

        // 判断通知类型。
        switch ($request->input('trade_status')) {
            case 'TRADE_SUCCESS':
            case 'TRADE_FINISHED':
                // TODO: 支付成功，取得订单号进行其它相关操作。
                Log::debug('Alipay notify get data verification success.', [
                    'out_trade_no' => $request->input('out_trade_no'),
                    'trade_no' => $request->input('trade_no')
                ]);

                // 处理订单逻辑

                break;
        }

        return view('alipay.success');
    }

    /**
     * 支付宝异步通知
     */
    public function alipayNotify(Request $request)
    {
        // 验证请求。
        if (! app('alipay.mobile')->verify()) {
            Log::notice('Alipay notify post data verification fail.', [
                'data' => Request::instance()->getContent()
            ]);
            return 'fail';
        }

        // 判断通知类型。
        switch ($request->input('trade_status')) {
            case 'TRADE_SUCCESS':
            case 'TRADE_FINISHED':
                // TODO: 支付成功，取得订单号进行其它相关操作。
                Log::debug('Alipay notify get data verification success.', [
                    'out_trade_no' => $request->input('out_trade_no'),
                    'trade_no' => $request->input('trade_no')
                ]);
                break;
        }

        return 'success';
    }

    public function wxNotify()
    {
        $app = new Application(config('wechat'));
        $response = $app->payment->handleNotify(function($notify, $successful){
            if ($successful) {
                $order_arr=json_decode($notify,true);
                $order_guid=$order_arr['out_trade_no'];//订单号
                //回调成功的逻辑


            }
        });
    }
}