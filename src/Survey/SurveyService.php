<?php

declare(strict_types=1);

namespace App\Survey;

use App\Security\ShortCodeGenerator;

final class SurveyService
{
    private const MAX_CODE_GENERATION_ATTEMPTS = 20;
    private const MINIMUM_OPTION_COUNT = 2;

    public function __construct(
        private readonly SurveyRepository $repository,
        private readonly ShortCodeGenerator $shortCodeGenerator,
    ) {
    }

    /**
     * @param string[] $optionLabels
     * @param string   $expiresAtInput Optional date/time string (e.g. from a datetime-local input).
     *                                  Empty string means the poll never expires.
     */
    public function createSurvey(string $question, array $optionLabels, string $expiresAtInput = ''): Survey
    {
        $question = trim($question);
        $normalisedLabels = array_values(array_filter(
            array_map(static fn(string $label): string => trim($label), $optionLabels),
            static fn(string $label): bool => $label !== ''
        ));

        if ($question === '') {
            throw new \InvalidArgumentException('A survey must have a question.');
        }

        if (count($normalisedLabels) < self::MINIMUM_OPTION_COUNT) {
            throw new \InvalidArgumentException(
                sprintf('A survey requires at least %d options.', self::MINIMUM_OPTION_COUNT)
            );
        }

        $expiresAt = $this->normaliseExpiry($expiresAtInput);

        return $this->repository->create(
            $question,
            $this->generateUniqueShortCode(),
            $normalisedLabels,
            $expiresAt
        );
    }

    /**
     * Validates the optional expiry input and returns it as a normalised
     * "Y-m-d H:i:s" string, or null when the poll should never expire.
     */
    private function normaliseExpiry(string $expiresAtInput): ?string
    {
        $expiresAtInput = trim($expiresAtInput);
        if ($expiresAtInput === '') {
            return null;
        }

        $timestamp = strtotime($expiresAtInput);
        if ($timestamp === false) {
            throw new \InvalidArgumentException('The closing date is not a valid date/time.');
        }

        if ($timestamp <= time()) {
            throw new \InvalidArgumentException('The closing date must be in the future.');
        }

        return date('Y-m-d H:i:s', $timestamp);
    }

    public function deleteSurvey(int $surveyId): void
    {
        $this->repository->delete($surveyId);
    }

    public function findByShortCode(string $shortCode): ?Survey
    {
        return $this->repository->findByShortCode($shortCode);
    }

    /**
     * @return Survey[]
     */
    public function listSurveys(): array
    {
        return $this->repository->findAll();
    }

    public function recordVote(Survey $survey, int $optionId, VoteTracker $voteTracker): VoteOutcome
    {
        if ($survey->isClosed()) {
            return VoteOutcome::Closed;
        }

        if ($voteTracker->hasVoted($survey->id)) {
            return VoteOutcome::AlreadyVoted;
        }

        if (!$this->repository->incrementVote($survey->id, $optionId)) {
            return VoteOutcome::InvalidOption;
        }

        $voteTracker->markVoted($survey->id);

        return VoteOutcome::Success;
    }

    private function generateUniqueShortCode(): string
    {
        for ($attempt = 0; $attempt < self::MAX_CODE_GENERATION_ATTEMPTS; $attempt++) {
            $length = 6 + intdiv($attempt, 5);
            $code = $this->shortCodeGenerator->generate($length);

            if (!$this->repository->shortCodeExists($code)) {
                return $code;
            }
        }

        throw new \RuntimeException('Unable to generate a unique short code.');
    }
}
