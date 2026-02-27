<?php

namespace App\Core;

use PDO;

final class DB
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO 
    {
        if (self::$pdo === null) {
            
            try {
                if (self::$pdo) return self::$pdo;

                $host = Config::get('DB_HOST', 'localhost');
                $port = Config::get('DB_PORT', 3306);
                $dbname = Config::get('DB_NAME', 'test');
                $user = Config::get('DB_USER', 'root');
                $password = Config::get('DB_PASSWORD', '');
                $charset = Config::get('DB_CHARSET', 'utf8mb4');

                $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";

                self::$pdo = new PDO($dsn, $user, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);

                return self::$pdo;                
            } catch (\PDOException $e) {
                if (Config::bool('APP_DEBUG', false)) {
                    die('Database connection failed: ' . $e->getMessage());
                }
                die('Service unavailable. Please try again later.');
            }
        }

        return self::$pdo;
    }
}