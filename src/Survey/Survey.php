<?php

declare(strict_types=1);

namespace App\Survey;

final class Survey
{
    /**
     * @param SurveyOption[] $options
     */
    public function __construct(
        public readonly int $id,
        public readonly string $shortCode,
        public readonly string $question,
        public readonly string $createdAt,
        public readonly ?string $expiresAt = null,
        public readonly array $options = [],
    ) {
    }

    public function totalVotes(): int
    {
        $total = 0;
        foreach ($this->options as $option) {
            $total += $option->voteCount;
        }

        return $total;
    }

    /**
     * A poll is closed once its expiry time (if any) has passed.
     */
    public function isClosed(): bool
    {
        if ($this->expiresAt === null) {
            return false;
        }

        return strtotime($this->expiresAt) <= time();
    }

    public function isOpen(): bool
    {
        return !$this->isClosed();
    }

    /**
     * @param SurveyOption[] $options
     */
    public function withOptions(array $options): self
    {
        return new self(
            $this->id,
            $this->shortCode,
            $this->question,
            $this->createdAt,
            $this->expiresAt,
            $options
        );
    }
}
