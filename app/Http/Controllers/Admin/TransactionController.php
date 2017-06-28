<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Menu;
use App\Models\Transaction;
use App\Models\UserCase;
use App\Traits\ImageTraits;
use Illuminate\Http\Request;
use DB;

class TransactionController extends BaseController
{
    use ImageTraits;

    private $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function index($channel_id, Request $request)
    {
        if ($request->ajax()) {

            $where[] = ['channel_id', '=', $channel_id];

            // 过滤参数
            $param = $this->cleanAjaxPageParam();
            $result = $this->transaction->ajaxData($this->transaction->getTable(), $param, $where, 'title');
            foreach ($result['rows'] as &$v) {
                $v['created_at'] = date('Y-m-d H:i:s', $v['created_at']);
            }

            return $this->responseAjaxTable($result['total'], $result['rows']);

        } else {
            $reponse = $this->responseTable(url('admin/transaction/index/' . $channel_id), [
                'addUrl' => null,
                'editUrl' => url('admin/transaction/edit'),
                'removeUrl' => url('admin/transaction/del'),
                'autoSearch' => true
            ]);

            return view('admin/Transaction/index', compact('reponse'));
        }

    }

    public function add($userId, Request $request)
    {
        if ($request->ajax()) {
            $data = $request->input('data');
            $data['logo'] = $this->thumbImg($request->input(['imgs'])[0], 'Head');
            $data['cphoto'] = $this->thumbImg($request->input(['imgs2'])[0], 'Head');

            $b = $this->transaction->createData($this->transaction->getTable(), $data);
            return $b ? $this->responseApi(0) : $this->responseApi(9000);

        } else {
            $cates = DB::table('channel')->get();
            $cates = obj2arr($cates);

            $this->createField('select', '频道', 'data[cate_id]', $this->cleanSelect(obj2arr($cates)));
            $this->createField('text', '标题', 'data[title]', '', ['dataType' => 's1-64']);
            $this->createField('text', '电话', 'data[tel]', '', ['dataType' => 's1-64']);
            $this->createField('text', '描述', 'data[description]', '', ['dataType' => 's1-200']);
            $this->createField('text', '频道类型', 'data[yingliu]');
            $this->createField('text', '职位', 'data[chengben]');
            $this->createField('text', '状态', 'data[sort]');
            $this->createField('textarea', '天数', 'data[demand]');
            $this->createField('textarea', '额外参数1', 'data[plan]');
            $this->createField('textarea', '额外参数2', 'data[guidance]');
            $this->createField('textarea', '标签', 'data[information]');

            $reponse = $this->responseForm('添加信息', $this->formField);
            return view('admin/transaction/add/' . $userId, compact('reponse'));
        }
    }

    public function edit($id, Request $request, UserCase $case)
    {
        if ($request->ajax()) {
            $data = $request->input('data');
            $logo = $request->input(['imgs']);
            $cphoto = $request->input(['imgs2']);
            if ($logo) {
                $data['logo'] = $this->thumbImg($logo[0], 'Head');
            }
            if ($cphoto) {
                $data['cphoto'] = $this->thumbImg($cphoto[0], 'Head');
            }

            $b = $this->transaction->createData($this->transaction->getTable(), $data);
            return $b ? $this->responseApi(0) : $this->responseApi(9000);

        } else {
            $info = $case->getInfo($case->getTable(), $id);
            $cates = DB::table('case_category')->get();

            $this->createField('select', '类型', 'data[cate_id]', $this->cleanSelect(obj2arr($cates), 'name', 'id', $info->cate_id));
            $this->createField('text', '标题', 'data[title]', $info->title, ['dataType' => 's1-64']);
            $this->createField('text', '描述', 'data[description]', $info->description, ['dataType' => 's1-200']);
            $this->createField('text', '引入流量', 'data[yingliu]', $info->yingliu);
            $this->createField('text', '成本下降', 'data[chengben]', $info->chengben);
            $this->createField('text', '销量提升', 'data[sales]', $info->sales);
            $this->createField('text', '排序', 'data[sort]', $info->sort);
            $this->createField('textarea', '推广需求', 'data[demand]', $info->demand);
            $this->createField('textarea', '执行方案', 'data[plan]', $info->plan);
            $this->createField('textarea', '运营指导', 'data[guidance]', $info->guidance);
            $this->createField('textarea', '客户资料', 'data[information]', $info->information);

            $reponse = $this->responseForm('添加管理员', $this->formField);
            return view('admin/case/edit', compact('reponse', 'info'));
        }

    }

    public function del()
    {
        $ids = $this->getDelIds();
        foreach ($ids as $v) {
            if ($v == 1)
                $this->responseApi(80001, "不可以删除超级管理员");
        }

        $result = DB::table($this->transaction->getTable())->whereIn('id', $ids)->delete();
        $result ? $this->responseApi(0) : $this->responseApi(9000);
    }


}
