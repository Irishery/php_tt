<?php
function getGeoCoords(string $ip): ?array
{
    // Зарезервированные или приватные IP
    if (
        $ip === '127.0.0.1' ||
        $ip === '::1' ||
        str_starts_with($ip, '192.168.') ||
        str_starts_with($ip, '10.') ||
        preg_match('/^172\.(1[6-9]|2[0-9]|3[0-1])\./', $ip)
    ) {
        return null;
    }

    $json = @file_get_contents("https://ipapi.co/{$ip}/json");
    $data = json_decode($json, true);

    if (!empty($data['reserved']) || !empty($data['error'])) {
        return null;
    }

    if (isset($data['latitude'], $data['longitude'])) {
        return ['lat' => $data['latitude'], 'lon' => $data['longitude']];
    }

    return null;
}


function getClientIp(): string
{
    $headers = [
        'HTTP_X_FORWARDED_FOR',
        'HTTP_CLIENT_IP',
        'HTTP_X_REAL_IP',
        'HTTP_CF_CONNECTING_IP',  // Cloudflare
    ];

    foreach ($headers as $key) {
        if (!empty($_SERVER[$key])) {
            $ipList = explode(',', $_SERVER[$key]); // могут быть несколько IP через запятую
            foreach ($ipList as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
    }

    // fallback
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}
