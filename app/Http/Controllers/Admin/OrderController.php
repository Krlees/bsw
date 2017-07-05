<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Order;
use Illuminate\Http\Request;
use DB;

class OrderController extends BaseController
{

    private $Order;

    public function __construct(Order $Order)
    {
        $this->Order = $Order;
    }

    public function index($type, Request $request)
    {
        if ($request->ajax()) {

            $where[] = ['type', '=', $type];
            $where[] = ['status', '>=', 2];
            // 过滤参数
            $param = $this->cleanAjaxPageParam();
            $result = $this->Order->ajaxData($this->Order->getTable(), $param, $where, 'Order_sn');

            return $this->responseAjaxTable($result['total'], $result['rows']);

        } else {
            $reponse = $this->responseTable(url('admin/Order/index/' . $type), [
                'addUrl' => null,
                'editUrl' => url('admin/Order/edit'),
                'removeUrl' => null,
                'autoSearch' => true
            ]);

            return view('admin/Order/index', compact('reponse'));
        }

    }

    public function edit($id, Request $request)
    {
        if ($request->ajax()) {
            $this->responseApi(0);
        }

        $info = $this->Order->get($id);
        $users = DB::table('user')->find($info->user_id, ['username']);
        $products = DB::table('product')->find($info->product_id);

        $this->createField('text', 'ID', 'data[id]', $info->id);
        $this->createField('text', '订单号', 'data[Order_sn]', $info->Order_sn);
        $this->createField('text', '价格', 'data[price]', $info->price);
        $this->createField('text', '用户', 'data[user_id]', $users->username);
        $this->createField('text', '下单时间', 'data[created_at]', $info->created_at);
        $this->createField('text', '支付用户', 'data[pay_buyer]', $info->pay_buyer);
        $this->createField('text', '支付时间', 'data[pay_time]', $info->pay_time);
        $this->createField('text', '支付方式', 'data[pay_type]', $info->pay_type);
        $this->createField('text', '类型', 'data[type]', $this->_OrderTypeStr($info->type));
        $this->createField('text', '产品', 'data[sort]', $products->name);

        $reponse = $this->responseForm('订单详情', $this->formField);

        return view('admin/Order/edit', compact('reponse'));
    }

    public function del()
    {
        $ids = $this->getDelIds();
        foreach ($ids as $v) {
            if ($v == 1)
                $this->responseApi(80001, "不可以删除超级管理员");
        }

        $result = DB::table($this->Order->getTable())->whereIn('id', $ids)->delete();
        $result ? $this->responseApi(0) : $this->responseApi(9000);
    }

    private function _OrderTypeStr($key)
    {
        $arr = [1 => '信息订单', 2 => '充值订单', 3 => '提现订单', 4 => '广告位订单', 5 => '认证资料订单', 6 => '推广激活', 7 => '其他'];
        return array_get($arr, $key, '');
    }

}
