<?php

namespace App\Models;

final class User extends Model
{
    protected string $table = 'users';

    public function findByEmail(string $email): ?array
    {
        $row = $this->query(
            "SELECT * FROM {$this->table} 
            WHERE email = :email 
            AND deleted_at IS NULL 
            LIMIT 1",
            ['email' => $email]
        )->fetch();

        return $row ?: null;
    }
}