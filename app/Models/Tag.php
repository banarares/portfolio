<?php

declare(strict_types=1);

namespace App\Models;

final class Tag extends Model
{
    protected string $table = 'tags';

    public function findByUser(int $userId, int $id): ?array
    {
        $row = $this->query(
            "SELECT * FROM {$this->table} WHERE user_id = :user_id AND id = :id AND deleted_at IS NULL LIMIT 1",
            ['user_id' => $userId, 'id' => $id]
        )->fetch();

        return $row ?: null;
    }

    public function forProject(int $projectId): array
    {
        return $this->query(
            "SELECT t.* 
            FROM {$this->table} t 
            JOIN projects_tags pt ON t.id = pt.tag_id 
            WHERE pt.project_id = :project_id
            ORDER BY t.name ASC",
            ['project_id' => $projectId]
        )->fetchAll();
    }

    public function findBySlug(int $userId, string $slug): ?array
    {
        return $this->query(
            "SELECT * FROM {$this->table}
            WHERE user_id = :user_id
            AND slug = :slug
            AND deleted_at IS NULL
            LIMIT 1",
            ['user_id' => $userId, 'slug' => $slug]
        )->fetch();
    }

    public function projectsForTag(int $userId, int $tagId): array
    {
        return $this->query(
            "SELECT p.*
            FROM projects p
            JOIN projects_tags pt ON p.id = pt.project_id
            WHERE pt.tag_id = :tag_id
            AND p.user_id = :user_id
            AND p.deleted_at IS NULL
            AND p.published_at IS NOT NULL
            AND p.published_at <= NOW()
            ORDER BY p.is_featured DESC, p.published_at DESC, p.created_at DESC",
            ['tag_id' => $tagId, 'user_id' => $userId]
        )->fetchAll();
    }

    public function projectsByTagSlug(int $userId, string $slug): array
    {
        return $this->query(
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug
            FROM projects p
            LEFT JOIN categories c ON p.category_id = c.id
            JOIN projects_tags pt ON p.id = pt.project_id
            JOIN tags t ON pt.tag_id = t.id
            WHERE t.slug = :slug 
            AND p.user_id = :user_id1 
            AND t.user_id = :user_id2
            AND p.deleted_at IS NULL 
            AND t.deleted_at IS NULL
            AND p.published_at IS NOT NULL
            AND p.published_at <= NOW()
            ORDER BY p.is_featured DESC, p.published_at DESC, p.created_at DESC",
            ['slug' => $slug, 'user_id1' => $userId, 'user_id2' => $userId]
        )->fetchAll();
    }
}