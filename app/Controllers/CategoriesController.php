<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\View;
use App\Models\Category;
use App\Models\Setting;
use App\Services\SeoService;
use \App\Services\PortfolioContext;

final class CategoriesController
{
    public function show(string $slug): void
    {
        $portfolioContext = new PortfolioContext();

        $settings = $portfolioContext->settings;
        $category = (new Category())->findBySlug($portfolioContext->userId, $slug);
        if (!$category) {
            http_response_code(404);
            View::render('errors/404', ['seo' => SeoService::page($settings, ['meta_title' => 'Not found'])]);
            return;
        }

        $projects = (new Category())->projectsByCategorySlug($portfolioContext->userId, $slug);

        $seo = SeoService::page($settings, [
            'meta_title' => $category['meta_title'] ?? $category['name'],
            'meta_description' => $category['meta_description'] ?? null,
            'keywords' => $category['keywords'] ?? null,
            'canonical' => ($settings['canonical_base_url'] ?? null) ? rtrim($settings['canonical_base_url'], '/') . '/category/' . $category['slug'] : null,
        ]);

        View::render('categories/show', compact('category', 'projects', 'seo'));
    }
}