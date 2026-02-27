<?php

namespace App\Controllers;

use App\Core\AdminGuard;
use App\Core\Auth;
use App\Core\Cache;
use App\Core\Csrf;
use App\Core\View;
use App\Models\Tag;
use App\Services\SlugService;

final class AdminTagsController
{
    private function userId(): int
    {
        return Auth::userId() ?? 0;
    }

    public function index(): void
    {
        AdminGuard::requireLogin();

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 30;

        $pager = (new Tag())->paginateByUser($this->userId(), $page, $perPage);
        
        View::render('admin/tags/index', [
            'tags'       => $pager['items'],
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

        View::render('admin/tags/form', [
            'mode' => 'create',
            'tag'  => null,
            'csrf' => Csrf::token()
        ]);
    }

    public function store(): void
    {
        AdminGuard::requireLogin();
        $this->verifyOrFail();

        $data = $this->validatedTagPayload();

        (new Tag())->insert($data);

        header('Location: /admin/tags');
        exit;
    }

    public function edit(int $id): void
    {
        AdminGuard::requireLogin();

        $tag = (new Tag())->findByUser($this->userId(), $id);
        if (!$tag) {
            http_response_code(404);
            echo 'Tag not found';
            return;
        }

        View::render('admin/tags/form', [
            'mode' => 'edit',
            'tag' => $tag,
            'csrf' => Csrf::token()
        ]);
    }

    public function update(int $id): void
    {
        AdminGuard::requireLogin();
        $this->verifyOrFail();

        $tag = (new Tag())->findByUser($this->userId(), $id);
        if (!$tag) {
            http_response_code(404);
            echo 'Tag not found';
            return;
        }

        $data = $this->validatedTagPayload($id);

        (new Tag())->update($id, $data);

        header('Location: /admin/tags');
        exit;
    }

    public function destroy(int $id): void
    {
        AdminGuard::requireLogin();
        $this->verifyOrFail();

        $tag = (new Tag())->findByUser($this->userId(), $id);
        if (!$tag) {
            http_response_code(404);
            echo 'Tag not found';
            return;
        }

        (new Tag())->delete($id);

        Cache::clear();

        header('Location: /admin/tags');
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

    private function validatedTagPayload(?int $tagId = null): array
    {
        $name = trim((string)($_POST['name'] ?? ''));
        $slugInput = trim((string)($_POST['slug'] ?? ''));

        if ($name === '') {
            http_response_code(422);
            echo 'Name is required';
            exit;
        }

        $baseSlug = $slugInput !== ''
                    ? SlugService::make($slugInput)
                    : SlugService::make($name);

        $model = new Tag();

        $slug = $model->uniqueSlug($this->userId(), $baseSlug, $tagId);

        return [
            'user_id' => $this->userId(),
            'name' => $name,
            'slug' => $slug,
            'updated_at' => date('Y-m-d H:i:s')
        ];
    }
}