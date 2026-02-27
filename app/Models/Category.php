<?php

declare(strict_types=1);

namespace App\Models;

final class Category extends Model
{
    protected string $table = 'categories';

    public function findByUser(int $userId, int $id): ?array
    {
        $row = $this->query(
            "SELECT * FROM {$this->table} WHERE user_id = :user_id AND id = :id AND deleted_at IS NULL LIMIT 1",
            ['user_id' => $userId, 'id' => $id]
        )->fetch();

        return $row ?: null;
    }

    public function projectsForCategory(int $userId, int $categoryId): array
    {
        return $this->query(
            "SELECT p.* FROM projects p
            WHERE p.user_id = :user_id 
            AND p.category_id = :category_id 
            AND p.deleted_at IS NULL
            AND p.published_at IS NOT NULL
            AND p.published_at <= NOW()
            ORDER BY p.is_featured DESC, p.published_at DESC, p.created_at DESC",
            ['user_id' => $userId, 'category_id' => $categoryId]
        )->fetchAll();
    }

    public function findBySlug(int $userId, string $slug)
    {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE user_id = :user_id AND slug = :slug AND deleted_at IS NULL LIMIT 1",
            ['user_id' => $userId, 'slug' => $slug]
        )->fetch();
    }

    public function projectsByCategorySlug(int $userId, string $slug): array
    {
        return $this->query(
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug
            FROM projects p
            JOIN categories c ON p.category_id = c.id
            WHERE p.user_id = :user_id 
            AND c.slug = :slug 
            AND p.deleted_at IS NULL
            AND c.deleted_at IS NULL
            AND p.published_at IS NOT NULL
            AND p.published_at <= NOW()
            ORDER BY p.is_featured DESC, p.published_at DESC, p.created_at DESC",
            ['user_id' => $userId, 'slug' => $slug]
        )->fetchAll();
    }
}