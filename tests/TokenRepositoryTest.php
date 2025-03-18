<?php

declare(strict_types=1);

namespace Tests;

use Cndrsdrmn\LaravelPasswords\TokenRepository;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Connection;
use Mockery;
use Mockery\MockInterface;

function createFakeBuilder(?array $attributes = [])
{
    return Mockery::mock(Builder::class, function (MockInterface $mock) use ($attributes): void {
        $mock->shouldReceive('where')
            ->withAnyArgs()
            ->andReturnSelf();

        if (filled($attributes) || is_null($attributes)) {
            $mock->shouldReceive('first')
                ->withAnyArgs()
                ->andReturn((object) $attributes);
        }
    });
}

function createFakeConnection(Builder $builder)
{
    return Mockery::mock(Connection::class, ['table' => $builder]);
}

function createFakeHasher(array $attributes = [])
{
    return Mockery::mock(Hasher::class, array_merge([
        'info' => [],
        'make' => 'hashed-token',
        'check' => true,
        'needsRehash' => false,
    ], $attributes));
}

function createFakeUser(?string $email = null)
{
    return Mockery::mock(CanResetPassword::class, function (MockInterface $mock) use ($email): void {
        $mock->shouldReceive('getEmailForPasswordReset')->andReturn($email);

        $mock->shouldReceive('sendPasswordResetNotification')
            ->withAnyArgs()
            ->andReturnUndefined();
    });
}

function createTokenRepository(?Builder $builder = null, ?Hasher $hasher = null): TokenRepository
{
    return new TokenRepository(
        createFakeConnection($builder ?? createFakeBuilder()),
        $hasher ?? createFakeHasher(),
        'passwords',
        'key'
    );
}

afterEach(function (): void {
    Mockery::close();
});

test('create new token', function (): void {
    $repository = createTokenRepository();

    expect($repository->createNewToken())
        ->toBeString()->toBeNumeric()
        ->toHaveLength(6);
});

test('mark verified return false with an exists record', function (): void {
    $repository = createTokenRepository(createFakeBuilder(null));

    expect($repository->markVerified(createFakeUser(), 'token'))->toBeFalse();
});

test('mark verified return false with invalid hashed token', function (): void {
    $repository = createTokenRepository(createFakeBuilder([
        'created_at' => now()->addHour()->toDateTimeString(),
        'email' => 'user@example.com',
        'token' => 'hashed-token',
    ]), createFakeHasher(['check' => false]));

    expect($repository->markVerified(createFakeUser(), 'invalid-token'))->toBeFalse();
});

test('mark verified return false with expired token', function (): void {
    $repository = createTokenRepository(createFakeBuilder([
        'created_at' => now()->subHour()->toDateTimeString(),
        'email' => 'user@example.com',
        'token' => 'hashed-token',
    ]));

    expect($repository->markVerified(createFakeUser(), 'token'))->toBeFalse();
});

test('mark verified return true', function (): void {
    $builder = createFakeBuilder([
        'created_at' => now()->addHour()->toDateTimeString(),
        'email' => 'user@example.com',
        'token' => 'hashed-token',
    ]);

    $builder->shouldReceive('update')
        ->withAnyArgs()
        ->andReturn(1);

    $repository = createTokenRepository($builder);

    expect($repository->markVerified(createFakeUser(), 'token'))->toBeTrue();
});

test('exists will return false', function (): void {
    $repository = createTokenRepository(createFakeBuilder([
        'created_at' => now()->subHour()->toDateTimeString(),
        'email' => 'user@example.com',
        'token' => 'hashed-token',
    ]));

    expect($repository->exists(createFakeUser(), 'token'))->toBeFalse();
});

test('exists will return true', function (): void {
    $repository = createTokenRepository(createFakeBuilder([
        'created_at' => now()->addHour()->toDateTimeString(),
        'email' => 'user@example.com',
        'token' => 'hashed-token',
    ]));

    expect($repository->exists(createFakeUser(), 'token'))->toBeTrue();
});
