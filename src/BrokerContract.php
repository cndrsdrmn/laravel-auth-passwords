<?php

namespace Cndrsdrmn\Passwords;

use Illuminate\Contracts\Auth\PasswordBroker as PasswordBrokerContract;

interface BrokerContract extends PasswordBrokerContract
{
    /**
     * Constant representing a token is unverified.
     *
     * @var string
     */
    const UNVERIFIED_TOKEN = 'passwords.unverified';

    /**
     * Constant representing a token is verified.
     *
     * @var string
     */
    const VERIFIED_TOKEN = 'passwords.verified';

    /**
     * Mark a password resets token as used.
     *
     * @param  array  $credentials
     * @return string
     */
    public function markAsUsed(array $credentials): string;
}
