<?php

require_once __DIR__ . '/../Models/Auth.php';

class AuthController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $config = require __DIR__ . '/../config.php';
        $this->baseUrl = rtrim($config['base_url'], '/');
    }

    public function showLogin()
    {
        $this->view('auth/login');
    }

    public function login()
    {
        session_start();

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $auth = new Auth();
        $userId = $auth->login($email, $password);

        if ($userId !== false) {
            $_SESSION['user_id'] = $userId;
            header("Location: {$this->baseUrl}/dashboard");
        } else {
            $this->view('auth/login', ['error' => 'Неверный email или пароль']);
        }
    }

    public function showRegister()
    {
        $this->view('auth/register');
    }

    public function register()
    {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $auth = new Auth();
        if ($auth->register($username, $email, $password)) {
            header("Location: /login");
        } else {
            $this->view('auth/register', ['error' => 'Пользователь уже существует']);
        }
    }

    public function logout()
    {
        $auth = new Auth();
        $auth->logout();
        header("Location: /login");
    }
}
