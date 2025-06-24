<?php
class Database {
    private static $pdo;

    public static function connect() {
        if (!self::$pdo) {
            $cfg = require __DIR__ . '/../../config/config.php';
            $db = $cfg['db'];
            $dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset=utf8";
            self::$pdo = new PDO($dsn, $db['user'], $db['password']);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$pdo;
    }
}
