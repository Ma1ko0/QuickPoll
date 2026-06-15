<?php

declare(strict_types=1);

namespace App\View;

use App\Auth\AuthService;
use App\Http\UrlBuilder;

final class View
{
    public function __construct(
        private readonly string $templatesDirectory,
        private readonly AuthService $authService,
        private readonly UrlBuilder $urlBuilder,
    ) {
    }

    public function render(string $template, array $data = []): string
    {
        $data['view'] = $this;
        $data['urlBuilder'] = $this->urlBuilder;
        $data['isAuthenticated'] = $this->authService->isAuthenticated();

        $templatePath = $this->resolveTemplatePath($template);

        ob_start();
        (static function (string $__templatePath, array $__data): void {
            extract($__data, EXTR_SKIP);
            require $__templatePath;
        })($templatePath, $data);

        return (string) ob_get_clean();
    }

    public function renderPage(string $title, string $template, array $data = [], string $activeNav = ''): string
    {
        $content = $this->render($template, $data);

        return $this->render('layout', [
            'title' => $title,
            'content' => $content,
            'activeNav' => $activeNav,
        ]);
    }

    public function escape(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }

    private function resolveTemplatePath(string $template): string
    {
        $relative = str_replace('.', '/', $template) . '.php';
        $path = $this->templatesDirectory . DIRECTORY_SEPARATOR . $relative;

        if (!is_file($path)) {
            throw new \RuntimeException(sprintf('Template "%s" not found at %s.', $template, $path));
        }

        return $path;
    }
}
