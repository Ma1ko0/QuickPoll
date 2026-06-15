<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\UrlBuilder;
use App\Security\CsrfToken;
use App\Security\RateLimiter;
use App\Survey\Survey;
use App\Survey\SurveyService;
use App\Survey\VoteOutcome;
use App\Survey\VoteTracker;
use App\View\View;

final class SurveyController
{
    private const VOTE_MAX_ATTEMPTS = 10;
    private const VOTE_WINDOW_SECONDS = 60;

    public function __construct(
        private readonly SurveyService $surveyService,
        private readonly VoteTracker $voteTracker,
        private readonly CsrfToken $csrfToken,
        private readonly View $view,
        private readonly UrlBuilder $urlBuilder,
        private readonly RateLimiter $rateLimiter,
    ) {
    }

    public function handle(): void
    {
        $shortCode = trim((string) ($_GET['c'] ?? ''));

        if ($shortCode === '') {
            $this->renderNotFound(null);
            return;
        }

        $survey = $this->surveyService->findByShortCode($shortCode);
        if ($survey === null) {
            $this->renderNotFound($shortCode);
            return;
        }

        $errorMessage = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->csrfToken->assertValid($_POST['csrf'] ?? null);
            $errorMessage = $this->processVote($survey);

            if ($errorMessage === null) {
                $this->urlBuilder->redirect('survey.php?c=' . urlencode($survey->shortCode));
            }
        }

        $this->renderSurvey($survey, $errorMessage);
    }

    private function processVote(Survey $survey): ?string
    {
        if ($survey->isClosed()) {
            return 'This poll is closed. Voting is no longer possible.';
        }

        $rateKey = 'vote:' . RateLimiter::clientIp();
        if ($this->rateLimiter->tooManyAttempts($rateKey, self::VOTE_MAX_ATTEMPTS, self::VOTE_WINDOW_SECONDS)) {
            return 'Too many votes from your network. Please slow down and try again shortly.';
        }

        $optionId = (int) ($_POST['option_id'] ?? 0);
        if ($optionId <= 0) {
            return 'Please select an option.';
        }

        $outcome = $this->surveyService->recordVote($survey, $optionId, $this->voteTracker);

        return match ($outcome) {
            VoteOutcome::Success       => null,
            VoteOutcome::AlreadyVoted  => 'You have already voted in this survey.',
            VoteOutcome::InvalidOption => 'The selected option is not valid for this survey.',
            VoteOutcome::Closed        => 'This poll is closed. Voting is no longer possible.',
            VoteOutcome::RateLimited   => 'Too many votes from your network. Please slow down and try again shortly.',
        };
    }

    private function renderSurvey(Survey $survey, ?string $errorMessage): void
    {
        echo $this->view->renderPage(
            title: $survey->question,
            template: 'survey_view',
            data: [
                'survey' => $survey,
                'csrf' => $this->csrfToken->token(),
                'hasVoted' => $this->voteTracker->hasVoted($survey->id),
                'errorMessage' => $errorMessage,
                'shareUrl' => $this->urlBuilder->surveyUrl($survey->shortCode),
            ]
        );
    }

    private function renderNotFound(?string $shortCode): void
    {
        echo $this->view->renderPage(
            title: 'Survey Not Found',
            template: 'survey_not_found',
            data: ['shortCode' => $shortCode]
        );
    }
}
