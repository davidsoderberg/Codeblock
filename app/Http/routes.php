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
Route::group(['prefix' => 'embed'], function () {
    Route::get('/{id}', 'PostController@embed');
    Route::get('/{slug}', 'PostController@embed');
});
Route::get('license', 'MenuController@license');
Route::group(['prefix' => 'contact'], function () {
    Route::get('/', 'MenuController@contact');
    Route::post('/send', 'MenuController@sendContact');
});
Route::get('oauth/{social}', 'UserController@oauth');
Route::post('search', 'PostController@search');
Route::get('command/{command}/{password}/{param?}', 'MenuController@command')->where([
    'command' => '[a-zA-Z]+',
    'password' => '[a-zA-Z0-9]+',
    'param' => '[a-zA-Z_]+',
]);

Route::group(['prefix' => 'posts'], function () {
    Route::get('/list', 'PostController@listPosts');
    Route::get('/{id}', 'PostController@show');
    Route::get('/{id}/{comment}', 'PostController@show');
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/', 'PostController@index');
        Route::get('/create', 'PostController@create');
        Route::get('/edit/{id}', 'PostController@edit');
        Route::get('/delete/{id}', 'PostController@delete');
        Route::get('/star/{id}', 'PostController@star');
        Route::post('/store/{id?}', 'PostController@createOrUpdate');
        Route::get('/fork/{id}', 'PostController@fork');
        Route::get('/forked/{id}', 'PostController@forked');
        Route::post('/gist', 'PostController@forkGist');
        Route::get('/history/undo/{id}', 'PostController@undo');
    });
    Route::get('/{slug}', 'PostController@show');
    Route::get('/{slug}/{comment?}', 'PostController@show');
});

Route::group(['prefix' => 'teams'], function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/delete/{id}', 'TeamController@delete');
        Route::post('/store/{id?}', 'TeamController@createOrUpdate');
        Route::get('/{id?}', 'TeamController@index');
    });
});

Route::group(['prefix' => 'messages'], function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/{id?}', 'MessageController@index');
        Route::post('/store/{id?}', 'MessageController@createOrUpdate');
    });
});

Route::group(['prefix' => 'team'], function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::post('/store/{id?}', 'TeamController@createOrUpdate');
        Route::post('/invite', 'TeamController@invite');
        Route::get('/leave/{id}', 'TeamController@leave');
        Route::get('/{id?}', 'TeamController@listTeams');
    });
    Route::get('/{token}', 'TeamController@respondInvite');
});

Route::group(['prefix' => 'notifications'], function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/', 'NotificationController@listNotification');
        Route::get('/delete/{id}', 'NotificationController@delete');
    });
});

Route::group(['prefix' => 'analytics'], function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/events', 'GapiController@events');
        Route::get('/visitedpages', 'GapiController@mostVisitedPages');
        Route::get('/visitorviews', 'GapiController@visitorsAndPageViews');
    });
});

Route::group(['prefix' => 'comments'], function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/', 'CommentController@index');
        Route::get('/list', 'CommentController@listComments');
        Route::post('/{id?}', 'CommentController@createOrUpdate');
        Route::get('/edit/{id}', 'CommentController@edit');
        Route::get('/delete/{id}', 'CommentController@delete');
    });
});

Route::group(['prefix' => 'rate'], function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/minus/{id}', 'RateController@minus');
        Route::get('/plus/{id}', 'RateController@plus');
    });
});

Route::group(['prefix' => 'forum'], function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/', 'ForumController@listForums');
        Route::get('/{id}', 'ForumController@show');
        //Route::get('forums/{id}', 'ForumController@forumsRedirect');
        Route::get('/topic/{id}/{reply?}', 'TopicController@show');
    });
});

Route::group(['prefix' => 'topics'], function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::post('/store/{id?}', 'TopicController@createOrUpdate');
        Route::get('/delete/{id}', 'TopicController@delete');
    });
});

Route::group(['prefix' => 'reply'], function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::post('/store/{id?}', 'ReplyController@createOrUpdate');
        Route::get('/delete/{id}', 'ReplyController@delete');
    });
});

Route::group(['prefix' => 'categories'], function () {
    Route::get('list/{id}/{sort?}', 'PostController@category');
    Route::get('list/{name}/{sort?}', 'PostController@category');
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/delete/{id}', 'CategoryController@delete');
        Route::post('/store/{id?}', 'CategoryController@createOrUpdate');
        Route::get('/{id?}', 'CategoryController@index');
    });
});

Route::group(['prefix' => 'blog'], function () {
    Route::get('/{id?}', 'ArticleController@index');
    Route::get('/{slug?}', 'ArticleController@index');
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/delete/{id}', 'ArticleController@delete');
        Route::post('/store/', 'ArticleController@create');
        Route::post('/store/{id}', 'ArticleController@update');
    });
});

Route::group(['prefix' => 'forums'], function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/delete/{id}', 'ForumController@delete');
        Route::post('/store/{id?}', 'ForumController@createOrUpdate');
        Route::get('/{id?}', 'ForumController@index');
    });
});

Route::group(['prefix' => 'tags'], function () {
    Route::get('list/{id}/{sort?}', 'PostController@tag');
    Route::get('list/{name}/{sort?}', 'PostController@tag');
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/delete/{id}', 'TagController@delete');
        Route::post('/store/{id?}', 'TagController@createOrUpdate');
        Route::get('/{id?}', 'TagController@index');
    });
});

Route::group(['prefix' => 'user'], function () {
    Route::post('only', 'UserController@setOnly');
    Route::post('/store/{id?}', 'UserController@store');
    Route::get('/list/{id?}/{sort?}', 'UserController@listUserBlock');
    Route::get('/list/{username?}/{sort?}', 'UserController@listUserBlock');
    Route::get('/list/{sort?}', 'UserController@listUserBlock');
    Route::get('/{id?}', 'UserController@show');
    Route::get('/{username?}', 'UserController@show');
    Route::group(['middleware' => 'auth'], function () {
        Route::post('/delete/{id}', 'UserController@delete');
        Route::get('/edit/{id}', 'UserController@edit');
        Route::post('/update/{id}', 'UserController@update');
    });
});

Route::group(['prefix' => 'permissions'], function () {
    Route::group(['middleware' => 'auth'], function () {
        /*
            Route::post('/store/{id?}', 'PermissionController@createOrUpdate');
            Route::get('/delete/{id}', 'PermissionController@delete');
            Route::get('/{id?}', 'PermissionController@index');
        */
        Route::get('/', 'RoleController@editRolePermission');
        Route::post('/update', 'RoleController@updateRolePermission');
    });
});

Route::group(['prefix' => 'roles'], function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::post('/default', 'RoleController@setDefault');
        Route::post('/store/{id?}', 'RoleController@store');
        Route::get('/delete/{id}', 'RoleController@delete');
        Route::get('/{id?}', 'RoleController@index');
    });
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/log', 'MenuController@log');
    Route::get('starred', 'UserController@listStarred');
    Route::get('users', 'UserController@index');
    Route::get('backup', 'UserController@backup');
    Route::get('logout', 'UserController@logout');
});

Route::group(['middleware' => 'guest'], function () {
    Route::get('login', 'UserController@login');
    Route::post('session/create', 'UserController@Usersession');
    Route::post('forgotpassword', 'UserController@forgotPassword');
    Route::get('activate/{id}/{token}', 'UserController@activate');
});

Route::group(['prefix' => 'api', 'middleware' => 'api'], function () {
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
            Route::post('', 'Api\TeamController@createOrUpdateTeam');
            Route::put('/{id}', 'Api\TeamController@createOrUpdateTeam');
            Route::post('/invite', 'Api\TeamController@invite');
            Route::post('/leave/{id}', 'Api\TeamController@leave');
            Route::post('/{token}', 'Api\TeamController@respondInvite');
            Route::delete('/{id}', 'Api\TeamController@deleteTeam');
        });

        Route::group(['prefix' => 'posts'], function () {
            Route::get('{id?}', 'Api\PostController@Posts');
            Route::group(['middleware' => 'jwt'], function () {
                Route::post('', 'Api\PostController@createOrUpdatePost');
                Route::delete('/{id}', 'Api\PostController@deletePost');
                Route::put('/{id}', 'Api\PostController@createOrUpdatePost');
                Route::post('star/{id}', 'Api\PostController@Star');
            });
        });

        Route::group(['prefix' => 'comments', 'middleware' => 'jwt'], function () {
            Route::post('/', 'Api\CommentController@createOrUpdateComment');
            Route::put('/{id}', 'Api\CommentController@createOrUpdateComment');
            Route::delete('/{id}', 'Api\CommentController@deleteComment');
            Route::post('rate/{id}', 'Api\CommentController@Rate');
        });

        Route::group(['prefix' => 'categories'], function () {
            Route::get('/{id?}', 'Api\CategoryController@Categories');
            Route::group(['middleware' => 'jwt'], function () {
                Route::post('/', 'Api\CategoryController@createOrUpdateCategory');
                Route::put('/{id}', 'Api\CategoryController@createOrUpdateCategory');
                Route::delete('/{id}', 'Api\CategoryController@deleteCategory');
            });
        });

        Route::group(['prefix' => 'tags'], function () {
            Route::get('/{id?}', 'Api\TagController@Tags');
            Route::group(['middleware' => 'jwt'], function () {
                Route::post('/', 'Api\TagController@createOrUpdateTag');
                Route::put('/{id}', 'Api\TagController@createOrUpdateTag');
                Route::delete('/{id}', 'Api\TagController@deleteTag');
            });
        });

        Route::group(['prefix' => 'topics'], function () {
            Route::get('/{id?}', 'Api\TopicController@topics');
            Route::group(['middleware' => 'jwt'], function () {
                Route::post('/', 'Api\TopicController@createOrUpdateTopic');
                Route::put('/{id}', 'Api\TopicController@createOrUpdateTopic');
                Route::delete('/{id}', 'Api\TopicController@deleteTopic');
            });
        });

        Route::group(['prefix' => 'replies', 'middleware' => 'jwt'], function () {
            Route::post('/', 'Api\ReplyController@createOrUpdateReply');
            Route::put('/{id}', 'Api\ReplyController@createOrUpdateReply');
            Route::delete('/{id}', 'Api\ReplyController@deleteReply');
        });

        Route::group(['prefix' => 'users'], function () {
            Route::get('/{id?}', 'Api\UserController@Users');
            Route::group(['middleware' => 'jwt'], function () {
                Route::put('/{id}', 'Api\UserController@createOrUpdateUser');
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
            Route::post('/register', 'Api\UserController@createOrUpdateUser');
            Route::post('/forgot', 'Api\AuthController@forgotPassword');
        });
    });
});
