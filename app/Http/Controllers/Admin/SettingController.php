<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Setting;
use Illuminate\Http\Request;
use DB;

class SettingController extends BaseController
{

    public function about(Request $request, Setting $setting)
    {
        if ($request->ajax()) {

            $data = $request->input('data');
            $result = $setting->set('base', 'about', \GuzzleHttp\json_encode($data, JSON_UNESCAPED_UNICODE));
            $result ? $this->responseApi(0) : $this->responseApi(9000);

        } else {

            $info = $setting->get('base', 'about');
            $info = \GuzzleHttp\json_decode($info, true);

            $this->createField('text', '标题', 'data[title]', $info['title']);
            $this->createField('textarea', '内容', 'data[content]', $info['content']);


            $reponse = $this->responseForm('订单详情', $this->formField);

            return view('admin/setting/about', compact('reponse'));
        }

    }


}
