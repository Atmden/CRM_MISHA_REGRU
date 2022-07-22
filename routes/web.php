<?php

use Illuminate\Support\Facades\Route;

//Auth::routes();


Route::get('/{any}', function () {
    return view('vue.auth');
})->where('any', '^(?!admin)^(?!pages).*$')->name('auth');

Route::get('/pages/{any?}', function () {
    return view('vue.main');
})->where('any', '.*')->name('main');

Route::get('/password/reset/{token}', ['as' => 'password_reset', 'uses' => 'API\UserController@password_reset']);

Route::get('/', 'PageController@login')->name('login');
Route::post('/', 'Auth\LoginController@login');

Route::get('/admin/login', ['as' => 'admin.login', 'uses' => 'AuthAdmin\LoginController@showLoginForm']);

Route::post('/admin/login', ['as' => 'adminpostlogin', 'uses' => 'AuthAdmin\LoginController@login']);
Route::post('/admin/logout', ['as' => 'admin.logout', 'uses' => 'AuthAdmin\LoginController@logout']);

//Route::get('/', ['as' => 'home', 'uses' => 'PageController@home']);
Route::get('/acounts', ['as' => 'acounts', 'middleware' => 'auth', 'uses' => 'PageController@home']);
