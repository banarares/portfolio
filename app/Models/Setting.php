<?php

declare(strict_types=1);

namespace App\Models;

final class Setting extends Model
{
    protected string $table = 'settings';

    public function firstByUser(int $userId): ?array
    {
        $row = $this->query(
            "SELECT * FROM {$this->table} WHERE user_id = :user_id LIMIT 1",
            ['user_id' => $userId]
        );

        return $row->fetch() ?: null;
    }

    /**
     * Insert or update the settings row for a user.
     */
    public function upsert(int $userId, array $data): void
    {
        $existing = $this->firstByUser($userId);

        if ($existing) {
            $this->updateWhere(['user_id' => $userId], $data);
        } else {
            $this->insert(array_merge($data, [
                'user_id'    => $userId,
                'created_at' => date('Y-m-d H:i:s'),
            ]));
        }
    }
}