<?php

declare(strict_types=1);

namespace Tests;

use Cndrsdrmn\LaravelPasswords\BrokerInterface;
use Cndrsdrmn\LaravelPasswords\BrokerManager;
use Cndrsdrmn\LaravelPasswords\ServiceProvider;
use Cndrsdrmn\LaravelPasswords\TokenRepositoryInterface;

it('package should be loaded properly', function (): void {
    $service = new ServiceProvider($this->app);
    $service->register();

    $manage = app('auth.password');

    expect($manage)->toBeInstanceOf(BrokerManager::class)
        ->broker()->toBeInstanceOf(BrokerInterface::class)
        ->getRepository()->toBeInstanceOf(TokenRepositoryInterface::class);
})->only();
