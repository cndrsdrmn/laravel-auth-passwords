<?php

namespace Cndrsdrmn\Passwords;

use Illuminate\Auth\Passwords\TokenRepositoryInterface as BaseTokenRepositoryInterface;
use Illuminate\Contracts\Auth\CanResetPassword;

interface TokenRepositoryInterface extends BaseTokenRepositoryInterface
{
    /**
     * Determine if a token record exists and is used.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $token
     * @return bool
     */
    public function ensureIsUsed(CanResetPassword $user, string $token): bool;

    /**
     * Mark a token of password resets is used.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return int
     */
    public function markAsUsed(CanResetPassword $user): int;
}
