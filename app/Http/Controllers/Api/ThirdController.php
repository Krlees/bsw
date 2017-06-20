<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;

class ThirdController extends BaseController
{
    /**
     * 第三方app微信登录
     */
    public function appWxLogin(Request $request)
    {
        //$data = $request->input('data') or $this->responseApi(1004);

    }

    /**
     * 第三方qq登录
     */
    public function appQqLogin()
    {

    }

    /**
     * 第三方微信PC端扫码登录
     */
    public function pcWxLogin()
    {

    }

    /**
     * 第三方QQ PC端登录
     */
    public function pcQqLogin()
    {

    }

    /**
     * 第三方手机版微信登录
     */
    public function wapWxLogin()
    {

    }

    /**
     * 第三方手机版QQ登录
     */
    public function wapQqLogin()
    {

    }
}