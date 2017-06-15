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
        Route::any('register', 'PublicController@register');
        Route::any('login', 'PublicController@login');
        Route::any('oauth-login', 'PublicController@oauthLogin'); //oauth第三方注册或登录
        Route::any('send-sms','PublicController@sendSms'); // 发生短信信息
        Route::any('check-valid','PublicController@checkValid'); // 检测验证码
        Route::any('about','PublicController@about'); // 关于我们
        Route::any('jpush','PublicController@jpush'); // 推送消息
    });

    // 信息接口
    Route::group(['namespace' => 'transaction'], function(){
        Route::get('get/{id}', 'TransactionController@get');
        Route::get('get-list', 'TransactionController@getList');
        Route::get('get-job-resume/{id}', 'TransactionController@getJobResume'); //获取求职信息的简历库
        Route::get('collect/{id}', 'TransactionController@collect'); // 收藏
    });

    // 评论
    Route::group(['namespace' => 'comment'], function(){
        Route::get('get/{id}', 'CommentController@get'); // 根据评论ID，获取评论详情
        Route::get('get-list/{tid}', 'CommentController@getList'); //根据信息ID，获取信息下的评论列表
    });

    // 首页
    Route::group(['namespace' => 'home'], function(){
        Route::get('index', 'HomeController@index'); // 根据评论ID，获取评论详情
    });

    // 用户
    Route::group(['namespace' => 'user'], function(){
        Route::get('get/{id}', 'UserController@get'); //获取用户基本信息
        Route::get('get-list', 'UserController@getList'); //获取用户列表
        Route::get('get-wallet/{id}', 'UserController@getWallet'); //获取用户财务信息，余额和记录等
        Route::get('get-setting/{id}', 'UserController@getSetting'); //获取用户个人设置
        Route::post('postVerify/{id}', 'UserController@postVerify'); // 提交认证资料
    });

    // 用户
    Route::group(['namespace' => 'red-packet'], function(){
        Route::get('get/{id}', 'PacketController@get'); // 获取红包详情
        Route::get('get-list', 'PacketController@getList');
        Route::get('get-user-packet', 'PacketController@getUserPacket'); // 获取用户抢到的红包
        Route::get('get-send', 'PacketController@getSend'); // 获取用户发出去的红包
        Route::post('create', 'PacketController@create'); // 创建新的红包
    });

    // 用户好友
    Route::group(['namespace' => 'user-friend'], function(){
        Route::get('get-list', 'UserFriendController@getList'); // 获取我的好友列表
        Route::get('create', 'UserFriendController@create'); // 加好友
        Route::get('delete', 'UserFriendController@delete'); // 删除好友
    });


});

Route::get('get-district/{upid?}', 'Api\BaseController@getDistrict');
Route::any('uploads', 'UploadsController@index');



