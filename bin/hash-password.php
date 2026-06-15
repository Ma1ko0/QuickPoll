<?php

declare(strict_types=1);

/**
 * Generates a password hash for the QuickPoll admin login.
 *
 * Usage:
 *   php bin/hash-password.php "my-secret-password"
 *   php bin/hash-password.php          (will prompt for the password)
 *
 * Put the resulting hash in config.local.php (admin.password_hash) or the
 * QUICKPOLL_ADMIN_PASSWORD_HASH environment variable.
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This script must be run from the command line.\n");
    exit(1);
}

$password = $argv[1] ?? null;

if ($password === null || $password === '') {
    fwrite(STDOUT, 'Enter a password to hash: ');
    $password = trim((string) fgets(STDIN));
}

if ($password === '') {
    fwrite(STDERR, "No password provided. Aborting.\n");
    exit(1);
}

$hash = password_hash($password, PASSWORD_DEFAULT);

fwrite(STDOUT, "\nPassword hash:\n");
fwrite(STDOUT, $hash . "\n\n");
fwrite(STDOUT, "Add it to config.local.php:\n");
fwrite(STDOUT, "    'admin' => ['password_hash' => '" . $hash . "'],\n");
fwrite(STDOUT, "\nor export it as an environment variable:\n");
fwrite(STDOUT, "    QUICKPOLL_ADMIN_PASSWORD_HASH='" . $hash . "'\n");
