<?php

namespace App\Http\Controllers\Api;

use App\Models\Comment;
use App\Models\Label;
use App\Models\Member;
use App\Models\Menu;
use App\Models\NavLabel;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserAdv;
use App\Models\UserFriend;
use App\Models\UserFriendLink;
use App\Models\UserInvite;
use App\Models\UserJuan;
use App\Models\UserLabelCard;
use App\Models\UserSign;
use App\Models\UserVip;
use App\Models\UserWallet;
use App\Traits\ImageTraits;
use App\Traits\NetEaseTraits;
use Illuminate\Http\Request;
use DB;

class UserController extends BaseController
{
    use NetEaseTraits;
    use ImageTraits;

    public function __construct(Request $request)
    {
        $this->middleware('api.token') or $this->responseApi(1000);
        parent::__construct();
        if (empty($this->user_ses)) {
            $this->responseApi(1000);
        }


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
     * 获取邀请收益
     */
    public function getInvite(UserInvite $invite)
    {
        $invite_money = \DB::table($invite->getTable())->where('user_id', $this->user_ses->id)->sum('commy'); //收益
        $invite_people = \DB::table($invite->getTable())->where('user_id', $this->user_ses->id)->count(); //总人数

        return compact('invite_money', 'invite_people');
    }

    /**
     * 获取邀请收益记录
     */
    public function getInviteList(UserInvite $invite)
    {
        $pages = $this->pageInit();
        $rows = $invite->getList($invite->getTable(), $pages['page'], $pages['liit']);

        $this->responseApi(0, '', $rows);
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
     * 提交个人中心下的项目图片
     */
    public function postProjectImg(Request $request)
    {
        $imgs = $request->input('imgs') or $this->responseApi(1004);
        foreach ($imgs as $img) {
            $img = $this->thumbImg($img, 'project');
            $user_id = $this->user_ses->id;
            $created_at = date('Y-m-d H:i:s');
            $r = DB::table('user_project_img')->insert(compact('user_id', 'img', 'created_at'));
            if (!$r)
                $this->responseApi(9000);
        }

        $this->responseApi(0);
    }

    /**
     * 加好友【加关注】
     * @param $followId
     */
    public function addFriend($followId, UserFriend $userFollow)
    {
        if ($userFollow->check($this->user_ses->id, $followId)) {
            return $this->responseApi(80001, '该好友已存在！');
        }

        $result = $userFollow->create($this->user_ses->id, $followId);
        $result ? $this->responseApi(0) : $this->responseApi(9000);
    }

    /**
     * 删除好友【取消关注】
     * @param $followId
     */
    public function deleteFriend($followId, UserFriend $userFollow)
    {
        if (!$userFollow->check($this->user_ses->id, $followId)) {
            return $this->responseApi(80001, '该好友不存在！');
        }

        $result = $userFollow->del($this->user_ses->id, $followId);
        $result ? $this->responseApi(0) : $this->responseApi(9000);
    }

    /**
     * 我的好友
     * @param UserFriend $userFollow
     */
    public function myFriend(Request $request, Member $member, UserFriend $userFollow, NavLabel $label)
    {
        $label_name = $request->input('label_name', '');
        $label_id = $label_name ? $label->nameById($label_name) : 0;

        $pages = $this->pageInit();

        $result = $userFollow->getFollow($pages, $this->user_ses->id, $label_id);
        foreach ($result as &$v) {
            $v['created_at'] = date('Y-m-d H:i:s', $v['created_at']);
            $users = DB::table($member->getTable())->find($v['user_id'], ['avatar', 'nickname', 'netease_token']);
            $v['nickname'] = $users ? $users->nickname : '';
            $v['avatar'] = $users ? $users->avatar : '';
            $v['netease_token'] = $users ? $users->netease_token : '';
        }

        $this->responseApi(0, '', $result);
    }

    /**
     * 我的粉丝
     * @param UserFriend $userFollow
     */
    public function myFans(UserFriend $userFollow, Member $member)
    {
        $result = $userFollow->getFans($this->user_ses->id);
        foreach ($result as &$v) {
            $v['created_at'] = date('Y - m - d H:i:s', $v['created_at']);
            $users = DB::table($member->getTable())->find($v['user_id'], ['avatar', 'nickname', 'netease_token']);
            $v['nickname'] = $users ? $users->nickname : '';
            $v['avatar'] = $users ? $users->avatar : '';
            $v['netease_token'] = $users ? $users->netease_token : '';
        }

        $this->responseApi(0, '', $result);
    }

    /**
     * 检查token
     */
    public function checkToken(Request $request, Member $member)
    {
        $registration_id = $request->input('RegistrationID') or $this->responseApi(1004);

        $this->user_ses = (object)$this->user_ses;
        // 判断网易云通讯token
        $res = $this->getNetToken($this->user_ses->id, $this->user_ses->nickname, picture_url($this->user_ses->avatar));
        if ($res) {
            $member->updateData($this->user_ses->id, ['netease_token' => $res['info']['token']]);
            $this->user_ses->netease_token = $res['info']['token'];
            $this->user_ses->accid = $res['info']['accid'];
            cache()->forever($request->input('token'), $this->user_ses);
        }

        $result = $member->updateData($this->user_ses->id, compact('registration_id'));
        if ($result) {
            $this->user_ses->registration_id = $registration_id; // jpush极光推送ID

            cache()->forever($request->input('token'), $this->user_ses);
            $this->responseApi(0, '', obj2arr($this->user_ses));
        }

        $this->responseApi(9000);
    }

    /**
     * 每完善一点资料就送一张卷
     * @param Request $request
     * @param Member $member
     * @param UserJuan $userJuan
     */
    public function set(Request $request, Member $member, UserJuan $userJuan)
    {
        $field = $request->input('key');
        $value = $request->input('value');


        $checkField = DB::table($member->getTable())->where('id', $this->user_ses->id)->where($field, ' = ', '')->count();
        $result = $member->updateData($this->user_ses->id, [$field => $value]);
        if ($result) {
            if ($checkField && ($field != 'avatar' || $field != 'nickname' || $field != 'sex')) {
                // 送劵
                $where[] = ['user_id', ' = ', $this->user_ses->id];
                $where[] = ['juan_id', ' = ', 1];
                $count = $userJuan->getCount($userJuan->getTable(), $where);
                if ($count > 0) {
                    $r = DB::table($userJuan->getTable())->where($where)->increment('nums');
                } else {
                    $r = $userJuan->createData($userJuan->getTable(), [
                        'user_id' => $this->user_ses->id,
                        'nums' => 1,
                        'created_at' => date('Y - m - d H:i:s')
                    ]);
                }
            }
            $this->responseApi(0);
        }

        $this->responseApi(9000);
    }

    public function getVip(UserVip $userVip)
    {
        $data = $userVip->getAll($userVip->getTable());

        return $data;
    }

    /**
     * 获取已购买的信息
     */
    public function getTransaction(Request $request, Transaction $transaction, Comment $comment)
    {
        $where = [];
        $pages = $this->pageInit();
        $type = (int)$request->input('type') or $this->responseApi(1004);
        switch ($type) {
            case 1: // 已解锁的信息
                $rows = DB::table($transaction->transactionPayRecordTb() . ' as a')->join($transaction->getTable() . ' as b', 'a . transaction_id', ' = ', 'b . id')
                    ->where('a . user_id', ' = ', $this->user_ses->id)
                    ->offset($pages['page'] * $pages['limit'])
                    ->limit($pages['limit'])
                    ->get(['b . id', 'b . title', 'b . content', 'b . user_id', 'b . label_id', 'b . created_at', 'b . city', 'b . ext1']);
                foreach ($rows as $v) {
                    $coverImg = DB::table($transaction->transactionImg())->where('transaction_id', $v->id)->where('is_cover', 1)->first();
                    $v->cover = $coverImg ? $coverImg->img_thumb : '';
                    $v->collect_count = DB::table($transaction->transctionFollowTb())->where('transaction_id', $v->id)->count();
                    $v->comment_count = DB::table($comment->getTable())->where('transaction_id', $v->id)->count();
                    $v->addtime = date('Y - m - d H:i:s', $v->created_at);
                }
                $rows = obj2arr($rows);

                break;

            case 2: // 已发布的信息
                $where[] = ['user_id', ' = ', $this->user_ses->id];
                $rows = $transaction->getList($pages['page'], $pages['limit'], $where);
                foreach ($rows as &$v) {
                    $v['collect_count'] = DB::table($transaction->transctionFollowTb())->where('transaction_id', $v['id'])->count();
                    $v['comment_count'] = DB::table($comment->getTable())->where('transaction_id', $v['id'])->count();
                }
                break;

            case 3:
                $rows = DB::table($transaction->transctionFollowTb() . ' as a')->join($transaction->getTable() . ' as b', 'a . transaction_id', ' = ', 'b . id')
                    ->where('a . user_id', ' = ', $this->user_ses->id)
                    ->offset($pages['page'] * $pages['limit'])
                    ->limit($pages['limit'])
                    ->get(['b . id', 'b . title', 'b . content', 'b . user_id', 'b . label_id', 'b . created_at', 'b . city', 'b . ext1']);
                foreach ($rows as $v) {
                    $coverImg = DB::table($transaction->transactionImg())->where('transaction_id', $v->id)->where('is_cover', 1)->first();
                    $v->cover = $coverImg ? $coverImg->img_thumb : '';
                    $v->collect_count = DB::table($transaction->transctionFollowTb())->where('transaction_id', $v->id)->count();
                    $v->comment_count = DB::table($comment->getTable())->where('transaction_id', $v->id)->count();
                    $v->addtime = date('Y - m - d H:i:s', $v->created_at);
                }
                break;
            case 4:

        }
        if (!$rows)
            $this->responseApi(0);

        $this->responseApi(0, '', $rows);

    }

    /**
     * 检测资料完善度
     */
    public function checkWs()
    {

    }

    public function postAdv(Request $request, UserAdv $adv)
    {
        $position_id = (int)$request->input('position_id') or $this->responseApi(1004);
        $name = $request->input('name') or $this->responseApi(1004);
        $desc = $request->input('desc') or $this->responseApi(1004);
        $img = $request->input('img') or $this->responseApi(1004);
        $created_at = date('Y - m - d H:i:s');
        $user_id = $this->user_ses->id;

        $img = $this->thumbImg($img, 'adv');

        $id = $adv->createData($adv->getTable(), compact('position_id', 'name', 'desc', 'img', 'created_at', 'user_id'));
        $id ? $this->responseApi(0) : $this->responseApi(9000);
    }

    public function getAdv(Request $request, UserAdv $adv)
    {
        $position_id = $request->input('position_id') or $this->responseApi(1004);
        $result = DB::table($adv->getTable())->where('position_id', $position_id)->where('user_id', $this->user_ses->id)->first();

        return $result;
    }

    public function postLabelCard(Request $request, UserLabelCard $card)
    {
        $data = $request->input('data') or $this->responseApi(1004);
        $label_id = $request->input('label_id') or $this->responseApi(1004);

        $data = \GuzzleHttp\json_decode($data, true);
        if (!is_array($data)) {
            $this->responseApi(80001, "data数据格式错误");
        }

        $card->create($label_id, $this->user_ses->id, $data);
        $this->responseApi(0);

    }

    /**
     * 编写推广链接
     */
    public function postFriendLink(Request $request, UserFriendLink $link)
    {
        $product_id = $request->input('product_id') or $this->responseApi(1004);
        $url = $request->input('url') or $this->responseApi(1004);
        $created_at = date('Y - m - d H:i:s');
        $user_id = $this->user_ses->id;

        $where[] = ['product_id', ' = ', $product_id];
        $where[] = ['user_id', ' = ', $user_id];
        $count = DB::table($link->getTable())->where($where)->count();
        if ($count) {
            $result = DB::table($link->getTable())->where($where)->update(['url' => $url]);
        } else {
            $result = DB::table($link->getTable())->insert(compact('product_id', 'user_id', 'created_at', 'url'));
        }

        $result ? $this->responseApi(0) : $this->responseApi(9000);
    }

    /**
     * 获取推广
     * @param Request $request
     * @param UserFriendLink $link
     */
    public function getFriendLink(Request $request, UserFriendLink $link, Product $product)
    {

        $info = DB::table($product->getTable() . ' as a')->leftJoin($link->getTable() . ' as b', 'a . id', ' = ', 'b . product_id')->where('a . category_id', 11)->where('b . user_id', $this->user_ses->id)->get();

        $this->responseApi(0, '', obj2arr($info));

    }

    /**
     * 获取未关注的最新用户
     */
    public function getNofollowUser(Member $member)
    {
        $pages = $this->pageInit();
        $result = $member->getNoFollowUser($pages, $this->user_ses->id);

        $this->responseApi(0, '', $result);
    }


}