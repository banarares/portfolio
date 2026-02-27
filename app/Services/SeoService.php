<?php
declare(strict_types=1);

namespace App\Services;

final class SeoService
{
    public static function page(array $settings, array $page = []): array
    {
        // Use trim + ?: so empty strings fall through to the next candidate
        $title = trim($page['meta_title'] ?? '')
            ?: trim($settings['default_meta_title'] ?? '')
            ?: ($settings['site_name'] ?? 'Portfolio');

        $description = trim($page['meta_description'] ?? '')
            ?: trim($settings['default_meta_description'] ?? '')
            ?: trim($settings['site_tagline'] ?? '');

        $keywords = trim($page['keywords'] ?? '')
            ?: trim($settings['default_keywords'] ?? '');

        return [
            'title'       => $title,
            'description' => $description !== '' ? $description : null,
            'keywords'    => $keywords !== '' ? $keywords : null,
            'canonical'   => ($page['canonical'] ?? null) ?: (($settings['canonical_base_url'] ?? '') !== '' ? $settings['canonical_base_url'] : null),
            'og_type'     => $page['og_type'] ?? 'website',
            'og_image'    => $page['og_image'] ?? null,
        ];
    }
}