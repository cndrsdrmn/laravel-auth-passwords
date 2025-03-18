<?php

declare(strict_types=1);

namespace Cndrsdrmn\LaravelPasswords;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Override;

final class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Register services.
     */
    #[Override]
    public function register(): void
    {
        $this->app->extend('auth.password', fn ($provider, Application $app): BrokerManager => new BrokerManager($app));
    }
}
