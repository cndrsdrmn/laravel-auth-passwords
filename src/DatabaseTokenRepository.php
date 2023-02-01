<?php

namespace Cndrsdrmn\Passwords;

use Illuminate\Auth\Passwords\DatabaseTokenRepository as TokenRepository;
use Illuminate\Contracts\Auth\CanResetPassword;

class DatabaseTokenRepository extends TokenRepository implements TokenRepositoryInterface
{
    /**
     * Create a new token for the user.
     *
     * @return string
     */
    public function createNewToken()
    {
        return random_number(withoutZero: true);
    }

    /**
     * Determine if a token record exists and is used.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $token
     * @return bool
     */
    public function ensureIsUsed(CanResetPassword $user, string $token): bool
    {
        $record = (array) $this->retrieveToken([
            'email' => $user->getEmailForPasswordReset(),
            'is_used' => true,
        ]);

        return $record &&
            !$this->tokenExpired($record['created_at']) &&
            $this->hasher->check($token, $record['token']);
    }

    /**
     * Mark a token of password resets is used.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return int
     */
    public function markAsUsed(CanResetPassword $user): int
    {
        return $this->getTable()
            ->where(['email' => $user->getEmailForPasswordReset(), 'is_used' => false])
            ->update(['is_used' => true]);
    }

    /**
     * Retrieve record for password resets given attributes.
     *
     * @param  array  $attributes
     * @return object|null
     */
    protected function retrieveToken(array $attributes = [])
    {
        return $this->getTable()
            ->where(array_merge(['is_used' => false], $attributes))
            ->first();
    }
}
