<?php

require_once __DIR__ . '/../Core/Database.php';

class Auth
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function register(string $username, string $email, string $password): bool
    {
        if ($this->findByUsername($username) || $this->findByEmail($email)) {
            return false;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare("
            INSERT INTO users (user_name, email, hashed_password) 
            VALUES (?, ?, ?)
        ");

        return $stmt->execute([$username, $email, $hash]);
    }

    public function login(string $email, string $password): int|false
    {
        $user = $this->findByEmail($email);
        if (!$user) return false;

        if (password_verify($password, $user['hashed_password'])) {
            $this->updateLastLogin((int)$user['id']);
            return (int)$user['id'];
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

    public function findByUsername(string $username): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE user_name = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function isAuthenticated(): bool
    {
        session_start();
        return isset($_SESSION['user_id']);
    }

    public function logout(): void
    {
        session_start();
        session_destroy();
    }
}
