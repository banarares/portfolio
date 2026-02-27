<?php

namespace App\Models;


class Project extends Model
{
    protected string $table = 'projects';

    public function allPublishedByUser(int $userId):array
    {
        return $this->query(
            "SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.user_id = :user_id 
                AND p.deleted_at IS NULL
                AND p.published_at IS NOT NULL
                AND p.published_at <= NOW()
                ORDER BY p.is_featured DESC, p.published_at DESC, p.created_at DESC",
            ['user_id' => $userId]
        )
        ->fetchAll();
    }

    public function findPublishedBySlug(int $userId, string $slug)
    {
        return $this->query(
            "SELECT p.*, c.name as category_name, c.slug as category_slug 
            FROM {$this->table} p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.user_id = :user_id
            AND p.slug = :slug
            AND p.deleted_at IS NULL
            AND p.published_at IS NOT NULL
            LIMIT 1", 
            ['user_id' => $userId, 'slug' => $slug]
        )
        ->fetch();
    }

    public function paginatePublishedByUser(int $userId, int $page, int $perPage): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        $offset = ($page - 1) * $perPage;

        $items = $this->query(
            "SELECT p.*, c.name as category_name, c.slug as category_slug 
            FROM {$this->table} p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.user_id = :user_id 
            AND p.deleted_at IS NULL 
            AND p.published_at IS NOT NULL 
            AND p.published_at <= NOW()
            ORDER BY p.is_featured DESC, p.published_at DESC, p.created_at DESC
            LIMIT :perPage OFFSET :offset",
            ['user_id' => $userId, 'perPage' => $perPage, 'offset' => $offset]
        )->fetchAll();

        $total = (int)$this->query(
            "SELECT COUNT(*) FROM {$this->table} 
            WHERE user_id = :user_id 
            AND deleted_at IS NULL 
            AND published_at IS NOT NULL 
            AND published_at <= NOW()",
            ['user_id' => $userId]
        )->fetchColumn();

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage)
        ];
    }

    public function prevPublished(int $userId, string $publishedAt, int $currentId)
    {
        return $this->query(
            "SELECT p.*, c.name as category_name, c.slug as category_slug 
            FROM {$this->table} p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.user_id = :user_id 
            AND p.deleted_at IS NULL 
            AND p.published_at IS NOT NULL 
            AND (p.published_at < :published_at1 OR (p.published_at = :published_at AND p.id < :current_id))
            ORDER BY p.published_at DESC, p.id DESC
            LIMIT 1",
            ['user_id' => $userId, 'published_at1' => $publishedAt, 'published_at' => $publishedAt, 'current_id' => $currentId]
        )->fetch();
    }

    public function nextPublished(int $userId, string $publishedAt, int $currentId)
    {
        return $this->query(
            "SELECT p.*, c.name as category_name, c.slug as category_slug 
            FROM {$this->table} p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.user_id = :user_id 
            AND p.deleted_at IS NULL 
            AND p.published_at IS NOT NULL 
            AND (p.published_at > :published_at1 OR (p.published_at = :published_at AND p.id > :current_id))
            ORDER BY p.published_at ASC, p.id ASC
            LIMIT 1",
            ['user_id' => $userId, 'published_at1' => $publishedAt, 'published_at' => $publishedAt, 'current_id' => $currentId]
        )->fetch();
    }

    public function findBySlug(int $userId, string $slug)
    {
        return $this->query(
            "SELECT p.*, c.name as category_name, c.slug as category_slug 
            FROM {$this->table} p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.user_id = :user_id
            AND p.slug = :slug 
            AND p.deleted_at IS NULL 
            LIMIT 1", 
            ['user_id' => $userId, 'slug' => $slug]
            )
            ->fetch();
    }

    public function findById(int $userId, int $id): ?array
    {
        return $this->query(
            "SELECT * FROM {$this->table} 
            WHERE user_id = :user_id 
            AND id = :id 
            AND deleted_at IS NULL 
            LIMIT 1",
            ['user_id' => $userId, 'id' => $id]
        )->fetch();
    }

    public function tagsForProject(int $projectId): array
    {
        return (new Tag())->forProject($projectId);
    }

    public function tagIdsForProject(int $projectId): array
    {
        $rows = $this->query(
            "SELECT tag_id FROM projects_tags WHERE project_id = :project_id",
            ['project_id' => $projectId]
        )->fetchAll();

        return array_column($rows, 'tag_id');
    }

    public function syncTags(int $projectId, array $tagIds): void
    {
        $tagIds = array_map('intval', $tagIds);
        $pdo = $this->pdo();
        $pdo->beginTransaction();

        try {
            // Delete existing tags
            $pdo->prepare("DELETE FROM projects_tags WHERE project_id = :project_id")
                ->execute(['project_id' => $projectId]);

            // Insert new tags
            if (!empty($tagIds)) {
                $placeholders = implode(', ', array_fill(0, count($tagIds), '(?, ?)'));
                $sql = "INSERT INTO projects_tags (project_id, tag_id) VALUES $placeholders";
                $stmt = $pdo->prepare($sql);

                $params = [];
                foreach ($tagIds as $tagId) {
                    $params[] = $projectId;
                    $params[] = $tagId;
                }

                $stmt->execute($params);
            }

            $pdo->commit();
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function countAdminByUser(int $userId): int
    {
        $row = $this->query(
            "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = :user_id AND deleted_at IS NULL",
            ['user_id' => $userId]
        )->fetchColumn();

        return (int)$row;
    }

    public function paginateAdminByUser(int $userId, int $page, int $perPage): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        $offset = ($page - 1) * $perPage;

        $items = $this->query(
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug
            FROM {$this->table} p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.user_id = :user_id 
            AND p.deleted_at IS NULL 
            ORDER BY p.updated_at DESC, p.id DESC 
            LIMIT :perPage OFFSET :offset",
            ['user_id' => $userId, 'perPage' => $perPage, 'offset' => $offset]
        )->fetchAll();

        $total = $this->countAdminByUser($userId);

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage)
        ];
    }
}
