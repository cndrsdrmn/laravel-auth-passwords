<?php

declare(strict_types=1);

namespace Cndrsdrmn\LaravelPasswords;

use Illuminate\Auth\Passwords\TokenRepositoryInterface as IlluminateTokenRepositoryInterface;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use SensitiveParameter;

interface TokenRepositoryInterface extends IlluminateTokenRepositoryInterface
{
    /**
     * Mark a token of password resets is verified.
     */
    public function markVerified(CanResetPasswordContract $user, #[SensitiveParameter] string $token): bool;
}
