<?php

namespace Cndrsdrmn\Passwords;

use Illuminate\Support\Facades\Password as Facade;

/**
 * @method static string markAsUsed(array $credentials)
 *
 * @see \App\Overrides\Passwords\PasswordBroker
 */
class Password extends Facade
{
    /**
     * Constant representing a token is unverified.
     *
     * @var string
     */
    const UNVERIFIED_TOKEN = PasswordBroker::UNVERIFIED_TOKEN;

    /**
     * Constant representing a token is verified.
     *
     * @var string
     */
    const VERIFIED_TOKEN = PasswordBroker::VERIFIED_TOKEN;
}
