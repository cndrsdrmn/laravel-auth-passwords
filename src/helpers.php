<?php

use Cndrsdrmn\Passwords\TokenGenerator;

if (!function_exists('random_number')) {
    /**
     * Generate for random number.
     *
     * @param  int  $length
     * @param  bool  $withoutZero
     * @return string
     */
    function random_number(int $length = 6, bool $withoutZero = false): string
    {
        $generator = app(TokenGenerator::class);

        return $withoutZero ? $generator->createWithoutZero($length) : $generator->create($length);
    }
}
