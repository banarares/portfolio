<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\AdminGuard;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\View;
use App\Models\Setting;
use App\Services\PortfolioContext;

final class AdminSettingsController
{
    public function edit(): void
    {
        AdminGuard::requireLogin();

        $ctx      = new PortfolioContext();
        $settings = $ctx->settings;

        View::render('admin/settings/form', [
            'settings' => $settings,
            'csrf'     => Csrf::token(),
        ]);
    }

    public function update(): void
    {
        AdminGuard::requireLogin();

        if (!Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(419);
            echo 'Invalid CSRF token';
            exit;
        }

        $userId  = Auth::userId() ?? 0;
        $model   = new Setting();

        $data = [
            'site_name'                => trim((string)($_POST['site_name'] ?? '')),
            'site_tagline'             => trim((string)($_POST['site_tagline'] ?? '')),
            'canonical_base_url'       => rtrim(trim((string)($_POST['canonical_base_url'] ?? '')), '/'),
            'default_meta_title'       => trim((string)($_POST['default_meta_title'] ?? '')),
            'default_meta_description' => trim((string)($_POST['default_meta_description'] ?? '')),
            'default_keywords'         => trim((string)($_POST['default_keywords'] ?? '')),
            'email_public'             => trim((string)($_POST['email_public'] ?? '')),
            'linkedin_url'             => trim((string)($_POST['linkedin_url'] ?? '')),
            'github_url'               => trim((string)($_POST['github_url'] ?? '')),
        ];

        $model->upsert($userId, $data);

        header('Location: /admin/settings');
        exit;
    }
}
