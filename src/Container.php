<?php

declare(strict_types=1);

namespace App;

use App\Auth\AuthService;
use App\Controller\AdminController;
use App\Controller\HomeController;
use App\Controller\SurveyController;
use App\Database\Connection;
use App\Database\SchemaMigrator;
use App\Http\UrlBuilder;
use App\Security\CsrfToken;
use App\Security\RateLimiter;
use App\Security\ShortCodeGenerator;
use App\Survey\SurveyRepository;
use App\Survey\SurveyService;
use App\Survey\VoteTracker;
use App\View\View;

final class Container
{
    private array $services = [];

    public function __construct(private readonly array $config)
    {
    }

    public function authService(): AuthService
    {
        return $this->services[__FUNCTION__] ??= new AuthService(
            (string) $this->config['admin']['password_hash']
        );
    }

    public function csrfToken(): CsrfToken
    {
        return $this->services[__FUNCTION__] ??= new CsrfToken();
    }

    public function urlBuilder(): UrlBuilder
    {
        return $this->services[__FUNCTION__] ??= new UrlBuilder();
    }

    public function shortCodeGenerator(): ShortCodeGenerator
    {
        return $this->services[__FUNCTION__] ??= new ShortCodeGenerator();
    }

    public function rateLimiter(): RateLimiter
    {
        return $this->services[__FUNCTION__] ??= new RateLimiter(
            $this->connection()->pdo()
        );
    }

    public function connection(): Connection
    {
        return $this->services[__FUNCTION__] ??= $this->buildConnection();
    }

    public function surveyRepository(): SurveyRepository
    {
        return $this->services[__FUNCTION__] ??= new SurveyRepository(
            $this->connection()->pdo()
        );
    }

    public function surveyService(): SurveyService
    {
        return $this->services[__FUNCTION__] ??= new SurveyService(
            $this->surveyRepository(),
            $this->shortCodeGenerator()
        );
    }

    public function voteTracker(): VoteTracker
    {
        return $this->services[__FUNCTION__] ??= new VoteTracker();
    }

    public function view(): View
    {
        return $this->services[__FUNCTION__] ??= new View(
            (string) $this->config['view']['templates_directory'],
            $this->authService(),
            $this->urlBuilder()
        );
    }

    public function homeController(): HomeController
    {
        return new HomeController(
            $this->surveyService(),
            $this->view()
        );
    }

    public function adminController(): AdminController
    {
        return new AdminController(
            $this->surveyService(),
            $this->authService(),
            $this->csrfToken(),
            $this->view(),
            $this->urlBuilder(),
            $this->rateLimiter()
        );
    }

    public function surveyController(): SurveyController
    {
        return new SurveyController(
            $this->surveyService(),
            $this->voteTracker(),
            $this->csrfToken(),
            $this->view(),
            $this->urlBuilder(),
            $this->rateLimiter()
        );
    }

    private function buildConnection(): Connection
    {
        $databasePath = (string) $this->config['database']['path'];
        $directory = dirname($databasePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $connection = new Connection($databasePath);
        (new SchemaMigrator($connection->pdo()))->migrate();

        return $connection;
    }
}
