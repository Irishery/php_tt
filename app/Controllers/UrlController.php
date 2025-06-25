<?php

require_once __DIR__ . '/../Models/Url.php';
require_once __DIR__ . '/../Models/Analytics.php';

class UrlController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $config = require __DIR__ . '/../config.php';
        $this->baseUrl = rtrim($config['base_url'], '/');
    }

    public function index()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $this->view('form');
    }

    public function shorten()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            return $this->respond(['error' => 'Требуется авторизация'], 403);
        }

        $original = $_POST['url'] ?? '';

        if (empty($original) || !filter_var($original, FILTER_VALIDATE_URL)) {
            return $this->respond(['error' => 'Неверный формат URL'], 400);
        }

        $short = substr(md5($original . time() . random_bytes(4)), 0, 6);
        $userId = $_SESSION['user_id'];

        $model = new Url();
        $model->save($original, $short, $userId);

        $this->respond([
            'original_url' => $original,
            'short_url' => "{$this->baseUrl}/$short"
        ]);
    }

    public function redirect($code)
    {
        $urlModel = new Url();
        $analytics = new Analytics();

        $urlRow = $urlModel->findFullByCode($code); // должен возвращать всю строку, включая `id`
        if ($urlRow) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $analytics->logClick((int)$urlRow['id'], $ip);

            header("Location: " . $urlRow['original_url']);
            exit;
        }
    }
}
