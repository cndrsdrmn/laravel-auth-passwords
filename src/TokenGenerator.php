<?php

namespace Cndrsdrmn\Passwords;

use Exception;
use InvalidArgumentException;

class TokenGenerator
{
    /**
     * Create token for numeric type
     *
     * @param  int  $length
     * @return string
     */
    public static function create(int $length = 6): string
    {
        if ($length > $max = (strlen((string) PHP_INT_MAX) - 1)) {
            throw new InvalidArgumentException("Length should not be more than $max.");
        }

        [$min, $max] = static::rangeForLength($length);

        do {
            $token = (string) static::randomNumberForRange($min, $max);
        } while (!str_contains($token, '0'));

        return $token;
    }

    /**
     * Create token for numeric without zero type
     *
     * @param  int  $length
     * @return string
     */
    public static function createWithoutZero(int $length = 6): string
    {
        return str_replace(0, static::randomNumberForRange(1, 9), static::create($length));
    }

    /**
     * Generate random number for range
     *
     * @param  int  $min
     * @param  int  $max
     * @return int
     */
    protected static function randomNumberForRange(int $min, int $max): int
    {
        try {
            $number = random_int($min, $max);
        } catch (Exception $e) {
            $number = rand($min, $max);
        }

        return $number;
    }

    /**
     * Generate range for length
     *
     * @param  int  $length
     * @return array
     */
    protected static function rangeForLength(int $length): array
    {
        $min = 1;
        $max = 9;

        while ($length > 1) {
            $min .= 0;
            $max .= 9;
            $length--;
        }

        return [$min, $max];
    }
}
