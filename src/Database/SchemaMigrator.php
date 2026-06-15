<?php

declare(strict_types=1);

namespace App\Database;

use PDO;

final class SchemaMigrator
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function migrate(): void
    {
        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS surveys (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                short_code TEXT NOT NULL UNIQUE,
                question TEXT NOT NULL,
                expires_at TEXT DEFAULT NULL,
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
            )'
        );

        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS survey_options (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                survey_id INTEGER NOT NULL,
                label TEXT NOT NULL,
                vote_count INTEGER NOT NULL DEFAULT 0,
                FOREIGN KEY (survey_id) REFERENCES surveys(id) ON DELETE CASCADE
            )'
        );

        $this->pdo->exec(
            'CREATE INDEX IF NOT EXISTS idx_survey_options_survey_id
                ON survey_options(survey_id)'
        );

        // Fixed-window counters for rate limiting (login attempts, voting, ...).
        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS rate_limits (
                bucket TEXT PRIMARY KEY,
                hits INTEGER NOT NULL DEFAULT 0,
                window_start INTEGER NOT NULL
            )'
        );

        $this->addColumnIfMissing('surveys', 'expires_at', 'TEXT DEFAULT NULL');
    }

    /**
     * Adds a column to an existing table when upgrading older databases.
     */
    private function addColumnIfMissing(string $table, string $column, string $definition): void
    {
        $statement = $this->pdo->query(sprintf('PRAGMA table_info(%s)', $table));
        $columns = $statement === false ? [] : $statement->fetchAll();

        foreach ($columns as $existing) {
            if (($existing['name'] ?? null) === $column) {
                return;
            }
        }

        $this->pdo->exec(sprintf('ALTER TABLE %s ADD COLUMN %s %s', $table, $column, $definition));
    }
}
