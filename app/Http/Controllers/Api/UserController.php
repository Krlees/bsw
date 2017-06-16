<?php

namespace App\Http\Controllers\Api;

use App\Models\Member;
use App\Models\UserSign;
use App\Models\UserWallet;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    public function __construct(Request $request)
    {
        $this->middleware('api.token') or $this->responseApi(1000);
        parent::__construct();
    }

    public function get(Member $member)
    {
        $result = $member->get($this->user_ses->id);
        $this->responseApi(0,'',$result);
    }

    public function getWallet(UserWallet $userWallet)
    {
        $userId = $this->user_ses->id;
        $data = $userWallet->get($userId);

        $this->responseApi(0,'',$data);
    }

    /**
     * 提交认证信息
     */
    public function postVerify(Request $request, UserSign $userSign)
    {
        $img = $request->input('img') or $this->responseApi(1004);

        $data = [
            'data' => $img,
            'user_id' => $this->user_ses->id,
            'fail_reason' => '',
            'created_at' => date('Y-m-d H:i:s')
        ];
        $res = $userSign->create($data);
        $res ? $this->responseApi(0) : $this->responseApi(9000);
    }



}