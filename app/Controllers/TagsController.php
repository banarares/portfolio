<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Tag;
use App\Models\Setting;
use App\Services\SeoService;
use \App\Services\PortfolioContext;

final class TagsController
{    
    public function show(string $slug): void
    {
        $portfolioContext = new PortfolioContext();

        $settings = $portfolioContext->settings;

        $tag = (new Tag())->findBySlug($portfolioContext->userId, $slug);
        if (!$tag) {
            http_response_code(404);
            View::render('errors/404', ['seo' => SeoService::page($settings, ['meta_title' => 'Not found'])]);
            return;
        }

        $projects = (new Tag())->projectsByTagSlug($portfolioContext->userId, $slug);

        $seo = SeoService::page($settings, [
            'meta_title' => ($tag['meta_title'] ?? null) ?: ('Tag: ' . $tag['name']),
            'meta_description' => $tag['meta_description'] ?? null,            
            'canonical' => ($settings['canonical_base_url'] ?? null) ? rtrim($settings['canonical_base_url'], '/') . '/tag/' . $tag['slug'] : null,
        ]);

        View::render('tags/show', compact('tag', 'projects', 'seo'));
    }
}