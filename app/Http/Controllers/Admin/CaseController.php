<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Menu;
use App\Models\UserCase;
use Illuminate\Http\Request;
use DB;

class CaseController extends BaseController
{
    private $case;

    public function __construct(UserCase $case)
    {
        $this->case = $case;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            // 过滤参数
            $param = $this->cleanAjaxPageParam();
            $result = $this->case->ajaxData($this->case->getTable(), $param, false, 'title');

            return $this->responseAjaxTable($result['total'], $result['rows']);

        } else {
            $reponse = $this->responseTable(url('admin/case/index'), [
                'addUrl' => url('admin/case/add'),
                'editUrl' => url('admin/case/edit'),
                'removeUrl' => url('admin/case/del'),
                'autoSearch' => true
            ]);

            return view('admin/case/index', compact('reponse'));
        }

    }

    public function add(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->input('data');

            $b = $this->case->createData($this->case->getTable(), $data);
            return $b ? $this->responseApi(0) : $this->responseApi(9000);

        } else {
            $cates = DB::table('case_category')->get();

            $this->createField('select', '类型', 'data[cate_id]', $this->cleanSelect(obj2arr($cates)));
            $this->createField('text', '标题', 'data[title]', '', ['dataType' => 's1-64']);
            $this->createField('text', '描述', 'data[desc]', '', ['dataType' => 's1-200']);
            $this->createField('text', '引入流量', 'data[yingliu]');
            $this->createField('text', '成本下降', 'data[chengben]');
            $this->createField('text', '销量提升', 'data[sales]');
            $this->createField('text', '排序', 'data[sort]');
            $this->createField('image', 'logo', 'data[logo]');
            $this->createField('image', '内容图片', 'data[cphoto]');
            $this->createField('textarea', '推广需求', 'data[demand]');
            $this->createField('textarea', '执行方案', 'data[plan]');
            $this->createField('textarea', '运营指导', 'data[guidance]');
            $this->createField('textarea', '客户资料', 'data[information]');

            $reponse = $this->responseForm('添加管理员', $this->formField);
            return view('admin/admin/add', compact('reponse'));
        }
    }

    public function edit($id, Request $request)
    {
        if ($request->ajax()) {
            $data = $request->input('data');

            $b = $this->case->createData($this->case->getTable(), $data);
            return $b ? $this->responseApi(0) : $this->responseApi(9000);

        } else {
            $cates = DB::table('case_category')->get();

            $this->createField('select', '类型', 'data[cate_id]', $this->cleanSelect(obj2arr($cates)));
            $this->createField('text', '标题', 'data[title]', '', ['dataType' => 's1-64']);
            $this->createField('text', '描述', 'data[desc]', '', ['dataType' => 's1-200']);
            $this->createField('text', '引入流量', 'data[yingliu]');
            $this->createField('text', '成本下降', 'data[chengben]');
            $this->createField('text', '销量提升', 'data[sales]');
            $this->createField('text', '排序', 'data[sort]');
            $this->createField('image', 'logo', 'data[logo]');
            $this->createField('image', '内容图片', 'data[cphoto]');
            $this->createField('textarea', '推广需求', 'data[demand]');
            $this->createField('textarea', '执行方案', 'data[plan]');
            $this->createField('textarea', '运营指导', 'data[guidance]');
            $this->createField('textarea', '客户资料', 'data[information]');

            $reponse = $this->responseForm('添加管理员', $this->formField);
            return view('admin/admin/add', compact('reponse'));
        }
    }

    public function del()
    {
        $ids = $this->getDelIds();
        foreach ($ids as $v) {
            if ($v == 1)
                $this->responseApi(80001, "不可以删除超级管理员");
        }

        $result = DB::table($this->case->getTable())->whereIn('id', $ids)->delete();
        $result ? $this->responseApi(0) : $this->responseApi(9000);
    }


}