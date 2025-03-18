<?php

declare(strict_types=1);

namespace Cndrsdrmn\LaravelPasswords;

use Illuminate\Auth\Passwords\PasswordBroker;

final class Broker extends PasswordBroker implements BrokerInterface
{
    /**
     * The password token repository.
     *
     * @var TokenRepositoryInterface
     */
    protected $tokens;

    /**
     * Mark a password resets token as verified.
     *
     * @param  array{email: string, token: string}  $credentials
     */
    public function markVerified(array $credentials): string
    {
        if (is_null($user = $this->getUser($credentials))) {
            return self::INVALID_USER;
        }

        return $this->tokens->markVerified($user, $credentials['token'])
            ? self::VERIFIED_TOKEN
            : self::UNVERIFIED_TOKEN;
    }
}
