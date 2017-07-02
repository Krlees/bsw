<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 首页
Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin', function () {
    return redirect('admin/index');
});

Route::group(['prefix' => 'components'], function () {
    Route::get('get-district/{upid?}', 'ComponentsController@getDistrict');
    Route::any('uploads', 'ComponentsController@upload');
});

Route::group(['prefix' => 'backup', 'namespace' => 'admin'], function () {
    Route::any('product', 'BackupController@product');
    Route::any('user', 'BackupController@user');
    Route::any('comment', 'BackupController@comment');
    Route::any('transaction', 'BackupController@transaction');
    Route::any('test', 'BackupController@test');
});

// 后台路由
Route::group(['namespace' => 'admin', 'prefix' => 'admin', 'middleware' => ['auth', 'auth.admin']], function () {

    Route::get('index', 'IndexController@index');
    Route::get('dashboard', 'IndexController@dashboard');

    // 超级管理员
    Route::group(['prefix' => 'admin'], function () {
        Route::any('index', 'AdminController@index');
        Route::any('add', 'AdminController@add');
        Route::any('del', 'AdminController@del');
    });

    // 案例
    Route::group(['prefix' => 'case'], function () {
        Route::any('index', 'CaseController@index');
        Route::any('add', 'CaseController@add');
        Route::any('edit/{id}', 'CaseController@edit');
        Route::any('del', 'CaseController@del');
    });

    // 用户
    Route::group(['prefix' => 'member'], function () {
        Route::any('index', 'UserController@index');
        Route::any('add', 'UserController@add');
        Route::any('edit/{id}', 'UserController@edit');
        Route::any('del', 'UserController@del');
        Route::any('project-img/{id}', 'UserController@projectImg');
        Route::any('get-sub-user/{pid}', 'UserController@getSubSelect');

        Route::any('follow', 'UserController@follow'); // 用户关注
    });

    // 创业邦
    Route::group(['prefix' => 'entre'], function () {
        Route::any('index/', 'EntreController@index');
        Route::any('add', 'EntreController@add');
        Route::any('edit/{id}', 'EntreController@edit');
        Route::any('del', 'EntreController@del');
    });

    // 标签
    Route::group(['prefix' => 'label'], function () {
        Route::any('index/', 'LabelController@index');
        Route::any('add', 'LabelController@add');
        Route::any('edit/{id}', 'LabelController@edit');
        Route::any('del', 'LabelController@del');
        Route::any('job', 'LabelController@job');
        Route::any('job-add', 'LabelController@jobAdd');
        Route::any('job-edit/{id}', 'LabelController@jobEdit');
        Route::any('job-del', 'LabelController@jobDel');
        Route::any('industry', 'LabelController@industry');
        Route::any('industry-add', 'LabelController@industryAdd');
        Route::any('industry-edit/{id}', 'LabelController@industryEdit');
        Route::any('industry-del', 'LabelController@industryDel');
    });

    // 菜单管理
    Route::group(['prefix' => 'menu'], function () {
        Route::any('index', 'MenuController@index');
        Route::any('add', 'MenuController@add');
        Route::any('edit/{id}', 'MenuController@edit');
        Route::any('del', 'MenuController@del');
        Route::any('get-sub-menu/{id}', 'MenuController@getSubMenu');
    });

    // 产品
    Route::group(['prefix' => 'product'], function () {
        Route::any('index', 'ProductController@index');
        Route::any('add', 'ProductController@add');
        Route::any('edit/{id}', 'ProductController@edit');
        Route::any('del', 'ProductController@del');
        Route::any('get-sub-class/{id}', 'ProductController@getSubClass');
        Route::any('category', 'ProductController@category');
        Route::any('category-add', 'ProductController@categoryAdd');
        Route::any('category-edit/{id}', 'ProductController@categoryEdit');
        Route::any('category-del', 'ProductController@categoryDel');
    });

    // 信息
    Route::group(['prefix' => 'transaction'], function () {
        Route::any('index/{channel_id}', 'TransactionController@index');
        Route::any('add', 'TransactionController@add');
        Route::any('edit/{id}', 'TransactionController@edit');
        Route::any('del', 'TransactionController@del');
    });

    // 订单
    Route::group(['prefix' => 'order'], function () {
        Route::any('index/{type}', 'OrderController@index');
        Route::any('edit/{id}', 'OrderController@edit');
    });

    // 文章
    Route::group(['prefix' => 'setting'], function () {
        Route::any('about', 'SettingController@about');
        Route::any('base', 'SettingController@base');
    });

});

// Auth退出
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

// 微信
Route::any('/wechat', 'WechatController@serve');
Route::group(['prefix' => 'wx-oauth', 'middreware' => ['web', 'wechat.oauth']], function () {

});

// 常用组件
Route::group(['prefix' => 'components'], function () {
    Route::post('upload', 'ComponentsController@upload');
});

Auth::routes();
