<?php

declare(strict_types=1);

namespace App\Controller;

use App\Survey\SurveyService;
use App\View\View;

final class HomeController
{
    public function __construct(
        private readonly SurveyService $surveyService,
        private readonly View $view,
    ) {
    }

    public function handle(): void
    {
        $surveys = $this->surveyService->listSurveys();

        echo $this->view->renderPage(
            title: 'Overview',
            template: 'home',
            data: ['surveys' => $surveys],
            activeNav: 'home'
        );
    }
}
