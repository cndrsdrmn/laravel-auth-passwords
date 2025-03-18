<?php

declare(strict_types=1);

namespace Tests;

use Cndrsdrmn\LaravelPasswords\Broker;
use Cndrsdrmn\LaravelPasswords\TokenRepositoryInterface;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\UserProvider;
use Mockery;

afterEach(function (): void {
    Mockery::close();
});

test('mark verified returns invalid user', function (): void {
    $users = Mockery::mock(UserProvider::class, ['retrieveByCredentials' => null]);
    $repository = Mockery::mock(TokenRepositoryInterface::class);

    $broker = new Broker($repository, $users, $this->app['events']);

    expect($broker->markVerified(['email' => 'user@example.com']))->toBe(Broker::INVALID_USER);
})->only();

test('mark verified returns verified token', function (): void {
    $users = Mockery::mock(UserProvider::class, ['retrieveByCredentials' => Mockery::mock(CanResetPassword::class)]);
    $repository = Mockery::mock(TokenRepositoryInterface::class, ['markVerified' => true]);

    $broker = new Broker($repository, $users, $this->app['events']);

    expect($broker->markVerified(['email' => 'user@example.com', 'token' => 'token']))->toBe(Broker::VERIFIED_TOKEN);
})->only();

test('mark verified returns unverified token', function (): void {
    $users = Mockery::mock(UserProvider::class, ['retrieveByCredentials' => Mockery::mock(CanResetPassword::class)]);
    $repository = Mockery::mock(TokenRepositoryInterface::class, ['markVerified' => false]);

    $broker = new Broker($repository, $users, $this->app['events']);

    expect($broker->markVerified(['email' => 'user@example.com', 'token' => 'token']))->toBe(Broker::UNVERIFIED_TOKEN);
})->only();
