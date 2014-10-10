<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/


Route::get('/', [
    'as' => 'home',
    'uses' => 'PagesController@home'
]);

Route::get('register', [
    'as' => 'register_path',
    'uses' => 'RegistrationController@show'
]);

Route::post('register', [
    'as' => 'register_path',
    'uses' => 'RegistrationController@register'
]);

/**
 * Sessions
 */
Route::get('login',[
    'as' => 'login_path',
    'uses' => 'SessionsController@show'
]);

Route::post('login',[
    'as' => 'login_path',
    'uses' => 'SessionsController@login'
]);

Route::get('logout', [
    'as' => 'logout_path',
    'uses' => 'SessionsController@logout'
]);

/**
 * Statuses
 */
Route::get('statuses', [
    'as' => 'statuses_path',
    'uses' => 'StatusesController@show'
]);

Route::post('statuses', [
    'as' => 'statuses_path',
    'uses' => 'StatusesController@publish'
]);

/**
 * Users
 */
Route::get('users', [
    'as' => 'users_path',
    'uses' =>'UsersController@users'
]);

Route::get('@{username}', [
    'as' => 'profile_path',
    'uses' => 'UsersController@profile'
]);

/**
 * Follows
 */


Route::delete('follows/{id}', [
    'as' => 'follow_path',
    'uses' => 'FollowsController@unfollow'
]);

Route::post('follows', [
    'as' => 'follows_path',
    'uses' => 'FollowsController@follow'
]);

/**
 * Password reset
 */
Route::controller('password', 'RemindersController');

/**
 * Inbox
 */
Route::get('inbox/new', [
    'as' => 'new_message_path',
    'uses' => 'InboxController@index'
]);

Route::get('inbox', [
    'as' => 'inbox_path',
    'uses' => 'InboxController@show'
]);

Route::post('inbox', [
    'as' => 'inbox_path',
    'uses' => 'InboxController@send'
]);

Route::delete('inbox', [
    'as' => 'inbox_path',
    'uses' => 'InboxController@delete'
]);

//listen to mysql event
//Event::listen('illuminate.query', function($sql)
//{
//    var_dump($sql);
//});

/**
 * Other routes
 */
Route::get('/{something}', function()
{
    return Redirect::home();
});
