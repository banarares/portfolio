<?php

declare(strict_types=1);

namespace App\Core;

use App\Services\PortfolioContext;

final class View
{
    public static function render(string $view, array $data = [], string $layout = 'layouts/main'): void
    {
        $base = __DIR__ . '/../Views/';

        $viewPath = $base . $view . '.php';
        if (!is_file($viewPath)) {
            http_response_code(500);
            echo "View not found: {$view}";
            return;
        }

        $layoutPath = $base . $layout . '.php';
        if (!is_file($layoutPath)) {
            http_response_code(500);
            echo "Layout not found: {$layout}";
            return;
        }

        // Always inject $settings so layout/footer can use site config
        if (!isset($data['settings'])) {
            try {
                $ctx = new PortfolioContext();
                $data['settings'] = $ctx->settings;
            } catch (\Throwable $e) {
                $data['settings'] = [];
            }
        }

        extract($data, EXTR_SKIP);

        $viewFile = $viewPath; // used by layout
        require $layoutPath;
    }
}