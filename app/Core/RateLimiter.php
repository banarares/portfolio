<?php

namespace App\Core;

final class RateLimiter
{
    private static function dir(): string
    {
        return dirname(__DIR__, 2) . '/storage/rate_limits';
    }

    private static function file(string $key): string
    {
        return self::dir() . '/' . md5($key) . '.json';
    }

    public static function attempt(string $key, int $maxAttempts, int $decaySeconds): bool
    {
        $dir = self::dir();
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $file = self::file($key);
        $now = time();

        $state = [
            'count' => 0,
            'reset' => $now + $decaySeconds
        ];

        if (is_file($file)) {
            $raw = json_decode((string)file_get_contents($file), true);
            if (is_array($raw)) {
                $state = array_merge($state, $raw);
            }

            // Reset if decay time has passed
            if (($state['reset'] ?? 0) <= $now) {
                $state['count'] = 0;
                $state['reset'] = $now + $decaySeconds;
            }
        }

        $state['count'] = (int)($state['count'] ?? 0) + 1;

        file_put_contents($file, json_encode($state), LOCK_EX);

        return $state['count'] <= $maxAttempts;
    }

    public static function remaining(string $key, int $maxAttempts, int $decaySeconds): int
    {
        $file = self::file($key);
        if (!is_file($file)) {
            return $maxAttempts;
        }

        $raw = json_decode((string)file_get_contents($file), true);
        if (!is_array($raw)) {
            return $maxAttempts;
        }

        $count = (int)($raw['count'] ?? 0);
        $reset = (int)($raw['reset'] ?? 0);

        if ($reset <= time()) {
            return $maxAttempts;
        }

        return max(0, $maxAttempts - $count);
    }
}