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
Route::pattern('slug', '[-a-zA-Z0-9-]+');

Route::get('/', 'MenuController@index');
Route::get('browse', 'MenuController@browse');
Route::get('news/{id?}', 'ArticleController@index');
Route::get('license', 'MenuController@license');
Route::get('contact', 'MenuController@contact');
Route::post('contact/send', 'MenuController@sendContact');
Route::post('user/store/{id?}', 'UserController@store');
Route::get('oauth/{social}', 'UserController@oauth');
Route::post('search', 'PostController@search');
Route::get('command/{command}/{password}/{param?}', 'MenuController@command')
	->where(['command' => '[a-zA-Z]+', 'password' => '[a-zA-Z0-9]+', 'param' => '[a-zA-Z]+']);

Route::get('posts/list', 'PostController@listPosts');
Route::get('posts/tag/{id}', 'PostController@tag');
Route::get('posts/category/{id}', 'PostController@category');
Route::get('user/list/{id?}', 'UserController@listUserBlock');
Route::get('posts/{id}', 'PostController@show');
Route::get('user/{id?}', 'UserController@show');
Route::get('user/{username?}', 'UserController@showByUsername');
Route::group(['middleware' => 'auth'], function() {

	Route::get('posts/create', 'PostController@create');
	Route::get('posts/edit/{id}', 'PostController@edit');
	Route::get('posts/delete/{id}', 'PostController@delete');
	Route::get('posts/star/{id}', 'PostController@star');
	Route::post('posts/store/{id?}', 'PostController@createOrUpdate');
	Route::get('posts/fork/{id}', 'PostController@fork');
	Route::get('posts/forked/{id}', 'PostController@forked');
	Route::post('posts/gist', 'PostController@forkGist');

	Route::get('notifications/', 'NotificationController@listNotification');
	Route::get('notifications/delete/{id}', 'NotificationController@delete');

	Route::get('comments/list', 'CommentController@listComments');
	Route::post('comments/{id?}', 'CommentController@createOrUpdate');
	Route::get('comments/edit/{id}', 'CommentController@edit');
	Route::get('comments/delete/{id}', 'CommentController@delete');

	Route::get('rate/minus/{id}', 'RateController@minus');
	Route::get('rate/plus/{id}', 'RateController@plus');

	Route::get('forum', 'ForumController@listForums');
	Route::get('forum/{id}', 'ForumController@show');
	Route::get('forums/{id}', 'ForumController@forumsRedirect');
	Route::get('forum/topic/{id}/{reply?}', 'TopicController@show');

	Route::post('topics/store/{id?}', 'TopicController@createOrUpdate');
	Route::get('topics/delete/{id}', 'TopicController@delete');
	Route::post('reply/store/{id?}', 'ReplyController@createOrUpdate');
	Route::get('reply/delete/{id}', 'ReplyController@delete');

	Route::get('categories/delete/{id}', 'CategoryController@delete');
	Route::post('categories/store/{id?}', 'CategoryController@createOrUpdate');
	Route::get('categories/{id?}', 'CategoryController@index');


	Route::get('news/delete/{id}', 'ArticleController@delete');
	Route::post('news/store/', 'ArticleController@create');
	Route::post('news/store/{id}', 'ArticleController@update');

	Route::get('posts', 'PostController@index');
	Route::get('comments/', 'CommentController@index');

	Route::get('forums/delete/{id}', 'ForumController@delete');
	Route::post('forums/store/{id?}', 'ForumController@createOrUpdate');
	Route::get('forums/{id?}', 'ForumController@index');

	Route::get('tags/delete/{id}', 'TagController@delete');
	Route::post('tags/store/{id?}', 'TagController@createOrUpdate');
	Route::get('tags/{id?}', 'TagController@index');

	Route::post('user/delete/{id}', 'UserController@delete');
	Route::get('user/edit/{id}', 'UserController@edit');
	Route::post('user/update/{id}', 'UserController@update');
	Route::get('users', 'UserController@index');
	/*
	Route::post('permissions/store/{id?}', 'PermissionController@createOrUpdate');
	Route::get('permissions/delete/{id}', 'PermissionController@delete');
	Route::get('permissions/{id?}', 'PermissionController@index');
	*/
	Route::get('permissions', 'RoleController@editRolePermission');
	Route::post('permissions/update', 'RoleController@updateRolePermission');

	Route::post('role/default', 'RoleController@setDefault');
	Route::post('roles/store', 'RoleController@store');
	Route::get('roles/edit/{id}', 'RoleController@edit');
	Route::post('roles/update', 'RoleController@update');
	Route::get('roles/delete/{id}', 'RoleController@delete');
	Route::get('roles', 'RoleController@index');

	Route::get('logout', 'UserController@logout');
});
Route::get('posts/{slug}', 'PostController@show');

Route::group(['middleware' => 'guest'], function(){
	Route::get('login', 'UserController@login');
	Route::post('session/create', 'UserController@Usersession');
	Route::post('forgotpassword', 'UserController@forgotPassword');
	Route::get('activate/{id}/{token}', 'UserController@activate');
});


Route::group(['prefix' => 'api', 'middleware' => 'api'],function(){

	Route::get('categories/{id?}', 'ApiController@Categories');
	Route::get('tags/{id?}', 'ApiController@Tags');
	Route::get('posts/{id?}', 'ApiController@Posts');
	Route::get('users/{id?}', 'ApiController@Users');
	Route::get('forums/{id?}', 'ApiController@forums');
	Route::get('topics/{id?}', 'ApiController@topics');
	Route::post('auth', 'ApiController@Auth');
	Route::get('jwt', 'ApiController@getJwt');
	Route::post('auth/register', 'ApiController@createOrUpdateUser');
	Route::post('auth/forgot', 'ApiController@forgotPassword');

	Route::group(['middleware' =>  'jwt'], function(){
		Route::post('comment', 'ApiController@createOrUpdateComment');
		Route::post('topics', 'ApiController@createOrUpdateTopic');
		Route::post('replies', 'ApiController@createOrUpdateReply');
		Route::post('post', 'ApiController@createOrUpdatePost');
		Route::post('star/{id}', 'ApiController@Star');
		Route::post('rate/{id}', 'ApiController@Rate');

		Route::put('post/{id}', 'ApiController@createOrUpdatePost');
		Route::put('comment/{id}', 'ApiController@createOrUpdateComment');
		Route::put('user/{id}', 'ApiController@createOrUpdateUser');
		Route::put('topics/{id}', 'ApiController@createOrUpdateTopic');
		Route::put('replies/{id}', 'ApiController@createOrUpdateReply');

		Route::post('category', 'ApiController@createOrUpdateCategory');
		Route::post('tag', 'ApiController@createOrUpdateTag');

		Route::put('category/{id}', 'ApiController@createOrUpdateCategory');
		Route::put('tag/{id}', 'ApiController@createOrUpdateTag');
	});
});
