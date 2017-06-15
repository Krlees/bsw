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
// 后台路由
Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => ['auth', 'auth.admin']], function () {


    Route::get('index', 'IndexController@index');
    Route::get('dashboard', 'IndexController@dashboard');

    // 权限管理
    Route::group(['prefix' => 'permission'], function () {
        Route::any('index', 'PermissionController@index');
        Route::any('add', 'PermissionController@add');
        Route::any('edit/{id}', 'PermissionController@edit');
        Route::any('del', 'PermissionController@del');
        Route::any('get-sub-perm/{id}', 'PermissionController@getSubPerm');
    });

    // 角色管理
    Route::group(['prefix' => 'role'], function () {
        Route::any('index', 'RoleController@index');
        Route::any('show/{id}', 'RoleController@show');
        Route::any('add', 'RoleController@add');
        Route::any('edit/{id}', 'RoleController@edit');
        Route::any('del', 'RoleController@del');
        Route::any('{id}', 'RoleController@getInfo');
    });

    // 管理员
    Route::group(['prefix' => 'user'], function () {
        Route::any('index', 'UsersController@index');
        Route::any('add', 'UsersController@add');
        Route::any('edit/{id}', 'UsersController@edit');
        Route::any('del', 'UsersController@del');
        Route::any('get-sub-user/{pid}', 'UsersController@getSubSelect');
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

// 常用组件
Route::group(['prefix' => 'components'], function () {
    Route::post('upload','ComponentsController@upload');
});

Auth::routes();
