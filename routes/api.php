<?php

Route::get('', 'ApiController@index');

Route::group(['prefix' => 'v1'], function () {
    Route::get('', 'ApiController@index');

    Route::group(['prefix' => 'articles'], function () {
        Route::get('{id?}', 'Api\ArticleController@articles');
    });

    Route::group(['prefix' => 'notifications', 'middleware' => 'jwt'], function () {
        Route::get('{id?}', 'Api\NotificationController@notifications');
        Route::delete('/{id}', 'Api\NotificationController@deleteNotification');
    });

    Route::group(['prefix' => 'teams', 'middleware' => 'jwt'], function () {
        Route::get('{id?}', 'Api\TeamController@teams');
        Route::post('', 'Api\TeamController@createTeam');
        Route::put('/{id}', 'Api\TeamController@updateTeam');
        Route::post('/invite', 'Api\TeamController@invite');
        Route::post('/leave/{id}', 'Api\TeamController@leave');
        Route::post('/{token}', 'Api\TeamController@respondInvite');
        Route::delete('/{id}', 'Api\TeamController@deleteTeam');
    });

    Route::group(['prefix' => 'posts'], function () {
        Route::get('{id?}', 'Api\PostController@Posts');
        Route::group(['middleware' => 'jwt'], function () {
            Route::post('', 'Api\PostController@createPost');
            Route::delete('/{id}', 'Api\PostController@deletePost');
            Route::put('/{id}', 'Api\PostController@updatePost');
            Route::post('star/{id}', 'Api\PostController@Star');
        });
    });

    Route::group(['prefix' => 'comments', 'middleware' => 'jwt'], function () {
        Route::post('/', 'Api\CommentController@createComment');
        Route::put('/{id}', 'Api\CommentController@updateComment');
        Route::delete('/{id}', 'Api\CommentController@deleteComment');
        Route::post('rate/{id}', 'Api\CommentController@Rate');
    });

    Route::group(['prefix' => 'categories'], function () {
        Route::get('/{id?}', 'Api\CategoryController@Categories');
        Route::group(['middleware' => 'jwt'], function () {
            Route::post('/', 'Api\CategoryController@createCategory');
            Route::put('/{id}', 'Api\CategoryController@updateCategory');
            Route::delete('/{id}', 'Api\CategoryController@deleteCategory');
        });
    });

    Route::group(['prefix' => 'tags'], function () {
        Route::get('/{id?}', 'Api\TagController@Tags');
        Route::group(['middleware' => 'jwt'], function () {
            Route::post('/', 'Api\TagController@createTag');
            Route::put('/{id}', 'Api\TagController@updateTag');
            Route::delete('/{id}', 'Api\TagController@deleteTag');
        });
    });

    Route::group(['prefix' => 'topics'], function () {
        Route::get('/{id?}', 'Api\TopicController@topics');
        Route::group(['middleware' => 'jwt'], function () {
            Route::post('/', 'Api\TopicController@createTopic');
            Route::put('/{id}', 'Api\TopicController@updateTopic');
            Route::delete('/{id}', 'Api\TopicController@deleteTopic');
        });
    });

    Route::group(['prefix' => 'replies', 'middleware' => 'jwt'], function () {
        Route::post('/', 'Api\ReplyController@createReply');
        Route::put('/{id}', 'Api\ReplyController@updateReply');
        Route::delete('/{id}', 'Api\ReplyController@deleteReply');
    });

    Route::group(['prefix' => 'users'], function () {
        Route::get('/{id?}', 'Api\UserController@Users');
        Route::group(['middleware' => 'jwt'], function () {
            Route::put('/{id}', 'Api\UserController@updateUser');
        });
    });

    Route::group(['prefix' => 'forums'], function () {
        Route::get('/{id?}', 'Api\ForumController@forums');
        Route::group(['middleware' => 'jwt'], function () {
            Route::delete('/{id}', 'Api\ForumController@deleteForum');
        });
    });

    Route::group(['prefix' => 'auth'], function () {
        Route::get('/', 'Api\AuthController@getJwt');
        Route::post('/', 'Api\AuthController@Auth');
        Route::post('/register', 'Api\UserController@createUser');
        Route::post('/forgot', 'Api\AuthController@forgotPassword');
    });
});

