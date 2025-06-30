<?php
class Url
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function save($original, $short, $user_id)
    {
        $stmt = $this->pdo->prepare("INSERT INTO urls (original_url, short_code, created_at, user_id) VALUES (?, ?, CURRENT_TIMESTAMP, ?)");
        $stmt->execute([$original, $short, $user_id]);
    }

    public function findFullByCode(string $code): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM urls WHERE short_code = ?");
        $stmt->execute([$code]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllByUser(int $userId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM urls WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
