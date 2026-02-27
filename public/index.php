<?php

declare(strict_types=1);

ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');

if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', '1');
}

session_start();

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');

if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

// Basic CSP
header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline'; script-src 'self'; base-uri 'self'; frame-ancestors 'self'; form-action 'self'");

// Permission Policy
header("Permissions-Policy: geolocation=(), microphone=(), camera=()");

use App\Core\App;
use App\Core\Config;

require_once __DIR__.'/../vendor/autoload.php';

Config::load(dirname(__DIR__));

set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(function (\Throwable $e) {
    http_response_code(500);

    if (Config::bool('APP_DEBUG', false)) {
        echo '<pre>' . htmlspecialchars((string)$e, ENT_QUOTES, 'UTF-8') . '</pre>';
        return;
    }
    \App\Core\View::render('errors/500', ['seo' =>  ['title' => 'Server Error']]);
});

$app = new App();
$app->run();