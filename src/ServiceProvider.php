<?php

declare(strict_types=1);

namespace Cndrsdrmn\LaravelPasswords;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

final class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->extend('auth.password', function ($provider, Application $app): BrokerManager {
            return new BrokerManager($app);
        });
    }
}
