<?php

namespace App\Core;

final class Cache
{
    public static function clear(): void
    {
        $dir = dirname(__DIR__, 2) . '/storage/cache';
        if (!is_dir($dir)) {
            return;
        }

        foreach (glob($dir . '/*.html') as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}