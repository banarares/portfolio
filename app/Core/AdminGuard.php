<?php

namespace App\Core;

use App\Models\User;

final class AdminGuard
{
    public static function requireLogin(): void
    {
        if (!Auth::check()) {
            header('Location: /admin/login');
            exit;
        }

        $user = (new User())->find(Auth::userId());

        if (!$user || ($user['role'] ?? '') !== 'admin') {
            Auth::logout();
            header('Location: /admin/login');
            exit;
        }
    }
}