<?php

declare(strict_types=1);

namespace Cndrsdrmn\LaravelPasswords;

use Illuminate\Contracts\Auth\PasswordBroker as PasswordBrokerContract;

interface BrokerInterface extends PasswordBrokerContract
{
    /**
     * Constant representing a token is unverified.
     */
    public const UNVERIFIED_TOKEN = 'passwords.unverified';

    /**
     * Constant representing a token is verified.
     */
    public const VERIFIED_TOKEN = 'passwords.verified';

    /**
     * Mark a password resets token as verified.
     *
     * @param  array{email?: string, token?: string}  $credentials
     */
    public function markVerified(array $credentials): string;
}
