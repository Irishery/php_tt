<?php
if (!function_exists('parseEnv')) {
    function parseEnv(string $path): array
    {
        if (!file_exists($path)) return [];
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $env = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || strpos($line, '#') === 0) continue;
            [$key, $value] = array_map('trim', explode('=', $line, 2));
            $value = trim($value, "\"'");
            $env[$key] = $value;
        }
        return $env;
    }
}

$env = $_ENV;

if (empty($env['DB_HOST'])) {
    $env = parseEnv(__DIR__ . '/../.env');
}

return [
    'db' => [
        'host' => $env['DB_HOST'] ?? '',
        'dbname' => $env['DB_NAME'] ?? '',
        'user' => $env['DB_USER'] ?? '',
        'password' => $env['DB_PASS'] ?? '',
        'port' => $env['DB_PORT'] ?? 3306,
        'charset' => $env['DB_CHARSET'] ?? '',
    ],
    'base_url' => rtrim($env['BASE_URL'] ?? 'http://localhost', '/'),
    'mail' => [
        'smtp_host' => $env['SMTP_HOST'] ?? '',
        'smtp_port' => $env['SMTP_PORT'] ?? 465,
        'smtp_user' => $env['SMTP_USER'] ?? '',
        'smtp_pass' => $env['SMTP_PASS'] ?? '',
        'from_email' => $env['MAIL_FROM'] ?? '',
        'from_name' => $env['MAIL_NAME'] ?? '',
    ],
];
