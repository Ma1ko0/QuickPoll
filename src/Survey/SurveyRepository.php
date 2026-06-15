<?php

declare(strict_types=1);

namespace App\Survey;

use PDO;

final class SurveyRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function findByShortCode(string $shortCode): ?Survey
    {
        $statement = $this->pdo->prepare('SELECT * FROM surveys WHERE short_code = ?');
        $statement->execute([$shortCode]);
        $row = $statement->fetch();

        return $row === false ? null : $this->hydrateSurvey($row, $this->loadOptions((int) $row['id']));
    }

    public function findById(int $id): ?Survey
    {
        $statement = $this->pdo->prepare('SELECT * FROM surveys WHERE id = ?');
        $statement->execute([$id]);
        $row = $statement->fetch();

        return $row === false ? null : $this->hydrateSurvey($row, $this->loadOptions($id));
    }

    /**
     * @return Survey[]
     */
    public function findAll(): array
    {
        $rows = $this->pdo
            ->query('SELECT * FROM surveys ORDER BY id DESC')
            ->fetchAll();

        if ($rows === []) {
            return [];
        }

        $surveys = [];
        foreach ($rows as $row) {
            $surveys[] = $this->hydrateSurvey($row, $this->loadOptions((int) $row['id']));
        }

        return $surveys;
    }

    public function shortCodeExists(string $shortCode): bool
    {
        $statement = $this->pdo->prepare('SELECT 1 FROM surveys WHERE short_code = ?');
        $statement->execute([$shortCode]);

        return $statement->fetchColumn() !== false;
    }

    /**
     * @param string[] $optionLabels
     */
    public function create(
        string $question,
        string $shortCode,
        array $optionLabels,
        ?string $expiresAt = null,
    ): Survey {
        $this->pdo->beginTransaction();
        try {
            $insertSurvey = $this->pdo->prepare(
                'INSERT INTO surveys (short_code, question, expires_at) VALUES (?, ?, ?)'
            );
            $insertSurvey->execute([$shortCode, $question, $expiresAt]);
            $surveyId = (int) $this->pdo->lastInsertId();

            $insertOption = $this->pdo->prepare(
                'INSERT INTO survey_options (survey_id, label) VALUES (?, ?)'
            );
            foreach ($optionLabels as $label) {
                $insertOption->execute([$surveyId, $label]);
            }

            $this->pdo->commit();
        } catch (\Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }

        $survey = $this->findById($surveyId);
        if ($survey === null) {
            throw new \RuntimeException('Failed to load survey after creation.');
        }

        return $survey;
    }

    public function delete(int $surveyId): void
    {
        $this->pdo->beginTransaction();
        try {
            $this->pdo->prepare('DELETE FROM survey_options WHERE survey_id = ?')
                ->execute([$surveyId]);
            $this->pdo->prepare('DELETE FROM surveys WHERE id = ?')
                ->execute([$surveyId]);
            $this->pdo->commit();
        } catch (\Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }

    public function incrementVote(int $surveyId, int $optionId): bool
    {
        $statement = $this->pdo->prepare(
            'UPDATE survey_options
                SET vote_count = vote_count + 1
                WHERE id = ? AND survey_id = ?'
        );
        $statement->execute([$optionId, $surveyId]);

        return $statement->rowCount() === 1;
    }

    /**
     * @return SurveyOption[]
     */
    private function loadOptions(int $surveyId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM survey_options WHERE survey_id = ? ORDER BY id'
        );
        $statement->execute([$surveyId]);

        $options = [];
        foreach ($statement->fetchAll() as $row) {
            $options[] = new SurveyOption(
                id: (int) $row['id'],
                surveyId: (int) $row['survey_id'],
                label: (string) $row['label'],
                voteCount: (int) $row['vote_count'],
            );
        }

        return $options;
    }

    /**
     * @param SurveyOption[] $options
     */
    private function hydrateSurvey(array $row, array $options): Survey
    {
        return new Survey(
            id: (int) $row['id'],
            shortCode: (string) $row['short_code'],
            question: (string) $row['question'],
            createdAt: (string) $row['created_at'],
            expiresAt: isset($row['expires_at']) && $row['expires_at'] !== null
                ? (string) $row['expires_at']
                : null,
            options: $options,
        );
    }
}
