<?php

namespace Tests\Unit;

use Cndrsdrmn\Passwords\DatabaseTokenRepository;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Carbon;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class DatabaseTokenRepositoryTest extends TestCase
{
    public function testCreateNewTokenRecord()
    {
        $user = $this->mockUserCanResetPassword(['email' => 'email']);

        $userTokenEntity = $this->mockUserTokenEntity();
        $userTokenEntity->shouldReceive('delete')->once();
        $userTokenEntity->shouldReceive('insert')->once();

        $token = $this->repositoryInstance($userTokenEntity)->create($user);

        $this->assertIsString($token);
        $this->assertEquals(6, strlen($token));
        $this->assertStringNotContainsString('0', $token);
    }

    public function testEnsureIsUsedReturnsFalseIfNoRowFoundForUser()
    {
        $user = $this->mockUserCanResetPassword(['email' => 'email']);

        $userTokenEntity = $this->mockUserTokenEntity();
        $userTokenEntity
            ->shouldReceive('first')
            ->andReturnNull();

        $repository = $this->repositoryInstance($userTokenEntity);

        $this->assertFalse($repository->ensureIsUsed($user, 'token'));
    }

    public function testEnsureIsUsedReturnsFalseIfTokenIsExpired()
    {
        $timestamps = Carbon::now()->subHour()->toDateTimeString();
        $user = $this->mockUserCanResetPassword(['email' => 'email']);

        $userTokenEntity = $this->mockUserTokenEntity();
        $userTokenEntity
            ->shouldReceive('first')
            ->andReturn((object) [
                'created_at' => $timestamps,
                'email' => 'email',
                'token' => 'hashed-value',
            ]);

        $repository = $this->repositoryInstance($userTokenEntity);

        $this->assertFalse($repository->ensureIsUsed($user, 'token'));
    }

    public function testEnsureIsUsedReturnsFalseIfTokenIsInvalid()
    {
        $timestamps = Carbon::now()->addHour()->toDateTimeString();
        $user = $this->mockUserCanResetPassword(['email' => 'email']);

        $userTokenEntity = $this->mockUserTokenEntity();
        $userTokenEntity
            ->shouldReceive('first')
            ->andReturn((object) [
                'created_at' => $timestamps,
                'email' => 'email',
                'token' => 'hashed-value',
            ]);

        $repository = $this->repositoryInstance($userTokenEntity, $this->mockHasher(['check' => false]));

        $this->assertFalse($repository->ensureIsUsed($user, 'token'));
    }

    public function testEnsureIsUsedReturnsTrue()
    {
        $timestamps = Carbon::now()->addHour()->toDateTimeString();
        $user = $this->mockUserCanResetPassword(['email' => 'email']);

        $userTokenEntity = $this->mockUserTokenEntity();
        $userTokenEntity
            ->shouldReceive('first')
            ->andReturn((object) [
                'created_at' => $timestamps,
                'email' => 'email',
                'token' => 'hashed-value',
            ]);

        $repository = $this->repositoryInstance($userTokenEntity);

        $this->assertTrue($repository->ensureIsUsed($user, 'token'));
    }

    public function testMarkAsUsedReturnsGreatThanZero()
    {
        $user = $this->mockUserCanResetPassword();

        $userTokenEntity = $this->mockUserTokenEntity();
        $userTokenEntity->shouldReceive('update')
            ->withAnyArgs()->andReturn(1);

        $repository = $this->repositoryInstance($userTokenEntity);

        $this->assertGreaterThan(0, $repository->markAsUsed($user));
    }

    public function testMarkAsUsedReturnsIsZero()
    {
        $user = $this->mockUserCanResetPassword();

        $userTokenEntity = $this->mockUserTokenEntity();
        $userTokenEntity->shouldReceive('update')
            ->withAnyArgs()->andReturn(0);

        $repository = $this->repositoryInstance($userTokenEntity);

        $this->assertLessThanOrEqual(0, $repository->markAsUsed($user));
    }

    protected function mockConnection(Builder $userTokenEntity): ConnectionInterface|m\MockInterface
    {
        return tap(m::mock(Connection::class), function ($mock) use ($userTokenEntity) {
            $mock
                ->shouldReceive('table')
                ->withAnyArgs()
                ->andReturn($userTokenEntity);
        });
    }

    protected function mockHasher(array $attributes = []): Hasher|m\MockInterface
    {
        return tap(m::mock(Hasher::class), function ($mock) use ($attributes) {
            $mock
                ->shouldReceive('info')
                ->withAnyArgs()
                ->andReturn($attributes['info'] ?? []);

            $mock
                ->shouldReceive('make')
                ->withAnyArgs()
                ->andReturn($attributes['make'] ?? 'hashed-value');

            $mock
                ->shouldReceive('check')
                ->withAnyArgs()
                ->andReturn($attributes['check'] ?? true);

            $mock
                ->shouldReceive('needsRehash')
                ->withAnyArgs()
                ->andReturn($attributes['needsRehash'] ?? false);
        });
    }

    protected function mockUserCanResetPassword(array $attributes = []): CanResetPassword|m\MockInterface
    {
        return tap(m::mock(CanResetPassword::class), function ($mock) use ($attributes) {
            $mock
                ->shouldReceive('getEmailForPasswordReset')
                ->withAnyArgs()
                ->andReturn($attributes['email'] ?? 'user@example.org');

            $mock
                ->shouldReceive('sendPasswordResetNotification')
                ->withAnyArgs()
                ->andReturnUndefined();
        });
    }

    protected function mockUserTokenEntity(): Builder|m\MockInterface
    {
        return tap(m::mock(Builder::class), function ($mock) {
            $mock
                ->shouldReceive('where')
                ->withAnyArgs()
                ->andReturnSelf();
        });
    }

    protected function repositoryInstance(Builder $userTokenEntity = null, Hasher $hasher = null)
    {
        return new DatabaseTokenRepository(
            connection: $this->mockConnection($userTokenEntity ?? $this->mockUserTokenEntity()),
            hasher: $hasher ?? $this->mockHasher(),
            table: 'table',
            hashKey: 'key'
        );
    }

    protected function tearDown(): void
    {
        m::close();
    }
}
