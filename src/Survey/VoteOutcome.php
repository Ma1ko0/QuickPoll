<?php

declare(strict_types=1);

namespace App\Survey;

enum VoteOutcome: string
{
    case Success = 'success';
    case AlreadyVoted = 'already_voted';
    case InvalidOption = 'invalid_option';
    case Closed = 'closed';
    case RateLimited = 'rate_limited';
}
