<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});
Route::get('/','admin/home/home');
Route::rule('/','index/index','get');
Route::get('hello','index/index/hello');

//后台管理模块路由-admin

Route::get('setting','admin/set/index');
Route::get('table_two','admin/set/tableTwo');

//主体框架加载Route
Route::get('admin','admin/home/home');
Route::get('main','admin/home/main');
Route::post('logout','admin/home/logout');
Route::get('menu','admin/home/menu');                   //导航栏加载
Route::post('opts','admin/home/opts');                   //导航栏加载
Route::get('/error','admin/error/index');      //ERROR页面

//登录Route
Route::get('login','admin/login/index');
Route::post('login','admin/login/login');

//管理员管理Route
Route::resource('adminer','admin/admin')->rest('edit',['GET', '/edit/:id','edit']);
Route::post('adminer/data','admin/admin/getData');
Route::post('adminer/status','admin/admin/setStatus');

//路由管理Route
Route::resource('router','admin/router')->rest('edit',['GET', '/edit/:id','edit']);
Route::post('router/data','admin/router/getData');
Route::post('router/status','admin/router/setStatus');
Route::get('router/create_modular','admin/router/createModular');
Route::post('router/save_modular','admin/router/saveModular');

//权限管理permission
Route::resource('permission','admin/permission')->rest('edit',['GET', '/edit/:id','edit']);
Route::post('permission/data','admin/permission/getData');
Route::post('permission/status','admin/permission/setStatus');

//参数设置模块config
Route::resource('config','admin/config')->rest('edit',['GET', '/edit/:id','edit'])->except(['read']);
Route::post('config/data','admin/config/getData');




return [

];
