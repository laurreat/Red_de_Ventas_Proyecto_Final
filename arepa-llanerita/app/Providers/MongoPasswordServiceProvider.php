<?php

namespace App\Providers;

use App\Auth\MongoPasswordBroker;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Support\ServiceProvider;

class MongoPasswordServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('auth.password', function ($app) {
            return new PasswordBrokerManager($app);
        });

        $this->app->bind('auth.password.broker', function ($app) {
            return new MongoPasswordBroker(
                $app['auth']->createUserProvider(config('auth.passwords.users.provider')),
                config('auth.passwords.users.expire'),
                config('auth.passwords.users.throttle')
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}