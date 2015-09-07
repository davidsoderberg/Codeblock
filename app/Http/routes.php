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
Route::pattern('sort', 'category|stars|date|comments|name');

Route::get('/', 'MenuController@index');
Route::get('browse', 'MenuController@browse');
Route::get('markdown', 'MenuController@markdown');
Route::get('blog/{id?}', 'ArticleController@index');
Route::get('blog/{slug?}', 'ArticleController@index');
Route::get('embed/{id}', 'PostController@embed');
Route::get('embed/{slug}', 'PostController@embed');
Route::get('license', 'MenuController@license');
Route::get('contact', 'MenuController@contact');
Route::post('contact/send', 'MenuController@sendContact');
Route::post('user/store/{id?}', 'UserController@store');
Route::get('oauth/{social}', 'UserController@oauth');
Route::post('search', 'PostController@search');
Route::get('command/{command}/{password}/{param?}', 'MenuController@command')
	->where(['command' => '[a-zA-Z]+', 'password' => '[a-zA-Z0-9]+', 'param' => '[a-zA-Z_]+']);

Route::get('posts/list', 'PostController@listPosts');
Route::get('tag/{id}/{sort?}', 'PostController@tag');
Route::get('tag/{name}/{sort?}', 'PostController@tag');
Route::get('category/{id}/{sort?}', 'PostController@category');
Route::get('category/{name}/{sort?}', 'PostController@category');
Route::get('user/list/{id?}/{sort?}', 'UserController@listUserBlock');
Route::get('user/list/{username?}/{sort?}', 'UserController@listUserBlock');
Route::get('user/list/{sort?}', 'UserController@listUserBlock');
Route::get('posts/{id}', 'PostController@show');
Route::get('posts/{id}/{comment}', 'PostController@show');
Route::get('user/{id?}', 'UserController@show');
Route::get('user/{username?}', 'UserController@show');
Route::group(['middleware' => 'auth'], function() {

	Route::get('starred', 'UserController@listStarred');
	Route::get('posts/create', 'PostController@create');
	Route::get('posts/edit/{id}', 'PostController@edit');
	Route::get('posts/delete/{id}', 'PostController@delete');
	Route::get('posts/star/{id}', 'PostController@star');
	Route::post('posts/store/{id?}', 'PostController@createOrUpdate');
	Route::get('posts/fork/{id}', 'PostController@fork');
	Route::get('posts/forked/{id}', 'PostController@forked');
	Route::post('posts/gist', 'PostController@forkGist');
	Route::get('posts/history/undo/{id}',  'PostController@undo');

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
	//Route::get('forums/{id}', 'ForumController@forumsRedirect');
	Route::get('forum/topic/{id}/{reply?}', 'TopicController@show');

	Route::post('topics/store/{id?}', 'TopicController@createOrUpdate');
	Route::get('topics/delete/{id}', 'TopicController@delete');
	Route::post('reply/store/{id?}', 'ReplyController@createOrUpdate');
	Route::get('reply/delete/{id}', 'ReplyController@delete');

	Route::get('categories/delete/{id}', 'CategoryController@delete');
	Route::post('categories/store/{id?}', 'CategoryController@createOrUpdate');
	Route::get('categories/{id?}', 'CategoryController@index');


	Route::get('blog/delete/{id}', 'ArticleController@delete');
	Route::post('blog/store/', 'ArticleController@create');
	Route::post('blog/store/{id}', 'ArticleController@update');

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
	Route::get('backup', 'UserController@backup');

	/*
	Route::post('permissions/store/{id?}', 'PermissionController@createOrUpdate');
	Route::get('permissions/delete/{id}', 'PermissionController@delete');
	Route::get('permissions/{id?}', 'PermissionController@index');
	*/
	Route::get('permissions', 'RoleController@editRolePermission');
	Route::post('permissions/update', 'RoleController@updateRolePermission');

	Route::post('role/default', 'RoleController@setDefault');
	Route::post('roles/store/{id?}', 'RoleController@store');
	Route::get('roles/delete/{id}', 'RoleController@delete');
	Route::get('roles/{id?}', 'RoleController@index');

	Route::get('logout', 'UserController@logout');
});
Route::get('posts/{slug}', 'PostController@show');
Route::get('posts/{slug}/{comment}', 'PostController@show');

Route::group(['middleware' => 'guest'], function(){
	Route::get('login', 'UserController@login');
	Route::post('session/create', 'UserController@Usersession');
	Route::post('forgotpassword', 'UserController@forgotPassword');
	Route::get('activate/{id}/{token}', 'UserController@activate');
});


Route::group(['prefix' => 'api', 'middleware' => 'api'],function(){

	Route::get('', 'ApiController@index');

	Route::group(['prefix' => 'posts'], function(){
		Route::get('{id?}', 'ApiController@Posts');
		Route::group(['middleware' =>  'jwt'], function() {
			Route::post('', 'ApiController@createOrUpdatePost');
			Route::delete('/{id}', 'ApiController@deletePost');
			Route::put('/{id}', 'ApiController@createOrUpdatePost');
			Route::post('star/{id}', 'ApiController@Star');
		});
	});

	Route::group(['prefix' => 'comments', 'middleware' =>  'jwt'], function(){
		Route::post('/', 'ApiController@createOrUpdateComment');
		Route::put('/{id}', 'ApiController@createOrUpdateComment');
		Route::delete('/{id}', 'ApiController@deleteComment');
		Route::post('rate/{id}', 'ApiController@Rate');
	});

	Route::group(['prefix' => 'categories'], function(){
		Route::get('/{id?}', 'ApiController@Categories');
		Route::group(['middleware' =>  'jwt'], function() {
			Route::post('/', 'ApiController@createOrUpdateCategory');
			Route::put('/{id}', 'ApiController@createOrUpdateCategory');
			Route::delete('/{id}', 'ApiController@deleteCategory');
		});
	});

	Route::group(['prefix' => 'tags'], function(){
		Route::get('/{id?}', 'ApiController@Categories');
		Route::group(['middleware' =>  'jwt'], function() {
			Route::post('/', 'ApiController@createOrUpdateCategory');
			Route::put('/{id}', 'ApiController@createOrUpdateCategory');
			Route::delete('/{id}', 'ApiController@deleteCategory');
		});
	});

	Route::group(['prefix' => 'topics'], function(){
		Route::get('/{id?}', 'ApiController@topics');
		Route::group(['middleware' =>  'jwt'], function() {
			Route::post('/', 'ApiController@createOrUpdateTopic');
			Route::put('/{id}', 'ApiController@createOrUpdateTopic');
			Route::delete('/{id}', 'ApiController@deleteTopic');
		});
	});

	Route::group(['prefix' => 'replies', 'middleware' =>  'jwt'], function(){
		Route::post('/', 'ApiController@createOrUpdateReply');
		Route::put('/{id}', 'ApiController@createOrUpdateReply');
		Route::delete('/{id}', 'ApiController@deleteReply');
	});

	Route::group(['prefix' => 'users'], function(){
		Route::get('/{id?}', 'ApiController@Users');
		Route::group(['middleware' =>  'jwt'], function() {
			Route::put('/{id}', 'ApiController@createOrUpdateUser');
		});
	});

	Route::group(['prefix' => 'forums'], function(){
		Route::get('/{id?}', 'ApiController@forums');
		Route::group(['middleware' =>  'jwt'], function() {
			Route::delete('/{id}', 'ApiController@deleteForum');
		});
	});

	Route::group(['prefix' => 'auth'], function(){
		Route::get('/', 'ApiController@getJwt');
		Route::post('/', 'ApiController@Auth');
		Route::post('/register', 'ApiController@createOrUpdateUser');
		Route::post('/forgot', 'ApiController@forgotPassword');
	});
});
