<?php

namespace App\Controllers;

use App\Core\AdminGuard;
use App\Core\Auth;
use App\Core\Cache;
use App\Core\Csrf;
use App\Core\View;
use App\Models\Project;
use App\Models\Category;
use App\Models\Tag;
use App\Services\SlugService;
use App\Services\ImageService;

final class AdminProjectsController
{
    private function userId(): int
    {
        return Auth::userId() ?? 0;
    }

    public function index(): void
    {
        AdminGuard::requireLogin();

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;

        $pager = (new Project())->paginateAdminByUser($this->userId(), $page, $perPage);
        
        View::render('admin/projects/index', [
            'projects'   => $pager['items'],
            'page'       => $page,
            'perPage'    => $perPage,
            'total'      => $pager['total'],
            'totalPages' => (int)$pager['totalPages'],
            'csrf'       => Csrf::token()
        ]);
    }

    public function create(): void
    {
        AdminGuard::requireLogin();

        $categories = (new Category())->allByUser($this->userId());
        $tags = (new Tag())->allByUser($this->userId());

        View::render('admin/projects/form', [
            'mode'              => 'create',
            'project'           => null,
            'categories'        => $categories,
            'tags'              => $tags,
            'selectedTagIds'    => [],
            'csrf'              => Csrf::token()
        ]);
    }

    public function store(): void
    {
        AdminGuard::requireLogin();
        $this->verifyOrFail();

        $data = $this->validatedProjectPayload(null, null);
        $data['created_at'] = date('Y-m-d H:i:s');
        $imagePath = $this->handleImageUpload();
        if ($imagePath !== null) {
            $data['image_path'] = $imagePath;
        }

        $projectId = (new Project())->insert($data);
        (new Project())->syncTags($projectId, $_POST['tag_ids'] ?? []);

        header('Location: /admin/projects');
        exit;
    }

    public function edit(int $id): void
    {
        AdminGuard::requireLogin();

        $project = (new Project())->findById($this->userId(), $id);
        $categories = (new Category())->allByUser($this->userId());
        $tags = (new Tag())->allByUser($this->userId());
        $selectedTagIds = (new Project())->tagIdsForProject($id);

        View::render('admin/projects/form', [
            'mode'              => 'edit',
            'project'           => $project,
            'categories'        => $categories,
            'tags'              => $tags,
            'selectedTagIds'    => $selectedTagIds,
            'csrf'              => Csrf::token()
        ]);
    }

    public function update(int $id): void
    {
        AdminGuard::requireLogin();
        $this->verifyOrFail();

        $project = (new Project())->findById($this->userId(), $id);
        $this->verifyProject($project);

        $data = $this->validatedProjectPayload($id, $project['published_at'] ?? null);
        $imagePath = $this->handleImageUpload();
        if ($imagePath !== null) {
            $data['image_path'] = $imagePath;
        }

        (new Project())->update($id, $data);
        (new Project())->syncTags($id, $_POST['tag_ids'] ?? []);

        header('Location: /admin/projects');
        exit;
    }

    public function destroy(int $id): void
    {
        AdminGuard::requireLogin();
        $this->verifyOrFail();

        $project = (new Project())->findById($this->userId(), $id);
        $this->verifyProject($project);

        (new Project())->delete($id, true);

        Cache::clear();

        header('Location: /admin/projects');
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

    private function verifyProject($project): void
    {
        if (!$project) {
            http_response_code(404);
            echo 'Project not found';
            exit;
        }
    }

    private function validatedProjectPayload(?int $projectId = null, ?string $existingPublishedAt = null): array
    {
        $title = trim((string)($_POST['title'] ?? ''));
        $slugInput = trim((string)($_POST['slug'] ?? ''));
        $categoryId = (int)($_POST['category_id'] ?? 0);

        if ($title === '') {
            http_response_code(422);
            echo 'Title is required';
            exit;
        }

        $base = $slugInput !== ''
                ? SlugService::make($slugInput)
                : SlugService::make($title);

        $projectModel = new Project();

        $slug = $projectModel->uniqueSlug($this->userId(), $base, $projectId);

        $isPublished =isset($_POST['is_published']);
        $publishedAt = $isPublished
            ? ($existingPublishedAt ?: date('Y-m-d H:i:s'))
            : null;

        return [
            'user_id'           => $this->userId(),
            'category_id'       => $categoryId ?: null,
            'title'             => $title,
            'slug'              => $slug,
            'summary'           => trim((string)($_POST['summary'] ?? '')),
            'description'       => trim((string)($_POST['description'] ?? '')),
            'live_url'          => trim((string)($_POST['live_url'] ?? '')),
            'repo_url'          => trim((string)($_POST['repo_url'] ?? '')),
            'meta_title'        => trim((string)($_POST['meta_title'] ?? '')),
            'meta_description'  => trim((string)($_POST['meta_description'] ?? '')),
            'keywords'          => trim((string)($_POST['keywords'] ?? '')),
            'is_featured'       => isset($_POST['is_featured']) ? 1 : 0,
            'published_at'      => $publishedAt,
            'updated_at'        => date('Y-m-d H:i:s')
        ];
    }

    private function handleImageUpload(): ?string
    {
        if (!isset($_FILES['image']) || ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(422);
            echo 'Error uploading image';
            exit;
        }

        $tmp = $_FILES['image']['tmp_name'];
        $size = $_FILES['image']['size'];

        if ($size > 5 * 1024 * 1024) {
            http_response_code(422);
            echo 'Image size must be less than 5MB';
            exit;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($tmp);

        $allowedType = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];

        if (!isset($allowedType[$mimeType])) {
            http_response_code(422);
            echo 'Invalid image type. Only JPG, PNG, and WEBP are allowed.';
            exit;
        }

        $root = dirname(__DIR__, 2);
        $uploadDir = $root . '/public/uploads/projects';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $filename = 'p_' . date('YmdHis') . '_' . bin2hex(random_bytes(5)) . '.webp';
        $targetPath = $uploadDir . '/' . $filename;

        try {
            ImageService::toWebpAndResize($tmp, $mimeType, $targetPath, 1600, 85);
        } catch (\Exception $e) {
            http_response_code(500);
            echo 'Error processing image: ' . $e->getMessage();
            exit;
        }

        return '/uploads/projects/' . $filename;
    }
}