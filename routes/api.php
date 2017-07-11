<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "Api" middleware group. Enjoy building your API!
|
*/

// url默认带上Api标示

Route::group(['namespace' => 'Api'], function () {
    // 公用接口
    Route::group(['prefix' => 'public'], function () {
        Route::any('register', 'PublicController@register'); //普通手机注册
        Route::any('login', 'PublicController@login');
        Route::any('qq-login', 'PublicController@qqlogin');
        Route::any('wx-login', 'PublicController@wxlogin');
        Route::any('oauth-login', 'PublicController@oauthLogin'); //oauth第三方注册或登录
        Route::any('forget-pwd', 'PublicController@forgetPwd');
        Route::any('send-sms', 'PublicController@sendSms'); // 发生短信信息
        Route::any('check-valid', 'PublicController@checkValid'); // 检测验证码
        Route::any('about', 'PublicController@about'); // 关于我们
        Route::any('jpush', 'PublicController@jpush'); // 推送消息
        Route::any('adv', 'PublicController@adv'); // 获取广告位的信息
        Route::any('point-get-address/{lng},{lat}', 'PublicController@pointGetAddress'); // 根据定位获取城市
        Route::any('address-get-point', 'PublicController@addressGetPoint'); // 根据地址获取定位
        Route::any('get-new-user', 'PublicController@getNewUser'); // 获取新用户
        Route::any('alipay', 'PublicController@alipay'); // 支付宝支付
        Route::any('wxpay', 'PublicController@wxpay'); // 微信支付
        Route::any('get-user-level', 'PublicController@getUserLevel'); // 用户等级
        Route::any('get-district/{id}', 'PublicController@getDistrict'); // 获取省市区
        Route::any('clear-cache', 'PublicController@clearCache'); // 清除缓存
        Route::any('get-nav-user', 'PublicController@getNavUser'); // 获取导航标签下的用户
        Route::any('get-nav-label', 'PublicController@getNavLabel'); // 获取所有导航标签

    });

    // 信息接口
    Route::group(['prefix' => 'transaction'], function () {
        Route::any('get/{id}', 'TransactionController@get');
        Route::any('get-list', 'TransactionController@getList');
        Route::any('get-vip-list', 'TransactionController@getVipList');
        Route::any('get-vip-info', 'TransactionController@getVipInfo');
        Route::any('get-order-list', 'TransactionController@getOrderList');
        Route::any('get-order-info', 'TransactionController@getOrderInfo');
        Route::any('get-job-resume/{id}', 'TransactionController@getJobResume'); //获取求职信息的简历库
        Route::any('collect/{id}', 'TransactionController@collect'); // 收藏
        Route::any('get-city', 'TransactionController@getCitys'); // 获取城市
        Route::any('post-click', 'TransactionController@postClick'); // 获取城市
        Route::any('post-follow', 'TransactionController@postFollow'); // 获取城市
    });

    // 产品
    Route::group(['prefix' => 'product'], function () {
        Route::any('get-list', 'ProductController@getList');
    });

    // 标签
    Route::group(['prefix' => 'label'], function () {
        Route::any('get-list', 'LabelController@getList');
        Route::any('get-user/{id?}', 'LabelController@getUser');
    });

    // 订单
    Route::group(['prefix' => 'order'], function () {
        Route::any('create', 'OrderController@create');
        Route::any('get/{id}', 'OrderController@get');
        Route::any('get-list', 'OrderController@getList');
    });

    // 评论
    Route::group(['prefix' => 'comment'], function () {
        Route::any('get/{id}', 'CommentController@get'); // 根据评论ID，获取评论详情
        Route::any('get-list', 'CommentController@getList'); //根据信息ID，获取信息下的评论列表
    });

    // 首页
//    Route::group(['prefix' => 'adv'], function(){
//        Route::any('get-list', 'AdvController@getList');
//    });

    // 用户
    Route::group(['prefix' => 'user'], function () {
        Route::any('get', 'UserController@get'); //获取用户基本信息
        Route::any('set', 'UserController@set'); //设置用户基本信息
        Route::any('get-wallet', 'UserController@getWallet'); //获取用户财务信息，余额和记录等
        Route::any('get-setting', 'UserController@getSetting'); //获取用户个人设置
        Route::any('post-verify', 'UserController@postVerify'); // 提交认证资料
        Route::any('get-verify', 'UserController@getVerify'); // 获取认证资料
        Route::any('add-friend/{followId}', 'UserController@addFriend'); // 加好友
        Route::any('delete-friend/{followId}', 'UserController@deleteFriend'); // 删除好友
        Route::any('my-friend', 'UserController@myFriend'); // 我的好友
        Route::any('my-fans', 'UserController@myFans'); // 我的粉丝
        Route::any('check-token', 'UserController@checkToken');
        Route::any('get-vip', 'UserController@getVip'); //获取开通的vip
        Route::any('get-adv', 'UserController@getAdv'); //获取广告信息
        Route::any('post-adv', 'UserController@postAdv'); //提交广告信息
        Route::any('post-label-card', 'UserController@postLabelCard'); //提交身份标签信息
        Route::any('get-transaction', 'UserController@getTransaction'); //我的订单信息
        Route::any('get-nofollow-user', 'UserController@getNofollowUser'); //获取未关注的最新用户
    });

    Route::group(['prefix' => 'pay'], function () {
        Route::any('wallet', 'PayController@wallet');
        Route::any('wxpay-app', 'PayController@wxpayApp');
        Route::any('alipay-wap', 'PayController@alipayWap');
    });

    // 红包
    Route::group(['prefix' => 'red-packet'], function () {
        Route::any('get/{id}', 'PacketController@get'); // 获取红包详情
        Route::any('get-list', 'PacketController@getList');
        Route::any('get-user-packet', 'PacketController@getUserPacket'); // 获取用户抢到的红包
        Route::any('get-send', 'PacketController@getSend'); // 获取用户发出去的红包
        Route::any('create', 'PacketController@create'); // 创建新的红包
    });


});




