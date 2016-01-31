<?php

namespace Minhbang\User;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class UserServiceProvider
 *
 * @package Minhbang\User
 */
class ServiceProvider extends BaseServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @param \Illuminate\Routing\Router $router
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'user');
        $this->loadViewsFrom(__DIR__ . '/../views', 'user');
        $this->publishes(
            [
                __DIR__ . '/../views'           => base_path('resources/views/vendor/user'),
                __DIR__ . '/../lang'            => base_path('resources/lang/vendor/user'),
                __DIR__ . '/../config/user.php' => config_path('user.php'),
            ]
        );
        $this->publishes(
            [
                __DIR__ . '/../database/migrations/2014_10_12_000000_create_users_table.php'           =>
                    database_path('migrations/' . '2014_10_12_000000_create_users_table.php'),
                __DIR__ . '/../database/migrations/2014_10_12_100000_create_password_resets_table.php' =>
                    database_path('migrations/' . '2014_10_12_100000_create_password_resets_table.php'),
            ],
            'db'
        );

        if (config('user.add_route') && !$this->app->routesAreCached()) {
            require __DIR__ . '/routes.php';
        }
        // pattern filters
        $router->pattern('user', '[0-9]+');
        // model bindings
        $router->model('user', 'Minhbang\User\User');

        // Validator rule kiểm tra password hiện tại
        $this->app['validator']->extend(
            'password_check',
            function ($attribute, $value, $parameters) {
                //TODO: thay bằng user() helper
                $user = $this->app['db']->table('users')->where('id', user('id'))->first();

                return $user && $this->app['hash']->check($value, $user->password);
            }
        );
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/user.php', 'user');
    }
}