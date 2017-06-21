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
        Route::post('register', 'PublicController@register'); //普通手机注册
        Route::any('login', 'PublicController@login');
        Route::any('qq-login', 'PublicController@qqlogin');
        Route::any('wx-login', 'PublicController@wxlogin');
        Route::any('oauth-login', 'PublicController@oauthLogin'); //oauth第三方注册或登录
        Route::any('forget-pwd', 'PublicController@forgetPwd');
        Route::any('send-sms', 'PublicController@sendSms'); // 发生短信信息
        Route::any('check-valid', 'PublicController@checkValid'); // 检测验证码
        Route::any('about', 'PublicController@about'); // 关于我们
        Route::any('jpush', 'PublicController@jpush'); // 推送消息
        Route::get('adv', 'PublicController@adv'); // 获取广告位的信息
        Route::get('point-get-address/{lng},{lat}', 'PublicController@pointGetAddress'); // 获取广告位的信息
        Route::get('address-get-point', 'PublicController@addressGetPoint'); // 获取广告位的信息
        Route::get('get-new-user', 'PublicController@getNewUser'); // 获取新用户
        Route::any('alipay', 'PublicController@alipay'); // 支付宝支付
        Route::any('wxpay', 'PublicController@wxpay'); // 微信支付
        Route::any('clear-cache', 'PublicController@clearCache'); // 微信支付

    });

    // 信息接口
    Route::group(['prefix' => 'transaction'], function () {
        Route::get('get/{id}', 'TransactionController@get');
        Route::get('get-list', 'TransactionController@getList');
        Route::get('get-vip-list', 'TransactionController@getVipList');
        Route::get('get-job-resume/{id}', 'TransactionController@getJobResume'); //获取求职信息的简历库
        Route::get('collect/{id}', 'TransactionController@collect'); // 收藏
    });

    // 产品
    Route::group(['prefix' => 'product'], function () {
        Route::get('get-list', 'ProductController@getList');
    });

    // 标签
    Route::group(['prefix' => 'label'], function () {
        Route::get('get-list', 'LabelController@getList');
        Route::get('get-user/{id?}', 'LabelController@getUser');
    });

    // 订单
    Route::group(['prefix' => 'order'], function () {
        Route::post('create', 'OrderController@create');
        Route::get('get/{id}', 'OrderController@get');
        Route::get('get-list', 'OrderController@getList');
    });

    // 评论
    Route::group(['prefix' => 'comment'], function () {
        Route::get('get/{id}', 'CommentController@get'); // 根据评论ID，获取评论详情
        Route::get('get-list', 'CommentController@getList'); //根据信息ID，获取信息下的评论列表
    });

    // 首页
//    Route::group(['prefix' => 'adv'], function(){
//        Route::get('get-list', 'AdvController@getList');
//    });

    // 用户
    Route::group(['prefix' => 'user'], function () {
        Route::get('get', 'UserController@get'); //获取用户基本信息
        Route::get('get-wallet', 'UserController@getWallet'); //获取用户财务信息，余额和记录等
        Route::get('get-setting', 'UserController@getSetting'); //获取用户个人设置
        Route::post('post-verify', 'UserController@postVerify'); // 提交认证资料
        Route::get('get-verify', 'UserController@getVerify'); // 获取认证资料
        Route::post('add-friend/{followId}', 'UserController@addFriend'); // 加好友
        Route::get('delete-friend/{followId}', 'UserController@deleteFriend'); // 删除好友
        Route::get('my-friend', 'UserController@myFriend'); // 我的好友
        Route::get('my-fans', 'UserController@myFans'); // 我的粉丝
        Route::any('check-token', 'UserController@checkToken'); //
    });

    Route::group(['prefix' => 'pay'], function () {
        Route::post('wallet', 'PayController@wallet');
    });

    // 红包
    Route::group(['prefix' => 'red-packet'], function () {
        Route::get('get/{id}', 'PacketController@get'); // 获取红包详情
        Route::get('get-list', 'PacketController@getList');
        Route::get('get-user-packet', 'PacketController@getUserPacket'); // 获取用户抢到的红包
        Route::get('get-send', 'PacketController@getSend'); // 获取用户发出去的红包
        Route::post('create', 'PacketController@create'); // 创建新的红包
    });


});

Route::get('get-district/{upid?}', 'Api\BaseController@getDistrict');
Route::any('uploads', 'UploadsController@index');



