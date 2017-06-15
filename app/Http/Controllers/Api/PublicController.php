<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PublicController extends Controller
{

    public function login(Request $request)
    {
        $timestamp = $request->input('timestamp');
        $salt = $request->input('salt');
        $sign = $request->input('sign');
        if (!$timestamp || !$salt || !$sign) {
            $this->responseApi(1004);
        }

        if (create_sign($timestamp, $salt) != $sign) {
            $this->responseApi(1001);
        }



    }

    public function oauthLogin()
    {

    }

    /**
     * 统一回调
     *
     * @param $code     状态码
     * @param $msg      提示文字
     * @param $data     数据
     * @prams $href     跳转的网址
     * @author krlee <lkd0769@126.com>
     */
    public function responseApi($code = 0, $msg = '', $data = [])
    {

        if (!$msg) {
            $msg = custom_config($code);
        }

        echo json_encode(compact('code', 'msg', 'data', 'href'));
        exit;
    }

}
