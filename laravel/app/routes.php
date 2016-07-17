<?php

/**
 * General routes
 */
Route::get('/', 'MainController@index');

/** 
 * User auth routes
 */
Route::get('/login', 'AuthController@showLogin')->before('guest');
Route::post('/login', 'AuthController@doLogin')->before(array('csrf', 'guest'));

Route::get('/register', 'AuthController@showRegister')->before('guest');
Route::post('/register', 'AuthController@doRegister')->before(array('csrf', 'guest'));
Route::get('/register/confirm', 'AuthController@showRegisterConfirm')->before('guest');
Route::get('/register/verify', 'AuthController@doRegisterVerify')->before('guest');

Route::get('/forgot', 'AuthController@showForgot')->before('guest');
Route::post('/forgot', 'AuthController@doForgot')->before(array('csrf', 'guest'));
Route::get('/forgot/confirm', 'AuthController@showForgotConfirm')->before('guest');
Route::get('/forgot/reset', 'AuthController@doForgotReset')->before('guest');

/** 
 * Members routes
 */
Route::get('/users', 'MembersController@members')->before('auth');
Route::get('/users/logout', 'MembersController@logout')->before('auth');
Route::get('/users/password', 'MembersController@showChangePassword')->before('auth');
Route::post('/users/password', 'MembersController@doChangePassword')->before(array('csrf', 'auth'));

/**
 * Admin routes
 */
Route::get('/admin', 'AdminController@index');

Route::get('/admin/login', 'AdminController@showLogin')->before('guest.admin');
Route::post('/admin/login', 'AdminController@doLogin')->before(array('csrf', 'guest.admin'));

Route::get('/admin/logout', 'AdminController@doLogout')->before('auth.admin');

Route::get('/admin/dashboard', 'AdminController@showDashboard')->before('auth.admin');