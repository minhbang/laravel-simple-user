<?php
Route::group(
    ['namespace' => 'Minhbang\User\Controllers'],
    function () {
        Route::get('auth/logout', ['as' => 'auth.logout', 'uses' => 'AuthController@logout']);
        Route::group(['prefix' => 'auth', 'middleware' => 'guest'], function () {
            Route::get('login', ['as' => 'auth.login', 'uses' => 'AuthController@showLogin']);
            Route::post('login', ['uses' => 'AuthController@login']);
        });
        Route::group(['prefix' => 'password', 'middleware' => 'guest'], function () {
            Route::get('email', ['as' => 'password.email', 'uses' => 'PasswordController@showEmail']);
            Route::post('email', ['uses' => 'PasswordController@email']);
            Route::get('reset', ['as' => 'password.reset', 'uses' => 'PasswordController@showReset']);
            Route::post('reset', ['uses' => 'PasswordController@reset']);
        });
        Route::group(['prefix' => 'account', 'middleware' => 'auth'], function () {
            Route::get('password', ['as' => 'account.password', 'uses' => 'AccountController@showPassword']);
            Route::post('password', ['uses' => 'AccountController@password']);
            Route::get('profile', ['as' => 'account.profile', 'uses' => 'AccountController@showProfile']);
            Route::post('profile', ['uses' => 'AccountController@profile']);
        });
    }
);
// Backend ===================================================================================
Route::group(
    ['prefix' => 'backend', 'namespace' => 'Minhbang\User\Controllers\Backend'],
    function () {
        // User Manage
        Route::group(['middleware' => config('user.middlewares.user')], function () {
            Route::get('user/data', ['as' => 'backend.user.data', 'uses' => 'UserController@data']);
            Route::post('user/{user}/quick_update', ['as' => 'backend.user.quick_update', 'uses' => 'UserController@quickUpdate']);
            Route::resource('user', 'UserController');
        });
    }
);
