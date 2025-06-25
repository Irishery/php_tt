<?php

require_once __DIR__ . '/../Core/Database.php';

class Analytics
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    // 🔄 Лог клика: сохраняем url_id, ip, страну
    public function logClick(int $urlId, string $ip, ?string $country = null): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO url_analytics (url_id, ip_address, country) 
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([$urlId, $ip, $country]);
    }

    // 📊 Количество переходов по url_id
    public function getClickCount(int $urlId): int
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM url_analytics WHERE url_id = ?
        ");
        $stmt->execute([$urlId]);
        return (int)$stmt->fetchColumn();
    }

    // 📋 Подробная статистика по ссылке
    public function getClicks(int $urlId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT ip_address, country, redirected_at 
            FROM url_analytics 
            WHERE url_id = ? 
            ORDER BY redirected_at DESC
        ");
        $stmt->execute([$urlId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
