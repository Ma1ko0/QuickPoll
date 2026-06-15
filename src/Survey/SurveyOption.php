<?php

declare(strict_types=1);

namespace App\Survey;

final class SurveyOption
{
    public function __construct(
        public readonly int $id,
        public readonly int $surveyId,
        public readonly string $label,
        public readonly int $voteCount,
    ) {
    }

    public function percentageOf(int $totalVotes): float
    {
        if ($totalVotes <= 0) {
            return 0.0;
        }

        return round(($this->voteCount / $totalVotes) * 100, 1);
    }
}
