<?php

declare(strict_types=1);

namespace App\Security;

final class CsrfToken
{
    private const SESSION_KEY = 'csrf_token';

    public function token(): string
    {
        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(16));
        }

        return (string) $_SESSION[self::SESSION_KEY];
    }

    public function isValid(?string $submittedToken): bool
    {
        if ($submittedToken === null) {
            return false;
        }

        return hash_equals($this->token(), $submittedToken);
    }

    public function assertValid(?string $submittedToken): void
    {
        if (!$this->isValid($submittedToken)) {
            http_response_code(400);
            throw new \RuntimeException('Invalid CSRF token.');
        }
    }
}
