<?php

declare(strict_types=1);

namespace App\Survey;

final class VoteTracker
{
    private const SESSION_KEY = 'cast_votes';

    public function hasVoted(int $surveyId): bool
    {
        return !empty($_SESSION[self::SESSION_KEY][$surveyId]);
    }

    public function markVoted(int $surveyId): void
    {
        $_SESSION[self::SESSION_KEY][$surveyId] = true;
    }
}
