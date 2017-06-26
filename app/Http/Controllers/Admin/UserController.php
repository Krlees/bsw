<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Admin\BaseController;
use App\Http\Controllers\Api\PublicController;
use App\Models\Member;
use Illuminate\Http\Request;
use Auth;
use DB;

class UserController extends BaseController
{
    private $user;

    public function __construct(Member $user)
    {
        $this->user = $user;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            // 过滤参数
            $param = $this->cleanAjaxPageParam();
            $results = $this->user->ajaxData($this->user->getTable(), $param, false, 'nickname');
            foreach ($results['rows'] as $k => &$v) {
                $v['user_id'] = $v['id'];
                $v['register_type'] = $this->getRegisterType($v['register_type']);
                $v['area_info'] = $v['province'] . $v['city'] . $v['area'] . ' ' . $v['address'];
            }

            return $this->responseAjaxTable($results['total'], $results['rows']);

        } else {
            $reponse = $this->responseTable(url('admin/member/index'), [
                'addUrl' => url('admin/member/add'),
                'editUrl' => url('admin/member/edit'),
                'removeUrl' => url('admin/member/del'),
                'autoSearch' => true
            ]);

            return view('admin/User/index', compact('reponse'));
        }

    }

    public function add(Request $request)
    {
        if ($request->ajax()) {

            $data = $request->input('data');

            $b = $this->user->createData($this->user->getTable(), $data);
            return $b ? $this->responseApi(0) : $this->responseApi(9000);

        } else {
            $levelData = curl_do(url('Api/public/get-user-level'));
            $levelData = \GuzzleHttp\json_decode($levelData, true);

            $this->createField('select', '用户等级', 'data[user_level_id]', $this->cleanSelect($levelData));
            $this->createField('text', '用户名', 'data[username]');
            $this->createField('text', '昵称', 'data[nickname]');
            $this->createField('image', '头像', 'data[times]');
            $this->createField('date', '出生年月', 'data[birthday]');
            $this->createField('radio', '性别', 'data[sex]', [
                [
                    'text' => '男',
                    'value' => 1,
                ], [
                    'text' => '女',
                    'value' => 0,
                ]
            ]);
            $this->createField('text', '签名', 'data[intro]');
            $this->createField('area', '省市区', '');
            $this->createField('text', '详细地址', 'data[address]');

            $reponse = $this->responseForm('添加用户', $this->getFormField());

            return view('admin/User/add', compact('reponse'));

        }
    }

    public function edit($id, Request $request)
    {
        if ($request->ajax()) {

            $data = $request->input('data');

            $b = $this->user->updateData($id, $data);
            return $b ? $this->responseApi(0) : $this->responseApi(9000);

        } else {
            $info = $this->user->get($id);

            $cateData = obj2arr($this->user->getCateList());
            $this->createField('select', '产品分类', 'data[category_id]', $this->cleanSelect($cateData, 'name', 'id', $info->category_id));
            $this->createField('text', '名称', 'data[name]', $info->name);
            $this->createField('text', '价钱', 'data[price]', $info->price);
            $this->createField('text', '有效期', 'data[times]', $info->times, ['placeholder' => '填写天数,0则不限期']);
            $this->createField('textarea', '参数', 'data[attrs]', $info->attrs);

            $reponse = $this->responseForm('编辑产品', $this->getFormField());

            return view('admin/User/edit', compact('reponse'));

        }
    }

    public function del()
    {
        $ids = $this->getDelIds();
        $result = $this->user->delData($this->user->getTable(), $ids);
        $result ? $this->responseApi(0) : $this->responseApi(9000);
    }

    /**
     * 经营项目图片
     */
    public function projectImg($id, Request $request)
    {
        $result = DB::table('user_project_img')->where('user_id', $id)->get();

        return view('admin.user.project_img', compact('result'));
    }

}
