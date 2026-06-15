<?php

declare(strict_types=1);

namespace App\Controller;

use App\Auth\AuthService;
use App\Http\UrlBuilder;
use App\Security\CsrfToken;
use App\Security\RateLimiter;
use App\Survey\SurveyService;
use App\View\View;

final class AdminController
{
    private const LOGIN_MAX_ATTEMPTS = 5;
    private const LOGIN_WINDOW_SECONDS = 900; // 15 minutes

    public function __construct(
        private readonly SurveyService $surveyService,
        private readonly AuthService $authService,
        private readonly CsrfToken $csrfToken,
        private readonly View $view,
        private readonly UrlBuilder $urlBuilder,
        private readonly RateLimiter $rateLimiter,
    ) {
    }

    public function handle(): void
    {
        $action = (string) ($_GET['action'] ?? '');

        if ($action === 'logout') {
            $this->authService->logout();
            $this->urlBuilder->redirect('admin.php');
        }

        if (!$this->authService->isAuthenticated()) {
            $this->renderLoginScreen($action);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create') {
            $this->handleCreate();
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
            $this->handleDelete();
            return;
        }

        $this->renderDashboard(flashMessage: $this->consumeFlash());
    }

    private function renderLoginScreen(string $action): void
    {
        $errorMessage = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'login') {
            $this->csrfToken->assertValid($_POST['csrf'] ?? null);

            $rateKey = 'login:' . RateLimiter::clientIp();
            if ($this->rateLimiter->tooManyAttempts($rateKey, self::LOGIN_MAX_ATTEMPTS, self::LOGIN_WINDOW_SECONDS)) {
                $errorMessage = 'Too many login attempts. Please try again later.';
            } else {
                $password = (string) ($_POST['password'] ?? '');

                if ($this->authService->attemptLogin($password)) {
                    $this->rateLimiter->clear($rateKey);
                    $this->urlBuilder->redirect('admin.php');
                }

                $errorMessage = 'Invalid password.';
            }
        }

        echo $this->view->renderPage(
            title: 'Admin Sign-in',
            template: 'admin_login',
            data: [
                'csrf' => $this->csrfToken->token(),
                'errorMessage' => $errorMessage,
            ],
            activeNav: 'admin'
        );
    }

    private function handleCreate(): void
    {
        $this->csrfToken->assertValid($_POST['csrf'] ?? null);

        $question = (string) ($_POST['question'] ?? '');
        $optionLabels = (array) ($_POST['options'] ?? []);
        $expiresAt = (string) ($_POST['expires_at'] ?? '');

        try {
            $survey = $this->surveyService->createSurvey($question, $optionLabels, $expiresAt);
            $_SESSION['flash'] = sprintf(
                'Survey created. Share URL: %s',
                $this->urlBuilder->surveyUrl($survey->shortCode)
            );
            $this->urlBuilder->redirect('admin.php');
        } catch (\InvalidArgumentException $exception) {
            $this->renderDashboard(
                errorMessage: $exception->getMessage(),
                submittedQuestion: $question,
                submittedOptions: $optionLabels,
                submittedExpiry: $expiresAt
            );
        }
    }

    private function handleDelete(): void
    {
        $this->csrfToken->assertValid($_POST['csrf'] ?? null);

        $surveyId = (int) ($_POST['survey_id'] ?? 0);
        if ($surveyId > 0) {
            $this->surveyService->deleteSurvey($surveyId);
            $_SESSION['flash'] = 'Survey deleted.';
        }

        $this->urlBuilder->redirect('admin.php');
    }

    /**
     * @param string[] $submittedOptions
     */
    private function renderDashboard(
        ?string $flashMessage = null,
        ?string $errorMessage = null,
        string $submittedQuestion = '',
        array $submittedOptions = [],
        string $submittedExpiry = '',
    ): void {
        $surveys = $this->surveyService->listSurveys();

        echo $this->view->renderPage(
            title: 'Admin Dashboard',
            template: 'admin_dashboard',
            data: [
                'surveys' => $surveys,
                'csrf' => $this->csrfToken->token(),
                'flashMessage' => $flashMessage,
                'errorMessage' => $errorMessage,
                'submittedQuestion' => $submittedQuestion,
                'submittedOptions' => $submittedOptions,
                'submittedExpiry' => $submittedExpiry,
            ],
            activeNav: 'admin'
        );
    }

    private function consumeFlash(): ?string
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        return $flash === null ? null : (string) $flash;
    }
}
