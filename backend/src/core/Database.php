<?php

namespace Core;

class Database
{
    private static $host;
    private static $dbname;
    private static $username;
    private static $password;
    private static $pdo = null;

    public static function connect()
    {
        self::$host = getenv('DB_HOST');
        self::$dbname = getenv('DB_NAME');
        self::$username = getenv('DB_USERNAME');
        self::$password = getenv('DB_PASSWORD');

        if (self::$pdo === null) {
            try {
                self::$pdo = new \PDO("mysql:host=" . self::$host . ";dbname=" . self::$dbname . ";charset=utf8", self::$username, self::$password, [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,  // Enable exceptions
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC  // Fetch as associative array
                ]);
            } catch (\PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
