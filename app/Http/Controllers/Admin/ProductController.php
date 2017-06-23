<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Product;
use Illuminate\Http\Request;
use Auth;
use DB;

class ProductController extends BaseController
{
    private $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            // 过滤参数
            $param = $this->cleanAjaxPageParam();
            $results = $this->product->ajaxData($this->product->getTable(), $param);
            foreach ($results['rows'] as $k => &$v) {
                $v['category'] = $this->product->getOnlyField($this->product->productCategoryTb(), $v['category_id'], 'name');
            }

            return $this->responseAjaxTable($results['total'], $results['rows']);

        } else {
            $reponse = $this->responseTable(url('admin/product/index'), [
                'addUrl' => url('admin/product/add'),
                'editUrl' => url('admin/product/edit'),
                'removeUrl' => url('admin/product/del'),
                'autoSearch' => true
            ]);

            return view('admin/product/index', compact('reponse'));
        }

    }

    public function add(Request $request)
    {
        if ($request->ajax()) {

            $data = $request->input('data');

            $b = $this->product->createData($this->product->getTable(), $data);
            return $b ? $this->responseApi(0) : $this->responseApi(9000);

        } else {

            $cateData = obj2arr($this->product->getCateList());
            $this->createField('select', '产品分类', 'data[category_id]', $this->cleanSelect($cateData));
            $this->createField('text', '名称', 'data[name]');
            $this->createField('text', '价钱', 'data[price]');
            $this->createField('text', '有效期', 'data[times]', '', ['placeholder' => '填写天数,0则不限期']);
            $this->createField('textarea', '参数', 'data[attrs]');

            $reponse = $this->responseForm('添加产品分类', $this->getFormField());

            return view('admin/product/add', compact('reponse'));

        }
    }

    public function edit($id, Request $request)
    {
        if ($request->ajax()) {

            $data = $request->input('data');

            $b = $this->product->updateData($this->product->getTable(), $id, $data);
            return $b ? $this->responseApi(0) : $this->responseApi(9000);

        } else {
            $info = $this->product->get($id);

            $cateData = obj2arr($this->product->getCateList());
            $this->createField('select', '产品分类', 'data[category_id]', $this->cleanSelect($cateData, 'name', 'id', $info->category_id));
            $this->createField('text', '名称', 'data[name]', $info->name);
            $this->createField('text', '价钱', 'data[price]', $info->price);
            $this->createField('text', '有效期', 'data[times]', $info->times, ['placeholder' => '填写天数,0则不限期']);
            $this->createField('textarea', '参数', 'data[attrs]', $info->attrs);

            $reponse = $this->responseForm('编辑产品', $this->getFormField());

            return view('admin/product/edit', compact('reponse'));

        }
    }

    public function del()
    {
        $ids = $this->getDelIds();
        $result = $this->product->delData($this->product->getTable(), $ids);
        $result ? $this->responseApi(0) : $this->responseApi(9000);
    }

    public function category(Request $request)
    {
        if ($request->ajax()) {

            // 过滤参数
            $param = $this->cleanAjaxPageParam();
            $results = $this->product->ajaxData($this->product->productCategoryTb(), $param);

            return $this->responseAjaxTable($results['total'], $results['rows']);

        } else {

            $reponse = $this->responseTable(url('admin/product/category'), [
                'addUrl' => url('admin/product/category-add'),
                'editUrl' => url('admin/product/category-edit'),
                'removeUrl' => url('admin/product/category-del'),
                'autoSearch' => true
            ]);

            return view('admin/product/category', compact('reponse'));
        }
    }

    public function categoryAdd(Request $request)
    {
        if ($request->ajax()) {

            $data = $request->input('data');

            $b = $this->product->createData($this->product->productCategoryTb(), $data);
            return $b ? $this->responseApi(0) : $this->responseApi(9000);

        } else {

            $this->createField('text', '名称', 'data[name]','',['dataType'=>'*']);
            $this->createField('textarea', '描述', 'data[desc]');

            $reponse = $this->responseForm('添加产品分类', $this->getFormField());

            return view('admin/product/categoryAdd', compact('reponse'));

        }
    }

    public function categoryEdit($id, Request $request)
    {
        if ($request->ajax()) {

            $data = $request->input('data');

            $b = $this->product->updateData($this->product->productCategoryTb(), $id, $data);
            return $b ? $this->responseApi(0) : $this->responseApi(9000);

        } else {
            $info = $this->product->getInfo($this->product->productCategoryTb(), $id);

            $this->createField('text', '名称', 'data[name]', $info->name);
            $this->createField('textarea', '描述', 'data[desc]', $info->desc);

            $reponse = $this->responseForm('添加产品分类', $this->getFormField());

            return view('admin/product/categoryEdit', compact('reponse'));

        }
    }

    public function categoryDel()
    {
        $ids = $this->getDelIds();

        $result = $this->product->delData($this->product->productCategoryTb(), $ids);
        $result ? $this->responseApi(0) : $this->responseApi(9000);
    }

}
