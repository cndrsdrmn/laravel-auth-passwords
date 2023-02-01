<?php

namespace Tests\Unit;

use Cndrsdrmn\Passwords\PasswordBroker;
use Cndrsdrmn\Passwords\PasswordBrokerManager;
use Cndrsdrmn\Passwords\PasswordResetServiceProvider;
use Tests\TestCase;

class PasswordResetServiceProviderTest extends TestCase
{
    public function testRegisterPasswordBroker()
    {
        $service = new PasswordResetServiceProvider($this->app);
        $service->register();

        $this->assertInstanceOf(PasswordBrokerManager::class, $this->app->make('auth.password'));
        $this->assertInstanceOf(PasswordBroker::class, $this->app->make('auth.password.broker'));
    }
}
