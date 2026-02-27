<?php

namespace App\Controllers;

use App\Core\AdminGuard;
use App\Core\Auth;
use App\Core\Cache;
use App\Core\Csrf;
use App\Core\View;
use App\Models\Category;
use App\Services\SlugService;

final class AdminCategoriesController
{
    private function userId(): int
    {
        return Auth::userId() ?? 0;
    }

    public function index(): void
    {
        AdminGuard::requireLogin();

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;

        $pager = (new Category())->paginateByUser($this->userId(), $page, $perPage);
        
        View::render('admin/categories/index', [
            'categories' => $pager['items'],
            'page'       => $page,
            'perPage'    => $perPage,
            'total'      => $pager['total'],
            'totalPages' => (int)$pager['last_page'],
            'csrf'       => Csrf::token()
        ]);
    }

    public function create(): void
    {
        AdminGuard::requireLogin();

        View::render('admin/categories/form', [
            'mode' => 'create',
            'csrf' => Csrf::token()
        ]);
    }

    public function store(): void
    {
        AdminGuard::requireLogin();
        $this->verifyOrFail();

        $data = $this->validatedCategoryPayload(null);

        (new Category())->insert($data);

        header('Location: /admin/categories');
        exit;
    }

    public function edit(int $id): void
    {
        AdminGuard::requireLogin();

        $category = (new Category())->findByUser($this->userId(), $id);
        if (!$category) {
            http_response_code(404);
            echo 'Category not found';
            return;
        }

        View::render('admin/categories/form', [
            'mode' => 'edit',
            'category' => $category,
            'csrf' => Csrf::token()
        ]);
    }

    public function update(int $id): void
    {
        AdminGuard::requireLogin();
        $this->verifyOrFail();

        $category = (new Category())->findByUser($this->userId(), $id);
        if (!$category) {
            http_response_code(404);
            echo 'Category not found';
            return;
        }

        $data = $this->validatedCategoryPayload($id);

        (new Category())->update($id, $data);

        header('Location: /admin/categories');
        exit;
    }

    public function destroy(int $id): void
    {
        AdminGuard::requireLogin();
        $this->verifyOrFail();

        $category = (new Category())->findByUser($this->userId(), $id);
        if (!$category) {
            http_response_code(404);
            echo 'Category not found';
            return;
        }

        (new Category())->delete($id);

        Cache::clear();

        header('Location: /admin/categories');
        exit;
    }

    private function verifyOrFail()
    {
        if (!Csrf::verify($_POST['_csrf'] ?? null)) 
        {
            http_response_code(419);
            echo 'Invalid CSRF token';
            exit;
        }
    }

    private function validatedCategoryPayload(?int $categoryId = null): array
    {
        $name = trim((string)($_POST['name'] ?? ''));
        $slugInput = trim((string)($_POST['slug'] ?? ''));
        if ($name === '') {
            http_response_code(422);
            echo 'Name is required';
            exit;
        }

        $base = $slugInput !== ''
                ? SlugService::make($slugInput)
                : SlugService::make($name);

        $categoryModel = new Category();

        $slug = $categoryModel->uniqueSlug($this->userId(), $base, $categoryId);

        return [
            'user_id' => $this->userId(),
            'name' => $name,
            'slug' => $slug,
            'meta_title' => trim((string)($_POST['meta_title'] ?? '')),
            'meta_description' => trim((string)($_POST['meta_description'] ?? '')),
            'keywords' => trim((string)($_POST['keywords'] ?? '')),
            'updated_at' => date('Y-m-d H:i:s')
        ];
    }
}