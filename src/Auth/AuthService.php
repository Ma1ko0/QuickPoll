<?php

declare(strict_types=1);

namespace App\Auth;

final class AuthService
{
    private const SESSION_KEY = 'is_admin_authenticated';

    /**
     * @param string $adminPasswordHash A bcrypt/argon2 hash produced by password_hash().
     */
    public function __construct(private readonly string $adminPasswordHash)
    {
    }

    public function isAuthenticated(): bool
    {
        return !empty($_SESSION[self::SESSION_KEY]);
    }

    public function attemptLogin(string $password): bool
    {
        // No password configured yet: refuse every login instead of granting access.
        if ($this->adminPasswordHash === '') {
            return false;
        }

        if (!password_verify($password, $this->adminPasswordHash)) {
            return false;
        }

        $_SESSION[self::SESSION_KEY] = true;
        session_regenerate_id(true);

        return true;
    }

    public function logout(): void
    {
        unset($_SESSION[self::SESSION_KEY]);
        session_regenerate_id(true);
    }
}
