<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Http\Controllers\Api\PublicController;
use App\Models\District;
use App\Models\Member;
use App\Models\UserFriend;
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
        if ($request->ajax()) {

            $data = $this->_helpAddEdit();
            if (!array_get($data, 'password'))
                $this->responseApi(1004);

            // 网易云通讯
            $id = $this->user->createData($this->user->getTable(), $data);
            if ($id) {
                $res = $this->getNetToken($id, $data['username'], picture_url($data['avatar']));
                if (array_get($res, 'code') == 200) {
                    $this->user->updateData($id, ['netease_token' => $res['info']['token']]);
                }
            }

            return $id ? $this->responseApi(0) : $this->responseApi(9000);

        } else {
            $levelData = curl_do(url('Api/public/get-user-level'));
            $levelData = \GuzzleHttp\json_decode($levelData, true);
            $provinces = curl_do(url('components/get-district/0'));
            $provinces = \GuzzleHttp\json_decode($provinces);

            $this->createField('select', '用户等级', 'data[user_level_id]', $this->cleanSelect($levelData));
            $this->createField('text', '用户名', 'data[username]');
            $this->createField('text', '密码', 'data[password]');
            $this->createField('text', '昵称', 'data[nickname]');
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
            $this->createField('text', '详细地址', 'data[address]');

            $reponse = $this->responseForm('添加用户', $this->getFormField());

            return view('admin/User/add', compact('reponse', 'provinces'));

        }
    }

    public function edit($id, Request $request, District $district)
    {
        if ($request->ajax()) {

            $tab = $request->input('tab'); //判断是否更改切换状态
            if ($tab) {
                $data = $request->input('data');
            } else {
                $data = $this->_helpAddEdit();
            }

            $b = $this->user->updateData($id, $data);
            return $b ? $this->responseApi(0) : $this->responseApi(9000);

        } else {
            $info = $this->user->get($id);
            $province_id = DB::table($district->getTable())->where('name', $info['province'])->where('level', 1)->first(['id']);
            $city_id = DB::table($district->getTable())->where('name', 'like', '%' . $info['city'] . '%')->where('level', 2)->first(['id']);
            $info['province_id'] = $province_id ? $province_id->id : 0;
            $info['city_id'] = $city_id ? $city_id->id : 0;
            $provinces = curl_do(url('components/get-district/0'));
            $provinces = \GuzzleHttp\json_decode($provinces);


            $levelData = curl_do(url('Api/public/get-user-level'));
            $levelData = \GuzzleHttp\json_decode($levelData, true);

            $this->createField('select', '用户等级', 'data[user_level_id]', $this->cleanSelect($levelData, 'name', 'id', $info['user_level_id']));
            $this->createField('text', '用户名', 'data[username]', $info['username']);
            $this->createField('text', '密码', 'data[password]', '', ['placeholder' => '不修改密码请为空']);
            $this->createField('text', '昵称', 'data[nickname]', $info['nickname']);
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
            $this->createField('text', '签名', 'data[intro]', $info['intro']);
            $this->createField('text', '详细地址', 'data[address]', $info['address']);

            $reponse = $this->responseForm('编辑用户', $this->getFormField());

            return view('admin/User/edit', compact('reponse', 'info', 'provinces'));


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

    public function follow(Request $request, UserFriend $friend)
    {
        if ($request->ajax()) {

            // 过滤参数
            $param = $this->cleanAjaxPageParam();
            $results = $friend->ajaxData($friend->getTable(), $param);
            foreach ($results['rows'] as $k => &$v) {
                $username = $this->user->get($v['user_id']);
                $follow_name = $this->user->get($v['follow_id']);
                $v['username'] = $username['username'];
                $v['follow_name'] = $follow_name['username'];
            }

            return $this->responseAjaxTable($results['total'], $results['rows']);

        } else {
            $reponse = $this->responseTable(url('admin/member/follow'), [
                'addUrl' => null,
                'editUrl' => null,
                'removeUrl' => null,
                'autoSearch' => false
            ]);

            return view('admin/User/follow', compact('reponse'));
        }
    }

    private function _helpAddEdit()
    {
        $request = request();
        $data = $request->input('data');
        $imgs = $request->input('imgs');
        if ($imgs) {
            $data['avatar'] = $this->thumbImg($imgs[0], 'Head');
            $data['origin_img'] = $data['avatar'];
        }

        $data['province'] = $this->getByCity($request->input('province'));
        $data['city'] = $this->getByCity($request->input('city'));
        //$data['area'] = $this->getByCity($request->input($data['area']));
        if (array_get($data, 'password'))
            $data['password'] = password($data['password']);
        else
            unset($data['password']);

        $location = $this->address_get_point($data['province'] . $data['city'] . $data['address']);
        $location = \GuzzleHttp\json_decode($location, true);
        if ($location['info'] == 'OK' && $location['geocodes']) {
            list($data['lng'], $data['lat']) = explode(",", $location['geocodes'][0]['location']);
        }

        return $data;
    }

}
