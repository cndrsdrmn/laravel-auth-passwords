<?php

declare(strict_types=1);

namespace Cndrsdrmn\LaravelPasswords;

use Cndrsdrmn\PhpStringFormatter\StringFormatter;
use Illuminate\Auth\Passwords\DatabaseTokenRepository;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Str;

final class TokenRepository extends DatabaseTokenRepository implements TokenRepositoryInterface
{
    /**
     * Create a new token for the user.
     */
    public function createNewToken(): string
    {
        return StringFormatter::numerify(Str::padBoth('', 6, '#'));
    }

    /**
     * Determine if a token record exists and is valid.
     *
     * @param  string  $token
     */
    public function exists(CanResetPasswordContract $user, $token): bool
    {
        $record = $this->getRecord($user, true);

        return $this->shouldVerified($record, $token);
    }

    /**
     * Mark a token of password resets is verified.
     */
    public function markVerified(CanResetPasswordContract $user, string $token): bool
    {
        $record = $this->getRecord($user, false);

        if ($this->shouldUnverified($record, $token)) {
            return false;
        }

        return (bool) $this->getTable()->where([
            'email' => $user->getEmailForPasswordReset(),
        ])->update([
            'is_verified' => true,
        ]);
    }

    /**
     * Get password resetter record by given user and verified args.
     *
     * @return array{email: string, is_verified: bool, token: string, created_at: string}
     */
    private function getRecord(CanResetPasswordContract $user, bool $verified): array
    {
        return (array) $this->getTable()->where([ // @phpstan-ignore return.type
            'email' => $user->getEmailForPasswordReset(),
            'is_verified' => $verified,
        ])->first();
    }

    /**
     * Determine if a token record is unverified.
     *
     * @param  array{created_at: string, token: string}  $record
     */
    private function shouldUnverified(array $record, string $token): bool
    {
        return ! $this->shouldVerified($record, $token);
    }

    /**
     * Determine if a token record is verified.
     *
     * @param  array{created_at: string, token: string}  $record
     */
    private function shouldVerified(array $record, string $token): bool
    {
        return $record &&
            ! $this->tokenExpired($record['created_at']) &&
            $this->hasher->check($token, $record['token']);
    }
}
