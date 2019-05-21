<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
//


//设置全局变量规则
Route::pattern([
    'id'   => '\d+',
    'goods_id'  => '\d+',
    'coupon_id'  => '\d+',
    'address_id'  => '\d+',
    'order_id'  => '\d+',
    'type'  => '\d+',
    'page'  => '\d+',
    'limit'  => '\d+',
]);

/*
 *
 * 前端路由处理模块
 *
 * */

//获取验证码处理
Route::post('/getCode/:mobile/:type/[:over]','index/Verify/getCode');
//图片上传处理
Route::get('uploader','admin/Uploader/uploader');





















return [

];
