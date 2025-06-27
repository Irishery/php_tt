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
    public function logClick(int $urlId, string $ip): bool
    {
        $country = $this->resolveCountry($ip);

        $stmt = $this->pdo->prepare("
        INSERT INTO url_analytics (url_id, ip_address, country)
        VALUES (?, ?, ?)
    ");
        return $stmt->execute([$urlId, $ip, $country]);
    }

    private function resolveCountry(string $ip): ?string
    {
        if (
            $ip === '127.0.0.1' ||
            $ip === '::1' ||
            str_starts_with($ip, '192.168.') ||
            str_starts_with($ip, '10.') ||
            preg_match('/^172\.(1[6-9]|2[0-9]|3[0-1])\./', $ip)
        ) {
            return 'Local';
        }

        // Переход на другой сервис, напр. ip-api.com
        $json = @file_get_contents("http://ip-api.com/json/{$ip}");
        $data = json_decode($json, true);

        if (!$data || $data['status'] !== 'success') {
            return null;
        }

        return $data['country'] ?? null;
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
