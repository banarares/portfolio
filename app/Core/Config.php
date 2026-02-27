<?php

declare(strict_types=1);

namespace App\Core;

use Dotenv\Dotenv;

final class Config
{
    private static bool $loaded = false;

    public static function load(string $rootPath): void
    {
        if (self::$loaded) {
            return;
        }

        if (is_file($rootPath . '/.env')) {
            Dotenv::createImmutable($rootPath)->safeLoad();
        }

        self::$loaded = true;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }

        $env = getenv($key);
        return $env !== false ? $env : $default;
    }

    public static function bool(string $key, bool $default = false): bool
    {
        $value = self::get($key, $default);
        if ($value === null) {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
