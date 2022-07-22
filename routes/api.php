<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/check', function (Request $request){
    return $request->user();
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:sanctum')->get('/athenticated', function () {
    return true;
});
Route::middleware('auth:sanctum')->group( function () {
    //Акаунты
    Route::resource('accounts', 'API\AccountController');
    Route::resource('socnets', 'API\SocnetController');
    Route::resource('statuses', 'API\StatusController');
    Route::resource('tags', 'API\TagController');
    Route::get('/my_accounts', 'API\AccountController@my_accounts');
    Route::get('/account_to_edit/{id}', 'API\AccountController@account_to_edit');
    Route::get('/get_new_account', 'API\AccountController@get_new_account');
    Route::post('/add_new_account', 'API\AccountController@add_new_account');
    Route::post('/add_new_user/{id}', 'API\UserController@add_attach_user');
    Route::post('/add_new_plan','API\PlanController@add_new_plan');
    Route::post('/save_edit_plan','API\PlanController@save_edit_plan');
    Route::get('/get_comments_plan/{id}','API\PlanController@get_comments_plan');
    Route::post('/save_comment_plan','API\PlanController@save_comment_plan');
    Route::post('/delete_comment','API\PlanController@delete_comment');
    Route::post('/apply_comment_plan','API\PlanController@apply_comment_plan');
    Route::get('/get_edit_plan/{id}','API\PlanController@get_edit_plan');
    Route::get('/apply_plan/{id}','API\PlanController@apply_plan');
    Route::get('/set_status_plan/{id}/{status_id}','API\PlanController@set_status_plan');

    Route::post('/delete_user/{id}', 'API\UserController@deattach_user');
    Route::post('/edit_user', 'API\UserController@edit_user');
    Route::post('/save_accounts_notify', 'API\UserController@save_accounts_notify');
    Route::post('/change_password', 'API\UserController@change_password');

    Route::post('/edit_account', 'API\AccountController@edit_account');

    Route::get('/account_plan/{id}', 'API\PlanController@get_plans');
    Route::get('/one_plan_page/{id}', 'API\PlanController@one_plan_page');
    Route::post('/account_plan_filter', 'API\PlanController@account_plan_filter');
    Route::get('/account_socnets/{id}', 'API\PlanController@get_socnets');
    Route::get('/page_socnets/{id}', 'API\PlanController@page_socnets');
    Route::get('/account_tags/{id}', 'API\PlanController@get_tags');
    Route::post('/get_tags', 'API\TagController@get_tags');
    Route::post('/upload_images','API\PlanController@upload_images');
    Route::post('/upload_videos','API\PlanController@upload_videos');
    Route::post('/add_youtube','API\PlanController@add_youtube');
    Route::post('/upload_avatar','API\UserController@upload_avatar');

    Route::get('/upload_images/delete/{id}','API\PlanController@upload_images_delete');
    Route::get('/upload_videos/delete/{id}','API\PlanController@upload_videos_delete');
    Route::get('/plan_videos/delete/{id}','API\PlanController@plan_videos_delete');
    Route::get('/plan_images/delete/{id}','API\PlanController@plan_images_delete');
    Route::get('/temp_youtube/delete/{id}','API\PlanController@temp_youtube_delete');
    Route::get('/plan_youtube/delete/{id}','API\PlanController@plan_youtube_delete');

});
Route::post('/password_reset', 'API\UserController@password_reset_post');
Route::post('/send_email','API\UserController@send_email');
Route::post('register', 'RegisterController@register');
Route::post('login', 'LoginController@login');
Route::post('logout', 'LoginController@logout');
