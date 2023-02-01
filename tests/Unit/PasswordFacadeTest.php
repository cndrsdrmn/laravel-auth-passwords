<?php

namespace Tests\Unit;

use Cndrsdrmn\Passwords\Password;
use Tests\TestCase;

class PasswordFacadeTest extends TestCase
{
    public function testTokenMarkAsUsed()
    {
        $credentials = ['email' => 'foo@example.org'];

        Password::shouldReceive('markAsUsed')
            ->with($credentials)
            ->andReturn(Password::VERIFIED_TOKEN);

        $this->assertEquals(Password::VERIFIED_TOKEN, Password::markAsUsed($credentials));
    }
}
