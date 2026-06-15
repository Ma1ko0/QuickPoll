<?php

declare(strict_types=1);

/**
 * Local configuration template.
 *
 * 1. Copy this file to `config.local.php` (which is git-ignored).
 * 2. Generate a password hash:  php bin/hash-password.php
 * 3. Paste the hash below.
 *
 * In production you can skip this file entirely and instead set the
 * QUICKPOLL_ADMIN_PASSWORD_HASH environment variable.
 */

return [
    'admin' => [
        // Replace with the output of `php bin/hash-password.php`.
        'password_hash' => '$2y$12$REPLACE_THIS_WITH_A_REAL_BCRYPT_HASH',
    ],

    // Optional: override where the SQLite database lives.
    // 'database' => [
    //     'path' => __DIR__ . '/data/surveys.sqlite',
    // ],
];
