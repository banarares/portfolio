<?php

namespace App\Core;

final class Csrf
{
    public static function token(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
        {
            session_start();
        }

        if (empty($_SESSION['_csrf']))
        {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));   
        }

        return $_SESSION['_csrf'];
    }

    public static function verify(?string $token): bool
    {
        if ($token === null) {
            return false;
        }

        if (session_status() !== PHP_SESSION_ACTIVE)
        {
            session_start();
        }

        return isset($_SESSION['_csrf'])
            && !empty($_SESSION['_csrf'])
            && hash_equals($_SESSION['_csrf'], $token);
    }

    public static function verifyOrFail(string $field = '_csrf'): void
    {
        $token = $_POST[$field] ?? '';

        if (!self::verify($token)) {
            http_response_code(419);
            exit('Invalid CSRF token');
        }
    }
}