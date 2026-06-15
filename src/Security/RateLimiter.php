<?php

declare(strict_types=1);

namespace App\Security;

use PDO;

/**
 * A small, persistent fixed-window rate limiter backed by SQLite.
 *
 * Each "bucket" (e.g. "login:198.51.100.7") gets a counter and a window start
 * timestamp. Once the window elapses the counter resets. This survives across
 * requests and processes, unlike a session-only approach.
 */
final class RateLimiter
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * Registers one hit against the bucket and reports whether the caller is
     * now over the limit. Returns true when the action should be BLOCKED.
     *
     * @param string $key            Identifier for the action + client (e.g. "vote:<ip>").
     * @param int    $maxAttempts    Allowed hits per window.
     * @param int    $windowSeconds  Length of the window in seconds.
     */
    public function tooManyAttempts(string $key, int $maxAttempts, int $windowSeconds): bool
    {
        $now = time();
        $windowStart = $now - $windowSeconds;

        $this->pdo->beginTransaction();
        try {
            $select = $this->pdo->prepare(
                'SELECT hits, window_start FROM rate_limits WHERE bucket = ?'
            );
            $select->execute([$key]);
            $row = $select->fetch();

            if ($row === false || (int) $row['window_start'] < $windowStart) {
                // No record, or the previous window has expired: start fresh.
                $upsert = $this->pdo->prepare(
                    'INSERT INTO rate_limits (bucket, hits, window_start)
                        VALUES (?, 1, ?)
                     ON CONFLICT(bucket) DO UPDATE SET hits = 1, window_start = excluded.window_start'
                );
                $upsert->execute([$key, $now]);
                $hits = 1;
            } else {
                $hits = (int) $row['hits'] + 1;
                $update = $this->pdo->prepare(
                    'UPDATE rate_limits SET hits = ? WHERE bucket = ?'
                );
                $update->execute([$hits, $key]);
            }

            $this->pdo->commit();
        } catch (\Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }

        return $hits > $maxAttempts;
    }

    /**
     * Clears a bucket, e.g. after a successful login.
     */
    public function clear(string $key): void
    {
        $statement = $this->pdo->prepare('DELETE FROM rate_limits WHERE bucket = ?');
        $statement->execute([$key]);
    }

    /**
     * Best-effort client identifier. REMOTE_ADDR is used by default because
     * forwarded headers can be spoofed unless a trusted proxy sets them.
     */
    public static function clientIp(): string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        return is_string($ip) && $ip !== '' ? $ip : 'unknown';
    }
}
