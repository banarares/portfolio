<?php

declare(strict_types=1);

namespace App\Services;

final class SlugService
{
    public static function make(string $text): string
    {
        $text = trim(mb_strtolower($text, 'UTF-8'));

        // RO chars
        $map = [
            'ă' => 'a',
            'â' => 'a',
            'î' => 'i',
            'ș' => 's',
            'ş' => 's',
            'ț' => 't',
            'ţ' => 't',
        ];

        $text = strtr($text, $map);

        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
        $text = trim($text, '-');

        return $text !== '' ? $text : 'n-a';
    }
}