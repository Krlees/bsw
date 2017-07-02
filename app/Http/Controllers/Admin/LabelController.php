<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\IndustryCate;
use App\Models\Jobcate;
use App\Models\Label;
use Illuminate\Http\Request;
use Auth;
use DB;

class LabelController extends BaseController
{
    private $label;

    public function __construct(Label $label)
    {
        $this->label = $label;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            // 过滤参数
            $param = $this->cleanAjaxPageParam();
            $result = $this->label->ajaxData($this->label->getTable(), $param, false, 'name');

            return $this->responseAjaxTable($result['total'], $result['rows']);

        } else {
            $reponse = $this->responseTable(url('admin/label/index'), [
                'addUrl' => url('admin/label/add'),
                'editUrl' => url('admin/label/edit'),
                'removeUrl' => url('admin/label/del'),
                'autoSearch' => true
            ]);

            return view('admin/Label/index', compact('reponse'));
        }

    }

    public function add(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->input('data');

            $b = $this->label->createData($this->label->getTable(), $data);
            return $b ? $this->responseApi(0) : $this->responseApi(9000);

        } else {

            $this->createField('text', '标签名', 'data[name]');
            $this->createField('text', '排序', 'data[sort]');

            $reponse = $this->responseForm('添加信息', $this->formField);
            return view('admin/label/add', compact('reponse'));
        }
    }

    public function edit($id, Request $request)
    {
        if ($request->ajax()) {
            $data = $request->input('data');

            $b = $this->label->updateData($this->label->getTable(), $id, $data);
            return $b ? $this->responseApi(0) : $this->responseApi(9000);

        } else {
            $info = $this->label->getInfo($this->label->getTable(), $id);

            $this->createField('text', '标签名', 'data[name]', $info->name);
            $this->createField('text', '排序', 'data[sort]', $info->sort);

            $reponse = $this->responseForm('添加信息', $this->formField);
            return view('admin/label/edit', compact('reponse'));
        }
    }

    public function del()
    {
        $ids = $this->getDelIds();

        $result = DB::table($this->label->getTable())->whereIn('id', $ids)->delete();
        $result ? $this->responseApi(0) : $this->responseApi(9000);
    }

    public function job(Request $request, Jobcate $jobcate)
    {
        if ($request->ajax()) {

            // 过滤参数
            $param = $this->cleanAjaxPageParam();
            $result = $jobcate->ajaxData($jobcate->getTable(), $param, false, 'cate_name');

            return $this->responseAjaxTable($result['total'], $result['rows']);

        } else {
            $reponse = $this->responseTable(url('admin/label/job'), [
                'addUrl' => url('admin/label/job-add'),
                'editUrl' => url('admin/label/job-edit'),
                'removeUrl' => url('admin/label/job-del'),
                'autoSearch' => true
            ]);

            return view('admin/Label/job', compact('reponse'));
        }
    }

    public function jobAdd(Request $request, Jobcate $jobcate)
    {
        if ($request->ajax()) {
            $data = $request->input('data');

            $b = $jobcate->createData($jobcate->getTable(), $data);
            return $b ? $this->responseApi(0) : $this->responseApi(9000);

        } else {

            $this->createField('text', '名称', 'data[cate_name]');
            $this->createField('text', '排序', 'data[sort]');

            $reponse = $this->responseForm('添加分类', $this->formField);
            return view('admin/label/job_add', compact('reponse'));
        }
    }

    public function jobEdit($id, Request $request, Jobcate $jobcate)
    {
        if ($request->ajax()) {
            $data = $request->input('data');

            $b = $jobcate->updateData($jobcate->getTable(), $id, $data);
            return $b ? $this->responseApi(0) : $this->responseApi(9000);

        } else {
            $info = $jobcate->getInfo($jobcate->getTable(), $id);

            $this->createField('text', '名称', 'data[cate_name]', $info->cate_name);
            $this->createField('text', '排序', 'data[sort]', $info->sort);

            $reponse = $this->responseForm('添加信息', $this->formField);
            return view('admin/label/job_edit', compact('reponse'));
        }
    }

    public function jobDel(Jobcate $jobcate)
    {
        $ids = $this->getDelIds();

        $result = DB::table($jobcate->getTable())->whereIn('id', $ids)->delete();
        $result ? $this->responseApi(0) : $this->responseApi(9000);
    }

    public function industry(Request $request, IndustryCate $industryCate)
    {
        if ($request->ajax()) {

            // 过滤参数
            $param = $this->cleanAjaxPageParam();
            $result = $industryCate->ajaxData($industryCate->getTable(), $param, false, 'cate_name');

            return $this->responseAjaxTable($result['total'], $result['rows']);

        } else {
            $reponse = $this->responseTable(url('admin/label/industry'), [
                'addUrl' => url('admin/label/industry-add'),
                'editUrl' => url('admin/label/industry-edit'),
                'removeUrl' => url('admin/label/industry-del'),
                'autoSearch' => true
            ]);

            return view('admin/Label/industry', compact('reponse'));
        }
    }

    public function industryAdd(Request $request, IndustryCate $industryCate)
    {
        if ($request->ajax()) {
            $data = $request->input('data');

            $b = $industryCate->createData($industryCate->getTable(), $data);
            return $b ? $this->responseApi(0) : $this->responseApi(9000);

        } else {

            $this->createField('text', '名称', 'data[cate_name]');
            $this->createField('text', '排序', 'data[sort]');

            $reponse = $this->responseForm('添加分类', $this->formField);
            return view('admin/label/industry_add', compact('reponse'));
        }
    }

    public function industryEdit($id, Request $request, IndustryCate $industryCate)
    {
        if ($request->ajax()) {
            $data = $request->input('data');

            $b = $industryCate->updateData($industryCate->getTable(), $id, $data);
            return $b ? $this->responseApi(0) : $this->responseApi(9000);

        } else {
            $info = $industryCate->getInfo($industryCate->getTable(), $id);

            $this->createField('text', '名称', 'data[cate_name]', $info->cate_name);
            $this->createField('text', '排序', 'data[sort]', $info->sort);

            $reponse = $this->responseForm('添加信息', $this->formField);
            return view('admin/label/industry_edit', compact('reponse'));
        }
    }

    public function industryDel(IndustryCate $industryCate)
    {
        $ids = $this->getDelIds();

        $result = DB::table($industryCate->getTable())->whereIn('id', $ids)->delete();
        $result ? $this->responseApi(0) : $this->responseApi(9000);
    }


}
