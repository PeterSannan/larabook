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

Route::post('login', 'AuthController@login');
Route::post('register', 'AuthController@register');

Route::group(['middleware' => 'auth:api'], function(){
    Route::post('posts', 'PostsController@store');
    Route::get('posts', 'PostsController@index');
    
    Route::get('users/auth-user', 'UsersController@getAuthUser');
    Route::get('users/{user}', 'UsersController@show');
    Route::get('users/{user}/posts', 'UserPostsController@index');

    Route::post('/friend-request', 'FriendRequestsController@store');
    Route::put('/friend-request/{friend_request}', 'FriendRequestsController@update');
    Route::delete('/friend-request/{friend_request}', 'FriendRequestsController@destroy');
   
    Route::post('posts/{post}/likes', 'LikesController@store');
});
