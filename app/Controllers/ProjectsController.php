<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\Project;
use App\Models\Tag;
use App\Models\Setting;
use App\Services\SeoService;
use \App\Services\PortfolioContext;

final class ProjectsController
{
    public function index(): void
    {
        $portfolioContext = new PortfolioContext();

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 6;

        $settings = $portfolioContext->settings;
        $pager = (new Project())->paginatePublishedByUser($portfolioContext->userId, $page, $perPage);

        if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
            header('Content-Type: application/json');
            $totalPages = (int)$pager['totalPages'];
            echo json_encode([
                'items' => $pager['items'],
                'page' => $page,
                'perPage' => $perPage,
                'total' => $pager['total'],
                'totalPages' => $totalPages,
                'hasMore' => $page < $totalPages,
                'nextPage' => $page + 1,
            ]);
            return;
        }

        $projects = $pager['items'];
        $total = $pager['total'];
        $totalPages = (int)$pager['totalPages'];

        $tags = (new Tag())->allByUser($portfolioContext->userId);

        $seo = SeoService::page($settings, [
            // No meta_title here — SeoService will fall back to default_meta_title from settings
            'canonical' => ($settings['canonical_base_url'] ?? '') !== '' ? rtrim($settings['canonical_base_url'], '/') . '/' : null,
        ]);

        View::render('projects/index', compact('projects', 'total', 'seo', 'page', 'perPage', 'totalPages', 'tags'));
    }

    public function show(string $slug): void
    {

        $portfolioContext = new PortfolioContext();

        $settings = $portfolioContext->settings;
        $project = (new Project())->findPublishedBySlug($portfolioContext->userId, $slug);
        if (!$project) {
            http_response_code(404);
            View::render('errors/404', ['seo' => SeoService::page($settings, ['meta_title' => 'Not found'])]);
            return;
        }

        $projectModel = new Project();
        $previousProject = $projectModel->prevPublished($portfolioContext->userId, $project['published_at'], $project['id']);
        $nextProject = $projectModel->nextPublished($portfolioContext->userId, $project['published_at'], $project['id']);

        $seo = SeoService::page($settings, [
            'meta_title'      => $project['meta_title'] ?? $project['title'],
            'meta_description' => $project['meta_description'] ?? $project['summary'] ?? null,
            'keywords'        => $project['keywords'] ?? null,
            'canonical'       => ($settings['canonical_base_url'] ?? null) ? rtrim($settings['canonical_base_url'], '/') . '/project/' . $project['slug'] : null,
            'og_type'         => 'article',
            'og_image'        => !empty($project['image_path'])
                ? rtrim($settings['canonical_base_url'] ?? '', '/') . $project['image_path']
                : null,
        ]);

        $project['tags'] = (new Tag())->forProject($project['id']);

        View::render('projects/show', compact('project', 'seo', 'previousProject', 'nextProject'));
    }
}