<?php

namespace Cndrsdrmn\Passwords;

use Illuminate\Auth\Passwords\PasswordBroker as Broker;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class PasswordBroker extends Broker implements BrokerContract
{
    /**
     * The password token repository.
     *
     * @var \Cndrsdrmn\Passwords\TokenRepositoryInterface
     */
    protected $tokens;

    /**
     * Mark a password resets token as used.
     *
     * @param  array  $credentials
     * @return string
     */
    public function markAsUsed(array $credentials): string
    {
        $user = $this->validateIsUsed($credentials);

        if (!$user instanceof CanResetPasswordContract) {
            return $user;
        }

        return $this->tokens->markAsUsed($user) > 0
            ? static::VERIFIED_TOKEN
            : static::UNVERIFIED_TOKEN;
    }

    /**
     * Validate a password reset is used for the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\CanResetPassword|string
     */
    protected function validateIsUsed(array $credentials)
    {
        if (is_null($user = $this->getUser($credentials))) {
            return static::INVALID_USER;
        }

        if ($this->tokens->ensureIsUsed($user, $credentials['token'])) {
            return static::INVALID_TOKEN;
        }

        return $user;
    }

    /**
     * Validate a password reset for the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\CanResetPassword|string
     */
    protected function validateReset(array $credentials)
    {
        if (is_null($user = $this->getUser($credentials))) {
            return static::INVALID_USER;
        }

        if (!$this->tokens->ensureIsUsed($user, $credentials['token'])) {
            return static::INVALID_TOKEN;
        }

        return $user;
    }
}
