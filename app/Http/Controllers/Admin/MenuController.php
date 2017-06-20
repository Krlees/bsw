<?php

namespace App\Http\Controllers\admin;

use App\Models\Menu;
use App\Traits\Admin\FormTraits;
use Illuminate\Http\Request;

class MenuController extends BaseController
{
    use FormTraits;

    private $menu;

    public function __construct(Menu $menu)
    {
        $this->menu = $menu;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            // 过滤参数
            $param = $this->cleanAjaxPageParam($request->all());
            $results = $this->menu->ajaxList($param);

            if ($param['pid'] > 0) {
                $this->responseApi(0, '', $results['rows']);
            }

            return $this->responseAjaxTable($results['total'], $results['rows']);

        } else {
            $reponse = $this->responseTable(url('admin/menu/index?pid=0'), [
                'addUrl' => url('admin/menu/add'),
                'editUrl' => url('admin/menu/edit'),
                'removeUrl' => url('admin/menu/del'),
                'autoSearch' => true
            ]);

            return view('admin/Menu/index', compact('reponse'));
        }
    }

    public function add(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->input('data');

            $b = $this->menu->create($data);
            return $b ? $this->responseApi(0) : $this->responseApi(9000);

        } else {
            $menu_data = obj2arr($this->menu->getList(0));

            $this->createField('select', '上级菜单', 'data[pid]', $this->cleanSelect($menu_data, 'name', 'id'));
            $this->createField('text', '名称', 'data[name]', '', ['dataType' => 's1-30']);
            $this->createField('text', 'Url路由', 'data[url]');
            $this->createField('text', 'Icon', 'data[icon]');
            $this->createField('text', '排序', 'data[sort]', 0, ['dataType' => '*']);
            // $this->createField('text', '权限名', 'data[permission_name]');
            $this->createField('radio', '是否显示', 'data[is_show]', [
                [
                    'text' => '是',
                    'value' => 1,
                    'checked' => true
                ], [
                    'text' => '否',
                    'value' => 0,
                ]
            ]);

            $reponse = $this->responseForm('添加菜单', $this->formField);
            return view('admin/menu/add', compact('reponse'));
        }
    }

    public function edit($id, Request $request)
    {
        if ($request->ajax()) {
            $data = $request->input('data');

            $affected = $this->menu->updateData($id,$data);
            return $affected ? $this->responseApi(0) : $this->responseApi(400);

        } else {
            $data = $this->menu->get($id);
            $data = obj2arr($data);

            // 菜单顶级分类
            $menu_data = obj2arr($this->menu->getList(0));
            $selectData = $this->cleanSelect($menu_data, 'name', 'id', $data['pid']);

            $this->createField('select', '上级菜单', 'data[pid]', $selectData);
            $this->createField('text', '名称', 'data[name]', $data['name'], ['dataType' => 's1-30']);
            $this->createField('text', 'Url路由', 'data[url]', $data['url']);
            $this->createField('text', 'Icon', 'data[icon]', $data['icon']);
            $this->createField('text', '排序', 'data[sort]', $data['sort'], ['dataType' => 'n']);
            $this->createField('radio', '是否显示', 'data[is_show]', [
                [
                    'text' => '是',
                    'value' => 1,
                    'checked' => $data['is_show'] == 1 ? true : false
                ], [
                    'text' => '否',
                    'value' => 0,
                    'checked' => $data['is_show'] == 0 ? true : false
                ]
            ]);

            $reponse = $this->responseForm('编辑菜单', $this->formField, url('admin/menu/edit/' . $id));
            return view('admin/menu/edit', compact('reponse'));
        }
    }

    public function del()
    {
        $ids = $this->getDelIds();

        $affected = $this->menu->delData($ids);
        $affected ? $this->responseApi(0) : $this->responseApi(200);
    }

    /**
     * 获取子菜单
     *
     * @param $id
     */
    public function getSubMenu($id)
    {
        $data = $this->menu->getMenuSelects($id);
        return $data ? $this->responseApi(0, "操作成功", $data) : $this->responseApi(200, "操作失败");
    }


}
