<?php

namespace Tests\Unit;

use Cndrsdrmn\Passwords\BrokerContract;
use Cndrsdrmn\Passwords\PasswordBrokerManager;
use InvalidArgumentException;
use Mockery as m;
use Tests\TestCase;

class PasswordBrokerManagerTest extends TestCase
{
    public function testBrokerInstanceOfBrokerContract()
    {
        $this->assertInstanceOf(BrokerContract::class, $this->brokerInstance()->broker());
    }

    public function testThrowExceptionWhenConfigIsNull()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->brokerInstance()->broker('foo');
    }

    protected function brokerInstance()
    {
        return new PasswordBrokerManager($this->app);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        m::close();
    }
}
