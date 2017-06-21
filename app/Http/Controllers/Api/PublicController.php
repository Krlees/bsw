<?php

namespace App\Http\Controllers\Api;

use App\Models\Adv;
use App\Models\Member;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Setting;
use App\Models\UserLoginRecord;
use App\Models\UserOauth;
use App\Models\UserToken;
use App\Traits\GaodemapTraits;
use App\Traits\NetEaseTraits;
use App\Traits\SmsTraits;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Cache;

class PublicController extends BaseController
{

    use NetEaseTraits;
    use SmsTraits;
    use GaodemapTraits;

    public function __construct()
    {
    }

    /**
     * 手机注册用户
     * @param Request $request
     * @param Member $member
     */
    public function register(Request $request, Member $member)
    {
        $timestamp = $request->input('timestamp');
        $salt = $request->input('salt');
        $sign = $request->input('sign');
        $username = $request->input('username');
        $password = $request->input('password');
        $register_type = $request->input('register_type');
        $lng = $request->input('lng');
        $lat = $request->input('lat');
        $valid = $request->input('valid');
        $type = $request->input('register', 'mobile');
        if (!$valid || !$timestamp || !$salt || !$sign || !$username || !$password || !$register_type || !$lng || !$lat) {
            $this->responseApi(1004);
        } elseif ($register_type != 'mobile') {
            $this->responseApi(80001, "注册方式不对，register_type必须填写mobile");
        } elseif (!check_mobile_format($username)) {
            $this->responseApi(80001, "手机格式不正确");
        } elseif (!check_valid($username, $type, $valid)) {
            $this->responseApi(80001, "验证码错误");
        } elseif (create_sign($timestamp, $salt) != $sign) {
            $this->responseApi(1001);
        }

        // 高德地图定位

        $created_at = date('Y-m-d H:i:s', $timestamp);
        $password = password($password);
        $data = compact('username', 'password', 'created_at', 'register_type', 'lat', 'lng');
        $result = $member->create($data);
        $result ? $this->responseApi(0) : $this->responseApi(9000);
    }

    /**
     * 正常账户密码登录
     * @param Request $request
     * @param Member $member
     * @return bool
     */
    public function login(Request $request, Member $member, UserLoginRecord $userLoginRecord, UserToken $userToken)
    {
        $timestamp = $request->input('timestamp');
        $salt = $request->input('salt');
        $sign = $request->input('sign');
        $username = $request->input('username');
        $pwd = $request->input('password');
        $lng = $request->input('lng');
        $lat = $request->input('lat');
        if (!$timestamp || !$salt || !$sign || !$username || !$pwd) {
            $this->responseApi(1004, '', $request->all());
        }

        // 判断秘银是否一致
        if (create_sign($timestamp, $salt) != $sign) {
            $this->responseApi(1001);
        }

        // 检测登录信息
        $user = $member->checkLogin($username, $pwd) or $this->responseApi(1002);
        unset($user->password);
        $user->user_id = $user->id;

        // 网易云通讯token
        $user->netease_token = "";
        if (empty($user->netease_token)) {
            $res = $this->getNetToken($user->id, $user->nickname, picture_url($user->avatar));
            if ($res) {
                $member->updateData($user->id, ['netease_token' => $res['info']['token']]);
                $user->netease_token = $res['info']['token'];
                $user->accid = $res['info']['accid'];

                cache()->forever($request->input('token'), $this->user_ses);
            }
        }

        // 查询出token
        $token = $userToken->getForUserId($user->id);
        if (!$token) {
            // 生成token
            $token = create_token($user->id, $salt);
            $userToken->create($user->id, $token);

            cache()->forever($token, $user);
        } elseif (!cache()->has($token)) {
            cache()->forever($token, $user);
        }


        // 记录用户登录
        $userLoginRecord->create($user->id, $lng, $lat);
        $user->token = $token;

        $this->responseApi(0, '', obj2arr($user));

    }

    /**
     * 第三方微信登录
     * @param Request $request
     * @param UserOauth $userOauth
     */
    public function wxLogin(Request $request, Member $member)
    {
        $data = $request->all();
        unset($data['token']);

        $count = \DB::table($member->getTable())->where('openid', $data['open_id'])->count();
        if (!$count) {
            $result = $member->create($data);
        } else {
            $result = $member->updateData($this->user_ses->id, $data);
        }

        $result ? $this->responseApi(0) : $this->responseApi(9000);
    }

    /**
     * 第三方qq登录
     * @param Request $request
     * @param UserOauth $userOauth
     */
    public function qqLogin(Request $request, Member $member)
    {
        $data = $request->all();
        unset($data['token']);

        $count = \DB::table($member->getTable())->where('openid', $data['open_id'])->count();
        if (!$count) {
            $result = $member->create($data);
        } else {
            $result = $member->updateData($this->user_ses->id, $data);
        }

        $result ? $this->responseApi(0) : $this->responseApi(9000);
    }

    /**
     * 忘记密码
     * @param Request $request
     * @param Member $member
     */
    public function forgetPwd(Request $request, Member $member)
    {
        $valid = $request->input('valid');
        $mobile = $request->input('mobile');
        $pwd = $request->input('password');
        $type = $request->input('type');
        if (!$valid || !$mobile || !$pwd || !$type) {
            $this->responseApi(1004);
        } elseif (!check_valid($mobile, $type, $valid)) {
            $this->responseApi(80001, '验证码错误');
        }

        $result = $member->forgetPwd($mobile, password($pwd));
        $result ? $this->responseApi(0) : $this->responseApi(80001, '操作失败');
    }

    /**
     * 发送验证码
     * @param Request $request
     */
    public function sendSms(Request $request)
    {
        $type = $request->input('type') or $this->responseApi(1004);
        $mobile = $request->input('mobile') or $this->responseApi(1004);
        if (!check_mobile_format($mobile)) {
            $this->responseApi(80001, '手机格式不正确');
        }

        // 发送验证码
        $result = $this->sendSmsMsg($mobile, $type);
        $result ? $this->responseApi(0) : $this->responseApi(80001);
    }

    /**
     * 关于我们
     */
    public function about(Setting $setting)
    {
        $data = $setting->get('base', 'about');

        return $data;
    }

    /**
     * 获取广告位信息
     * @param Request $request
     * @param Adv $adv
     */
    public function adv(Request $request, Adv $adv)
    {
        $classify = $request->input('classify') or $this->responseApi(1004);

        $result = $adv->get($classify);
        $result ? $this->responseApi(0, '', $result) : $this->responseApi(80001);
    }

    /*
     * formatted_address: "福建省宁德市霞浦县",
province: "福建省",
citycode: "0593",
city: "宁德市",
district: "霞浦县",
    location: "120.005643,26.885204",
    */
    /**
     * 根据精纬度获取城市
     * @param $lng
     * @param $lat
     */
    public function pointGetAddress($lng, $lat)
    {
        $city = "";
        $result = $this->point_get_address($lng, $lat);
        $res = json_decode($result);
        if ($res->info == 'OK') {
            //$area_info = $res->geocodes->formatted_address; //具体地址
            $city = $res->regeocode->addressComponent->city;
        }

        $this->responseApi(0, '', compact('city'));
    }

    /**
     * 根据精纬度获取城市
     * @param $lng
     * @param $lat
     */
    public function addressGetPoint(Request $request)
    {
        $location = "";
        $address = $request->input('address') or $this->responseApi(1004);
        $city = $request->input('city');

        $result = $this->address_get_point($address, $city);
        $res = json_decode($result);
        if ($res->info == 'OK') {
            $location = $res->geocodes[0]->location;
        }

        $this->responseApi(0, '', compact('location'));
    }

    /**
     * 分享
     */
    public function share()
    {

    }

    public function getNewUser(Member $member)
    {
        $pages = $this->pageInit();
        $data = $member->getList($pages['page'], $pages['limit']);

        $this->responseApi(0, '', $data);
    }


    public function alipay(Order $order)
    {
        $id = 2;

        $orderData = $order->get($id);

        // 创建支付单。
        $alipay = app('alipay.web');
        $alipay->setOutTradeNo($orderData->order_sn);
        $alipay->setTotalFee($orderData->price);
        $alipay->setSubject($orderData->order_sn);
        $alipay->setBody($orderData->order_sn);

        //$alipay->setQrPayMode('4'); //该设置为可选，添加该参数设置，支持二维码支付。

        // 跳转到支付页面。
        return redirect()->to($alipay->getPayLink());
    }

    public function clearCache()
    {
        cache()->flush();
        $this->responseApi(0);
    }

}
