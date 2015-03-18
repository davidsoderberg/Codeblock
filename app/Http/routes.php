<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::pattern('id', '[0-9]+');
Route::pattern('username', '[-_0-9A-Za-z]+');

Route::get('/', 'MenuController@index');
Route::get('browse', 'MenuController@browse');
Route::get('license', 'MenuController@license');
Route::get('contact', 'MenuController@contact');
Route::post('contact/send', 'MenuController@sendContact');
Route::post('user/store/{id?}', 'UserController@store');
Route::get('oauth/{social}', 'UserController@oauth');
Route::post('search', 'PostController@search');
Route::get('command/{command}/{password}/{param?}', 'MenuController@command')
	->where('command', '[a-zA-Z]+')
	->where('password', '[a-zA-Z0-9]+')
	->where('param', '[a-zA-Z]+')
;

Route::get('posts/list', 'PostController@listPosts');
Route::get('posts/tag/{id}', 'PostController@tag');
Route::get('posts/category/{id}', 'PostController@category');
Route::get('user/list/{id?}', 'UserController@listUserBlock');
Route::get('posts/{id}', 'PostController@show');
Route::get('user/{id?}', 'UserController@show');
Route::get('user/{username?}', 'UserController@showByUsername');
Route::group(['middleware' => 'auth'], function() {

	Route::get('posts', 'PostController@index');
	Route::get('posts/create', 'PostController@create');
	Route::get('posts/edit/{id}', 'PostController@edit');
	Route::get('posts/delete/{id}', 'PostController@delete');
	Route::get('posts/star/{id}', 'PostController@star');
	Route::post('posts/store/{id?}', 'PostController@createOrUpdate');
	Route::get('posts/fork/{id}', 'PostController@fork');
	Route::get('posts/forked/{id}', 'PostController@forked');
	Route::get('posts/gist/{id}', 'PostController@forkGist');

	Route::get('notifications/', 'NotificationController@listNotification');

	Route::post('comments/{id?}', 'CommentController@createOrUpdate');
	Route::get('comments/', 'CommentController@index');
	Route::get('comments/edit/{id}', 'CommentController@edit');
	Route::get('comments/delete/{id}', 'CommentController@delete');

	Route::get('rate/minus/{id}', 'RateController@minus');
	Route::get('rate/plus/{id}', 'RateController@plus');

	Route::group(['middleware' => 'role', 'role' => '2'], function() {
		Route::get('categories', 'CategoryController@index');
		Route::get('categories/{id}', 'CategoryController@show');
		Route::get('categories/edit/{id}', 'CategoryController@index');
		Route::get('categories/delete/{id}', 'CategoryController@delete');
		Route::post('categories/store/{id?}', 'CategoryController@createOrUpdate');

		Route::get('tags', 'TagController@index');
		Route::get('tags/{id}', 'TagController@show');
		Route::get('tags/edit/{id}', 'TagController@index');
		Route::get('tags/delete/{id}', 'TagController@delete');
		Route::post('tags/store/{id?}', 'TagController@createOrUpdate');

		Route::get('users', 'UserController@index');
		Route::get('user/delete/{id}', 'UserController@delete');
		Route::get('user/edit/{id}', 'UserController@edit');
		Route::post('user/update/{id}', 'UserController@update');

		Route::get('permissions/{id?}', 'PermissionController@index');
		Route::get('permissions/edit/{id}', 'PermissionController@index');
		Route::post('permissions/store/{id?}', 'PermissionController@createOrUpdate');
		Route::get('permissions/delete/{id}', 'PermissionController@delete');

		Route::get('rolepermission/edit', 'RoleController@editRolePermission');
		Route::post('rolepermission/update', 'RoleController@updateRolePermission');

		Route::get('roles', 'RoleController@index');
		Route::get('roles/create', 'RoleController@create');
		Route::post('roles/store', 'RoleController@store');
		Route::get('roles/edit/{id}', 'RoleController@edit');
		Route::post('roles/update', 'RoleController@update');
		Route::get('roles/delete/{id}', 'RoleController@delete');
	});

	Route::get('logout', 'UserController@logout');
});

Route::group(['middleware' => 'guest'], function(){
	Route::get('login', 'UserController@login');
	Route::post('session/create', 'UserController@Usersession');
	Route::post('forgotpassword', 'UserController@forgotPassword');
	Route::get('activate/{id}/{token}', 'UserController@activate');
});


Route::group(['prefix' => 'api', 'middleware' => 'api'],function(){

	Route::get('category', 'ApiController@Categories');
	Route::get('tag', 'ApiController@Tags');
	Route::get('post', 'ApiController@Posts');
	Route::get('user', 'ApiController@Users');
	Route::post('auth', 'ApiController@Auth');

	Route::group(['middleware' =>  'jwt'], function(){
		Route::post('category', 'ApiController@CreateCategory');
		Route::post('tag', 'ApiController@CreateTag');
		Route::post('post', 'ApiController@CreatePost');
		Route::post('comment', 'ApiController@CreateComment');
		Route::post('star/{id}', 'ApiController@Star');
		Route::post('rate/{id}', 'ApiController@Rate');
	});
});
