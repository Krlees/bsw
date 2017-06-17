<?php

namespace App\Http\Controllers\Api;

use App\Models\Member;
use App\Models\UserFollow;
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

    /**
     * 获取用户基本信息
     * @param Member $member
     */
    public function get(Member $member)
    {
        $result = $member->get($this->user_ses->id);
        $this->responseApi(0, '', $result);
    }

    /**
     * 获取钱包信息
     * @param UserWallet $userWallet
     */
    public function getWallet(UserWallet $userWallet)
    {
        $userId = $this->user_ses->id;
        $data = $userWallet->get($userId);

        $this->responseApi(0, '', $data);
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

    /**
     * 获取认证信息
     */
    public function getVerify(UserSign $userSign)
    {
        $status_arr = ['失败', '审核中', '通过认证'];

        $result = $userSign->get($this->user_ses->id);
        $data = $result->data;
        $status = $result->status;
        $status_str = $status_arr[$result->status]; //1审核中 2通过认证 0失败

        $this->responseApi(0, '', compact('data', 'status', 'status_str'));
    }

    /**
     * 加好友【加关注】
     * @param $followId
     */
    public function addFriend($followId, UserFollow $userFollow)
    {
        if($userFollow->check($this->user_ses->id, $followId)){
            return $this->responseApi(80001,'该好友不存在！');
        }

        $result = $userFollow->create($this->user_ses->id, $followId);
        $result ? $this->responseApi(0) : $this->responseApi(9000);
    }

    /**
     * 删除好友【取消关注】
     * @param $followId
     */
    public function deleteFriend($followId, UserFollow $userFollow)
    {
        if(!$userFollow->check($this->user_ses->id, $followId)){
            return $this->responseApi(80001,'该好友不存在！');
        }

        $result = $userFollow->del($this->user_ses->id, $followId);
        $result ? $this->responseApi(0) : $this->responseApi(9000);
    }

    /**
     * 我的好友
     * @param UserFollow $userFollow
     */
    public function myFriend(UserFollow $userFollow)
    {
        $result = $userFollow->getFollow($this->user_ses->id);
        $this->responseApi(0,'',$result);
    }

    /**
     * 我的粉丝
     * @param UserFollow $userFollow
     */
    public function myFans(UserFollow $userFollow)
    {
        $result = $userFollow->getFans($this->user_ses->id);
        $this->responseApi(0,'',$result);
    }
}