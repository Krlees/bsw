<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Menu;
use App\Models\UserCase;
use App\Traits\ImageTraits;
use Illuminate\Http\Request;
use DB;

class CaseController extends BaseController
{
    use ImageTraits;

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

            return view('admin/Case/index', compact('reponse'));
        }

    }

    public function add(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->input('data');
            $data['logo'] = $this->thumbImg($request->input(['imgs'])[0], 'Head');
            $data['cphoto'] = $this->thumbImg($request->input(['imgs2'])[0], 'Head');

            $b = $this->case->createData($this->case->getTable(), $data);
            return $b ? $this->responseApi(0) : $this->responseApi(9000);

        } else {
            $cates = DB::table('case_category')->get();

            $this->createField('select', '类型', 'data[cate_id]', $this->cleanSelect(obj2arr($cates)));
            $this->createField('text', '标题', 'data[title]', '', ['dataType' => 's1-64']);
            $this->createField('text', '描述', 'data[description]', '', ['dataType' => 's1-200']);
            $this->createField('text', '引入流量', 'data[yingliu]');
            $this->createField('text', '成本下降', 'data[chengben]');
            $this->createField('text', '销量提升', 'data[sales]');
            $this->createField('text', '排序', 'data[sort]');
            $this->createField('textarea', '推广需求', 'data[demand]');
            $this->createField('textarea', '执行方案', 'data[plan]');
            $this->createField('textarea', '运营指导', 'data[guidance]');
            $this->createField('textarea', '客户资料', 'data[information]');

            $reponse = $this->responseForm('添加管理员', $this->formField);
            return view('admin/Case/add', compact('reponse'));
        }
    }

    public function edit($id, Request $request, UserCase $case)
    {
        if ($request->ajax()) {
            $data = $request->input('data');
            $logo = $request->input(['imgs']);
            $cphoto = $request->input(['imgs2']);
            if($logo){
                $data['logo'] = $logo ? $this->thumbImg($logo[0], 'Head') : '';
            }

            if($cphoto) {
                $data['cphoto'] = $this->thumbImg($cphoto[0], 'Head');
            }

            $b = $this->case->createData($this->case->getTable(), $data);
            return $b ? $this->responseApi(0) : $this->responseApi(9000);

        } else {
            $info = $case->getInfo($case->getTable(), $id);
            $cates = DB::table('case_category')->get();

            $this->createField('select', '类型', 'data[cate_id]', $this->cleanSelect(obj2arr($cates), 'name', 'id', $info->cate_id));
            $this->createField('text', '标题', 'data[title]', $info->title, ['dataType' => 's1-64']);
            $this->createField('text', '描述', 'data[description]', $info->description, ['dataType' => '*']);
            $this->createField('text', '引入流量', 'data[yingliu]', $info->yingliu);
            $this->createField('text', '成本下降', 'data[chengben]', $info->chengben);
            $this->createField('text', '销量提升', 'data[sales]', $info->sales);
            $this->createField('text', '排序', 'data[sort]', $info->sort);
            $this->createField('textarea', '推广需求', 'data[demand]', $info->demand);
            $this->createField('textarea', '执行方案', 'data[plan]', $info->plan);
            $this->createField('textarea', '运营指导', 'data[guidance]', $info->guidance);
            $this->createField('textarea', '客户资料', 'data[information]', $info->information);

            $reponse = $this->responseForm('添加管理员', $this->formField);
            return view('admin/Case/edit', compact('reponse', 'info'));
        }

    }

    public function del()
    {
        $ids = $this->getDelIds();

        $result = DB::table($this->case->getTable())->whereIn('id', $ids)->delete();
        $result ? $this->responseApi(0) : $this->responseApi(9000);

    }


}
