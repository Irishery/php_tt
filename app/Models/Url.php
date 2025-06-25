<?php
class Url
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function save($original, $short)
    {
        $stmt = $this->pdo->prepare("INSERT INTO urls (original_url, short_code) VALUES (?, ?)");
        $stmt->execute([$original, $short]);
    }

    public function findByCode($code)
    {
        $stmt = $this->pdo->prepare("SELECT original_url FROM urls WHERE short_code = ?");
        $stmt->execute([$code]);
        return $stmt->fetchColumn();
    }

    public function findFullByCode(string $code): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM urls WHERE short_code = ?");
        $stmt->execute([$code]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
