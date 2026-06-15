<?php

declare(strict_types=1);

namespace App\Security;

final class ShortCodeGenerator
{
    private const ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';

    public function generate(int $length = 6): string
    {
        if ($length < 1) {
            throw new \InvalidArgumentException('Code length must be positive.');
        }

        $alphabetLength = strlen(self::ALPHABET) - 1;
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= self::ALPHABET[random_int(0, $alphabetLength)];
        }

        return $code;
    }
}
