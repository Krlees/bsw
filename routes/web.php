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

Route::group(['prefix' => 'backup', 'namespace' => 'Admin'], function () {
    Route::any('product', 'BackupController@product');
    Route::any('user', 'BackupController@user');
    Route::any('comment', 'BackupController@comment');
    Route::any('transaction', 'BackupController@transaction');
    Route::any('test', 'BackupController@test');
});

// 后台路由
Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => ['auth', 'auth.admin']], function () {


    Route::get('index', 'IndexController@index');
    Route::get('dashboard', 'IndexController@dashboard');


//    // 权限管理
//    Route::group(['prefix' => 'product'], function () {
//        Route::any('index', 'ProductController@index');
//        Route::any('add', 'ProductController@add');
//        Route::any('edit/{id}', 'ProductController@edit');
//        Route::any('del', 'ProductController@del');
//        Route::any('category', 'ProductController@category');
//        Route::any('category-add', 'ProductController@categoryAdd');
//        Route::any('category-edit/{id}', 'ProductController@categoryEdit');
//        Route::any('category-del', 'ProductController@categoryDel');
//    });

    // 角色管理
    Route::group(['prefix' => 'role'], function () {
        Route::any('index', 'RoleController@index');
        Route::any('show/{id}', 'RoleController@show');
        Route::any('add', 'RoleController@add');
        Route::any('edit/{id}', 'RoleController@edit');
        Route::any('del', 'RoleController@del');
        Route::any('{id}', 'RoleController@getInfo');
    });

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
        Route::any('entre/', 'EntreController@entre');
        Route::any('entre-add', 'EntreController@entreAdd');
        Route::any('entre-edit/{id}', 'EntreController@entreEdit');
        Route::any('entre-del', 'EntreController@entreDel');
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

    // 订单
    Route::group(['prefix' => 'order'], function () {
        Route::any('index', 'OrderController@index');
        Route::any('detail/{id}', 'OrderController@detail');
    });

    // 文章
    Route::group(['prefix' => 'news'], function () {
        Route::any('index', 'NewsController@index');
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
