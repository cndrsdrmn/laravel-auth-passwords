<?php

declare(strict_types=1);

namespace Tests;

use Cndrsdrmn\LaravelPasswords\BrokerManager;
use Illuminate\Auth\Passwords\CacheTokenRepository;
use InvalidArgumentException;

beforeEach(function (): void {
    $this->manager = new BrokerManager($this->app);
});

test('create token repository with cache driver', function (): void {
    config(['auth.passwords.users.driver' => 'cache']);

    $config = config('auth.passwords.users');

    $repository = (fn () => $this->createTokenRepository($config));

    expect($repository->call($this->manager))->toBeInstanceOf(CacheTokenRepository::class);
})->only();

test('throw an exception with invalid broker config', function (): void {
    $broker = fn () => $this->manager->broker('invalid');

    expect($broker)->toThrow(InvalidArgumentException::class, 'Password resetter [invalid] is not defined.');
})->only();
