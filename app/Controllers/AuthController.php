<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\View;
use App\Core\RateLimiter;
use App\Models\User;

final class AuthController
{
    public function loginForm(): void
    {
        if (Auth::check()) {
            header('Location: /admin');
            exit;
        }

        View::render('admin/login', [
            'csrf' => Csrf::token(),
            'error' => null
        ]);
    }

    public function login(): void
    {
        if (!Csrf::verify($_POST['_csrf'] ?? null)) 
        {
            http_response_code(419);
            echo 'Invalid CSRF token';
            return;
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $key ='admin_login:' . $ip;
        if (!RateLimiter::attempt($key, 5, 300)) {
            http_response_code(429);
            View::render('admin/login', [
                'csrf' => Csrf::token(),
                'error' => 'Too many login attempts. Please try again later.'
            ]);
            return;
        }

        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        $user = (new User())->findByEmail($email);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            View::render('admin/login', [
                'csrf' => Csrf::token(),
                'error' => 'Invalid email or password'
            ]);
            return;
        }

        Auth::login((int)$user['id']);
        header('Location: /admin');
        exit;
    }

    public function logout(): void
    {
        if (!Csrf::verify($_POST['_csrf'] ?? null)) 
        {
            http_response_code(419);
            echo 'Invalid CSRF token';
            return;
        }

        Auth::logout();
        header('Location: /admin/login');
        exit;
    }
}