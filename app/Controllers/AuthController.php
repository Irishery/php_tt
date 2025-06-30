<?php

require_once __DIR__ . '/../Models/Auth.php';

class AuthController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $config = require __DIR__ . '/../../config/config.php';
        $this->baseUrl = $config['base_url'];
    }

    public function showLogin()
    {
        $this->respondView('auth/login');
    }

    public function login()
    {
        session_start();

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $auth = new Auth();
        $user = $auth->findByEmail($email);

        if (!$user || !password_verify($password, $user['hashed_password'])) {
            return $this->respondView('auth/login', ['error' => 'Неверный email или пароль']);
        }

        if ((int)$user['is_verified'] !== 1) {
            return $this->respondView('auth/login', [
                'error' => 'Email не подтверждён. Пожалуйста, проверьте вашу почту.',
                'resend_email' => $user['email']
            ]);
        }

        $_SESSION['user_id'] = (int)$user['id'];
        header("Location: /analytics");
        exit;
    }

    public function apiLogin()
    {
        if (
            $_SERVER['REQUEST_METHOD'] !== 'POST' ||
            strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') === false
        ) {
            return $this->respondError('Bad request', 400);
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['email']) || empty($input['password'])) {
            return $this->respondError('Email и пароль обязательны', 400);
        }

        $auth = new Auth();

        try {
            $userId = $auth->login($input['email'], $input['password']);
        } catch (Exception $e) {
            return $this->respondError($e->getMessage(), 401);
        }

        if ($userId === false) {
            return $this->respondError('Неверный email или пароль', 401);
        }

        $token = bin2hex(random_bytes(32));
        $auth->saveApiToken($userId, $token);

        $this->respondJson([
            'token' => $token,
            'user_id' => $userId,
        ]);
    }

    public function showRegister()
    {
        $this->respondView('auth/register');
    }

    public function register()
    {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $auth = new Auth();
        if ($auth->register($username, $email, $password)) {
            header("Location: /login");
            exit;
        } else {
            $this->respondView('auth/register', ['error' => 'Пользователь уже существует']);
        }
    }

    public function verify()
    {
        $token = $_GET['token'] ?? '';
        $auth = new Auth();

        if ($auth->verify($token)) {
            $this->respondView('auth/verify', [
                'success' => 'Email успешно подтверждён. Теперь вы можете войти.',
                'title' => 'Подтверждение Email'
            ]);
        } else {
            $this->respondView('auth/verify', [
                'error' => 'Неверная или устаревшая ссылка подтверждения.',
                'title' => 'Подтверждение Email'
            ]);
        }
    }

    public function resend()
    {
        $email = $_POST['email'] ?? '';
        $auth = new Auth();

        $user = $auth->findByEmail($email);
        if (!$user || (int)$user['is_verified'] === 1) {
            return $this->respondView('auth/login', ['error' => 'Email уже подтверждён или пользователь не найден']);
        }

        $token = bin2hex(random_bytes(16));
        $auth->setVerificationToken($user['id'], $token);
        $auth->sendVerificationEmail($email, $token);

        $this->respondView('auth/login', ['success' => 'Письмо с подтверждением отправлено повторно']);
    }

    public function logout()
    {
        $auth = new Auth();
        $auth->logout();
        header("Location: /login");
        exit;
    }
}
