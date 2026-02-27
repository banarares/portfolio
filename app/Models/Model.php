<?php

namespace App\Models;

use App\Core\DB;
use PDO;
use PDOStatement;

abstract class Model
{
    protected string $table;
    protected string $primaryKey = 'id';

    protected function pdo(): PDO
    {
        return DB::connection();
    }

    protected function query(string $sql, array $params = [])
    {
        $stmt = $this->pdo()->prepare($sql);

        foreach ($params as $key => $value) {
            $param = is_int($key) ? $key + 1 : ':' . ltrim((string)$key, ':');

            if (is_int($value)) {
                $stmt->bindValue($param, $value, PDO::PARAM_INT);
            } elseif (is_bool($value)) {
                $stmt->bindValue($param, $value, PDO::PARAM_BOOL);
            } elseif ($value === null) {
                $stmt->bindValue($param, null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue($param, $value, PDO::PARAM_STR);
            }
        }

        $stmt->execute();
        return $stmt;
    }

    public function find(int $id)
    {
        $row = $this->query(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1",
            ['id' => $id]
        )->fetch();

        return $row ?: null;
    }

    public function insert(array $data): int
    {
        if (empty($data)) {
            throw new \InvalidArgumentException('Data array cannot be empty');
        }

        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ":$col", $columns);

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $this->query($sql, $data);

        return (int)$this->pdo()->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        if (empty($data)) {
            throw new \InvalidArgumentException('Data array cannot be empty');
        }

        $setClauses = [];
        foreach ($data as $column => $value) {
            $setClauses[] = "$column = :$column";
        }

        $sql = sprintf(
            "UPDATE %s SET %s WHERE %s = :id",
            $this->table,
            implode(', ', $setClauses),
            $this->primaryKey
        );

        $params = array_merge($data, ['id' => $id]);

        return $this->query($sql, $params)->rowCount() > 0;
    }

    public function updateWhere(array $where, array $data): bool
    {
        if (empty($data) || empty($where)) {
            throw new \InvalidArgumentException('Data and where arrays cannot be empty');
        }

        $set = implode(', ', array_map(fn($col) => "$col = :set_$col", array_keys($data)));
        $whr = implode(' AND ', array_map(fn($col) => "$col = :whr_$col", array_keys($where)));

        $params = [];
        foreach ($data  as $k => $v) $params["set_$k"] = $v;
        foreach ($where as $k => $v) $params["whr_$k"] = $v;

        return $this->query(
            "UPDATE {$this->table} SET $set WHERE $whr",
            $params
        )->rowCount() > 0;
    }

    // Soft delete method
    public function delete(int $id, bool $soft = true): bool
    {
        if ($soft) {
            $sql = sprintf(
                "UPDATE %s SET deleted_at = NOW() WHERE %s = :id",
                $this->table,
                $this->primaryKey
            );
        } else {
            $sql = sprintf(
                "DELETE FROM %s WHERE %s = :id",
                $this->table,
                $this->primaryKey
            );
        }

        return $this->query($sql, ['id' => $id])->rowCount() > 0;
    }


    public function slugExists(int $userId, string $slug, ?int $excludeId = null): bool
    {
        $query = "SELECT COUNT(*) FROM {$this->table}
                WHERE user_id = :user_id
                AND slug = :slug
                AND deleted_at IS NULL";
        $params = ['user_id' => $userId, 'slug' => $slug];

        if ($excludeId !== null) {
            $query .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        return $this->query($query . ' LIMIT 1', $params)->fetchColumn() > 0;
    }

    public function uniqueSlug(int $userId, string $baseSlug, ?int $excludeId = null): string
    {
        $slug = $baseSlug;
        $counter = 1;

        while ($this->slugExists($userId, $slug, $excludeId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function allByUser(int $userId, string $orderBy = 'id', string $direction = 'DESC'): array
    {
        $orderBy = preg_replace('/[^a-zA-Z0-9_]/', '', $orderBy);
        $direction = strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC';

        return $this->query(
            "SELECT * FROM {$this->table} 
            WHERE user_id = :user_id 
            AND deleted_at IS NULL 
            ORDER BY $orderBy $direction",
            ['user_id' => $userId]
        )->fetchAll();
    }

    public function countByUser(int $userId): int
    {
        return (int)$this->query(
            "SELECT COUNT(*) FROM {$this->table} 
            WHERE user_id = :user_id 
            AND deleted_at IS NULL",
            ['user_id' => $userId]
        )->fetchColumn();
    }

    public function paginateByUser(int $userId, int $page, int $perPage, string $orderBy = 'id', string $direction = 'DESC'): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));

        $orderBy = preg_replace('/[^a-zA-Z0-9_]/', '', $orderBy);
        $direction = strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC';

        $offset = ($page - 1) * $perPage;

        $items = $this->query(
            "SELECT * FROM {$this->table} 
            WHERE user_id = :user_id 
            AND deleted_at IS NULL 
            ORDER BY $orderBy $direction 
            LIMIT :limit OFFSET :offset",
            ['user_id' => $userId, 'limit' => $perPage, 'offset' => $offset]
        )->fetchAll();

        $total = $this->countByUser($userId);

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'last_page' => ceil($total / $perPage)
        ];
    }
}