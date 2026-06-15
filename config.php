<?php

declare(strict_types=1);

/**
 * Application configuration.
 *
 * Secrets are NOT stored in this file. The admin password hash and other
 * environment-specific values are read from (in order of precedence):
 *
 *   1. Environment variables (recommended for production), e.g.
 *      QUICKPOLL_ADMIN_PASSWORD_HASH, QUICKPOLL_DATABASE_PATH
 *   2. A local, git-ignored `config.local.php` file (handy for development).
 *
 * Copy `config.example.php` to `config.local.php` to get started, and
 * generate a password hash with `php bin/hash-password.php`.
 */

$localConfig = is_file(__DIR__ . '/config.local.php')
    ? require __DIR__ . '/config.local.php'
    : [];

$envHash = getenv('QUICKPOLL_ADMIN_PASSWORD_HASH');
$adminPasswordHash = $envHash !== false && $envHash !== ''
    ? $envHash
    : (string) ($localConfig['admin']['password_hash'] ?? '');

$envDatabasePath = getenv('QUICKPOLL_DATABASE_PATH');
$databasePath = $envDatabasePath !== false && $envDatabasePath !== ''
    ? $envDatabasePath
    : (string) ($localConfig['database']['path'] ?? __DIR__ . '/data/surveys.sqlite');

return [
    'database' => [
        'path' => $databasePath,
    ],
    'admin' => [
        // A bcrypt/argon2 hash produced by password_hash(). Never a plaintext password.
        'password_hash' => $adminPasswordHash,
    ],
    'view' => [
        'templates_directory' => __DIR__ . '/templates',
    ],
];
