<?php

require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../../lib/MailService.php';


class Auth
{
    private PDO $pdo;
    private string $baseUrl;

    public function __construct()
    {
        $this->pdo = Database::connect();

        $config = require __DIR__ . '/../../config/config.php';

        $this->baseUrl = $config['base_url'];
    }

    public function register(string $username, string $email, string $password): bool
    {
        if ($this->findByEmail($email)) return false;

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(16));

        $stmt = $this->pdo->prepare("
            INSERT INTO users (user_name, email, hashed_password, verification_token)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$username, $email, $hash, $token]);

        $this->sendVerificationEmail($email, $token);
        return true;
    }
    public function sendVerificationEmail(string $email, string $token): void
    {
        $url = "$this->baseUrl/verify?token=$token";
        $body = "Здравствуйте! Подтвердите ваш email, перейдя по ссылке:\n\n$url";

        MailService::send($email, 'Подтверждение email', $body);
    }
    public function setVerificationToken(int $userId, string $token): void
    {
        $stmt = $this->pdo->prepare("UPDATE users SET verification_token = ? WHERE id = ?");
        $stmt->execute([$token, $userId]);
    }


    public function login(string $email, string $password): int|false
    {
        $user = $this->findByEmail($email);
        if (!$user) return false;

        if (!password_verify($password, $user['hashed_password'])) {
            return false;
        }

        if ((int)$user['is_verified'] !== 1) {
            throw new Exception("Email не подтверждён. Пожалуйста, проверьте почту.");
        }

        $this->updateLastLogin((int)$user['id']);
        return (int)$user['id'];
    }

    public function verify(string $token): bool
    {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE verification_token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $stmt = $this->pdo->prepare("
                UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = ?
            ");
            return $stmt->execute([$user['id']]);
        }

        return false;
    }


    private function updateLastLogin(int $userId): void
    {
        $stmt = $this->pdo->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$userId]);
    }

    public function findByEmail(string $email): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function saveApiToken(int $userId, string $token): void
    {
        $stmt = $this->pdo->prepare("UPDATE users SET api_token = ? WHERE id = ?");
        $stmt->execute([$token, $userId]);
    }

    public function findByApiToken(string $token): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE api_token = ?");
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function logout(): void
    {
        session_start();
        session_destroy();
    }
}
