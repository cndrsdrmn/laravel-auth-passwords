<?php

namespace Tests\Unit;

use Cndrsdrmn\Passwords\PasswordBroker;
use Cndrsdrmn\Passwords\TokenRepositoryInterface;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\UserProvider;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class PasswordBrokerTest extends TestCase
{
    public function testMarkAsUsedReturnsInvalidUser()
    {
        $broker = new PasswordBroker($this->mockTokenRepository(), $this->mockUserProvider(['is_null' => true]));

        $this->assertSame(PasswordBroker::INVALID_USER, $broker->markAsUsed(['foo']));
    }

    public function testMarkAsUsedReturnsInvalidToken()
    {
        $broker = new PasswordBroker($this->mockTokenRepository(), $this->mockUserProvider());

        $this->assertSame(
            expected: PasswordBroker::INVALID_TOKEN,
            actual: $broker->markAsUsed(['token' => 'foo'])
        );
    }

    public function testMarkAsUsedReturnsFailedMarked()
    {
        $broker = new PasswordBroker(
            $this->mockTokenRepository(['ensure' => false, 'marked' => 0]),
            $this->mockUserProvider()
        );

        $this->assertSame(
            expected: PasswordBroker::UNVERIFIED_TOKEN,
            actual: $broker->markAsUsed(['token' => 'foo'])
        );
    }

    public function testMarkAsUsedReturnsTokenMarked()
    {
        $broker = new PasswordBroker($this->mockTokenRepository(['ensure' => false]), $this->mockUserProvider());

        $this->assertSame(
            expected: PasswordBroker::VERIFIED_TOKEN,
            actual: $broker->markAsUsed(['token' => 'foo'])
        );
    }

    public function testResetReturnsInvalidUser()
    {
        $broker = new PasswordBroker($this->mockTokenRepository(), $this->mockUserProvider(['is_null' => true]));

        $this->assertSame(
            expected: PasswordBroker::INVALID_USER,
            actual: $broker->reset(['token' => 'foo'], function () {
            })
        );
    }

    public function testResetReturnsInvalidToken()
    {
        $broker = new PasswordBroker($this->mockTokenRepository(['ensure' => false]), $this->mockUserProvider());

        $this->assertSame(
            expected: PasswordBroker::INVALID_TOKEN,
            actual: $broker->reset(['token' => 'foo'], function () {
            })
        );
    }

    public function testResetReturnsPasswordReset()
    {
        $broker = new PasswordBroker($this->mockTokenRepository(), $this->mockUserProvider());

        $this->assertSame(
            expected: PasswordBroker::PASSWORD_RESET,
            actual: $broker->reset(['token' => 'foo', 'password' => 'bar'], function () {
            })
        );
    }

    protected function mockTokenRepository(array $attributes = []): TokenRepositoryInterface|m\MockInterface
    {
        return tap(m::mock(TokenRepositoryInterface::class), function ($mock) use ($attributes) {
            $mock
                ->shouldReceive('delete')
                ->withAnyArgs()
                ->andReturnUndefined();

            $mock
                ->shouldReceive('ensureIsUsed')
                ->withAnyArgs()
                ->andReturn($attributes['ensure'] ?? true);

            $mock
                ->shouldReceive('markAsUsed')
                ->withAnyArgs()
                ->andReturn($attributes['marked'] ?? 1);
        });
    }

    protected function mockUserProvider(array $attributes = []): UserProvider|m\MockInterface
    {
        return tap(m::mock(UserProvider::class), function ($mock) use ($attributes) {
            $isNullable = $attributes['is_null'] ?? false;
            $user = $attributes['user'] ?? m::mock(CanResetPassword::class);

            $mock
                ->shouldReceive('retrieveByCredentials')
                ->withAnyArgs()
                ->andReturn($isNullable ? null : $user);
        });
    }

    protected function tearDown(): void
    {
        m::close();
    }
}
