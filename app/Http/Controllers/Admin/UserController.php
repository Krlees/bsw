<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Admin\BaseController;
use App\Http\Controllers\Api\PublicController;
use App\Models\Member;
use App\Traits\DistrictTraits;
use App\Traits\GaodemapTraits;
use App\Traits\ImageTraits;
use App\Traits\NetEaseTraits;
use Illuminate\Http\Request;
use Auth;
use DB;

class UserController extends BaseController
{
    use ImageTraits;
    use DistrictTraits;
    use GaodemapTraits;
    use NetEaseTraits;
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
            $results = $this->user->ajaxData($this->user->getTable(), $param, false, ['nickname', 'username']);
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
        $res = $this->getNetToken(1, 'krlee', picture_url('Uploads/Head/2017-06-27/20170627-5951b1d635ede.png'));
        if ($request->ajax()) {

            $data = $this->_helpAddEdit();
            if (!array_get($data, 'password'))
                $this->responseApi(1004);

            // 网易云通讯
            $id = $this->user->createData($this->user->getTable(), $data);
            if ($id) {
                $res = $this->getToken($id, $data['username'], picture_url($data['avatar']));
                if (array_get($res, 'code') == 200) {
                    $this->user->updateData($id, ['netease_token' => $res['info']['token']]);
                }
            }

            return $id ? $this->responseApi(0) : $this->responseApi(9000);

        } else {
            $levelData = curl_do(url('Api/public/get-user-level'));
            $levelData = \GuzzleHttp\json_decode($levelData, true);

            $this->createField('select', '用户等级', 'data[user_level_id]', $this->cleanSelect($levelData));
            $this->createField('text', '用户名', 'data[username]');
            $this->createField('text', '密码', 'data[password]');
            $this->createField('text', '昵称', 'data[nickname]');
            $this->createField('image', '头像', 'data[times]');
            $this->createField('date', '出生年月', 'data[birthday]');
            $this->createField('radio', '性别', 'data[sex]', [
                [
                    'text' => '男',
                    'value' => '男',
                ], [
                    'text' => '女',
                    'value' => '女',
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

            $levelData = curl_do(url('Api/public/get-user-level'));
            $levelData = \GuzzleHttp\json_decode($levelData, true);

            $this->createField('select', '用户等级', 'data[user_level_id]', $this->cleanSelect($levelData, 'name', 'id', $info['user_level_id']));
            $this->createField('text', '用户名', 'data[username]', $info['username']);
            $this->createField('text', '密码', 'data[password]', '', ['placeholder' => '不修改密码请为空']);
            $this->createField('text', '昵称', 'data[nickname]', $info['nickname']);
            $this->createField('image', '头像', 'data[avatar]', $info['avatar']);
            $this->createField('date', '出生年月', 'data[birthday]', $info['birthday']);
            $this->createField('radio', '性别', 'data[sex]', [
                [
                    'text' => '男',
                    'value' => '男',
                    'checked' => $info['sex'] == '男' ? true : false
                ], [
                    'text' => '女',
                    'value' => '女',
                    'checked' => $info['sex'] == '女' ? true : false
                ]
            ]);
            $this->createField('text', '签名', 'data[intro]',$info['intro']);
            $this->createField('area', '省市区', '');
            $this->createField('text', '详细地址', 'data[address]',$info['address']);

            $reponse = $this->responseForm('编辑用户', $this->getFormField());

            return view('admin/User/edit', compact('reponse', 'info'));


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

    private function _helpAddEdit()
    {
        $request = request();
        $data = $request->input('data');
        $imgs = $request->input('imgs');
        $data['avatar'] = $this->thumbImg($imgs[0], 'Head');
        $data['origin_img'] = $this->getOriginImg($imgs[0]);
        $data['province'] = $this->getByCity($request->input('province'));
        $data['city'] = $this->getByCity($request->input('city'));
        $data['area'] = $this->getByCity($request->input('area'));
        if (array_get($data, 'password'))
            $data['password'] = password($data['password']);

        $location = $this->address_get_point($data['province'] . $data['city'] . $data['area'] . $data['address']);
        $location = \GuzzleHttp\json_decode($location, true);
        if ($location['info'] == 'OK') {
            list($data['lng'], $data['lat']) = explode(",", $location['geocodes'][0]['location']);
        }

        return $data;
    }

}
