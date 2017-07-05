<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Product;
use Illuminate\Http\Request;
use Auth;
use DB;

class ProductController extends BaseController
{
    private $Product;

    public function __construct(Product $Product)
    {
        $this->Product = $Product;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            // 过滤参数
            $param = $this->cleanAjaxPageParam();
            $results = $this->Product->ajaxData($this->Product->getTable(), $param);
            foreach ($results['rows'] as $k => &$v) {
                $v['category'] = $this->Product->getOnlyField($this->Product->ProductCategoryTb(), $v['category_id'], 'name');
            }

            return $this->responseAjaxTable($results['total'], $results['rows']);

        } else {
            $reponse = $this->responseTable(url('admin/product/index'), [
                'addUrl' => url('admin/product/add'),
                'editUrl' => url('admin/product/edit'),
                'removeUrl' => url('admin/product/del'),
                'autoSearch' => true
            ]);

            return view('admin/Product/index', compact('reponse'));
        }

    }

    public function add(Request $request)
    {
        if ($request->ajax()) {

            $data = $request->input('data');

            $b = $this->Product->createData($this->Product->getTable(), $data);
            return $b ? $this->responseApi(0) : $this->responseApi(9000);

        } else {

            $cateData = obj2arr($this->Product->getCateList());
            $this->createField('select', '产品分类', 'data[category_id]', $this->cleanSelect($cateData));
            $this->createField('text', '名称', 'data[name]');
            $this->createField('text', '价钱', 'data[price]');
            $this->createField('text', '有效期', 'data[times]', '', ['placeholder' => '填写天数,0则不限期']);
            $this->createField('textarea', '参数', 'data[attrs]');

            $reponse = $this->responseForm('添加产品分类', $this->getFormField());

            return view('admin/Product/add', compact('reponse'));

        }
    }

    public function edit($id, Request $request)
    {
        if ($request->ajax()) {

            $data = $request->input('data');

            $b = $this->Product->updateData($this->Product->getTable(), $id, $data);
            return $b ? $this->responseApi(0) : $this->responseApi(9000);

        } else {
            $info = $this->Product->get($id);

            $cateData = obj2arr($this->Product->getCateList());
            $this->createField('select', '产品分类', 'data[category_id]', $this->cleanSelect($cateData, 'name', 'id', $info->category_id));
            $this->createField('text', '名称', 'data[name]', $info->name);
            $this->createField('text', '价钱', 'data[price]', $info->price);
            $this->createField('text', '有效期', 'data[times]', $info->times, ['placeholder' => '填写天数,0则不限期']);
            $this->createField('textarea', '参数', 'data[attrs]', $info->attrs);

            $reponse = $this->responseForm('编辑产品', $this->getFormField());

            return view('admin/Product/edit', compact('reponse'));

        }
    }

    public function del()
    {
        $ids = $this->getDelIds();
        $result = $this->Product->delData($this->Product->getTable(), $ids);
        $result ? $this->responseApi(0) : $this->responseApi(9000);
    }

    public function category(Request $request)
    {
        if ($request->ajax()) {

            // 过滤参数
            $param = $this->cleanAjaxPageParam();
            $results = $this->Product->ajaxData($this->Product->ProductCategoryTb(), $param);

            return $this->responseAjaxTable($results['total'], $results['rows']);

        } else {

            $reponse = $this->responseTable(url('admin/product/category'), [
                'addUrl' => url('admin/product/category-add'),
                'editUrl' => url('admin/product/category-edit'),
                'removeUrl' => url('admin/product/category-del'),
                'autoSearch' => true
            ]);

            return view('admin/Product/category', compact('reponse'));
        }
    }

    public function categoryAdd(Request $request)
    {
        if ($request->ajax()) {

            $data = $request->input('data');

            $b = $this->Product->createData($this->Product->ProductCategoryTb(), $data);
            return $b ? $this->responseApi(0) : $this->responseApi(9000);

        } else {

            $this->createField('text', '名称', 'data[name]','',['dataType'=>'*']);
            $this->createField('textarea', '描述', 'data[desc]');

            $reponse = $this->responseForm('添加产品分类', $this->getFormField());

            return view('admin/Product/categoryAdd', compact('reponse'));

        }
    }

    public function categoryEdit($id, Request $request)
    {
        if ($request->ajax()) {

            $data = $request->input('data');

            $b = $this->Product->updateData($this->Product->ProductCategoryTb(), $id, $data);
            return $b ? $this->responseApi(0) : $this->responseApi(9000);

        } else {
            $info = $this->Product->getInfo($this->Product->ProductCategoryTb(), $id);

            $this->createField('text', '名称', 'data[name]', $info->name);
            $this->createField('textarea', '描述', 'data[desc]', $info->desc);

            $reponse = $this->responseForm('添加产品分类', $this->getFormField());

            return view('admin/Product/categoryEdit', compact('reponse'));

        }
    }

    public function categoryDel()
    {
        $ids = $this->getDelIds();

        $result = $this->Product->delData($this->Product->ProductCategoryTb(), $ids);
        $result ? $this->responseApi(0) : $this->responseApi(9000);
    }

}
