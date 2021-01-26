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
use think\Route;

//域名路由：让指定域名http://adminapi.pyg.com/  访问adminapi模块
Route::domain('adminapi',function(){
	//后台路由统一编写在此处
	Route::get('/','adminapi/index/index');
    Route::post('login', 'adminapi/Login/login');
    Route::get('captcha/:id', "\\think\\captcha\\CaptchaController@index");//访问图片需要
    Route::get('captcha', 'adminapi/Login/captcha');
    Route::get('logout', 'adminapi/Login/logout');
    Route::resource('auths','adminapi/auth',[],['id'=>'\d+']);
    Route::get('nav','adminapi/auth/nav');
    Route::resource('roles','adminapi/role',[],['id'=>'\d+']);
    Route::resource('admins','adminapi/admin');
    Route::resource('categorys','adminapi/category',[],['id'=>'\d+']);
    Route::post('logo','adminapi/upload/logo');
    Route::post('images','adminapi/upload/images');
    Route::resource('brands','adminapi/brand',[],['id'=>'\d+']);
    Route::resource('types','adminapi/type',[],['id'=>'\d+']);
});


