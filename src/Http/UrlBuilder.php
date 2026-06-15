<?php

declare(strict_types=1);

namespace App\Http;

final class UrlBuilder
{
    public function baseUrl(): string
    {
        $protocol = $this->isHttps() ? 'https' : 'http';
        $host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');
        $directory = str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '')));
        $directory = rtrim($directory, '/');

        return $protocol . '://' . $host . $directory;
    }

    public function surveyUrl(string $shortCode): string
    {
        return $this->baseUrl() . '/survey.php?c=' . urlencode($shortCode);
    }

    public function redirect(string $location): never
    {
        header('Location: ' . $location, true, 302);
        exit;
    }

    private function isHttps(): bool
    {
        $https = $_SERVER['HTTPS'] ?? '';
        return $https !== '' && strcasecmp((string) $https, 'off') !== 0;
    }
}
