<?php

require_once __DIR__ . '/../Models/Url.php';
require_once __DIR__ . '/../Models/Analytics.php';
require_once __DIR__ . '/../helpers/geo.php';

class UrlController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $config = require __DIR__ . '/../../config/config.php';
        $this->baseUrl = $config['base_url'];
    }

    private function get_short_code(string $original_url): string
    {
        return substr(md5($original_url . time() . random_bytes(4)), 0, 6);
    }


    public function index()
    {
        $this->requireAuth();
        $this->respondView('url/form');
    }

    public function shorten()
    {
        $isJson = strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false;

        if ($isJson) {
            $this->requireApiAuth();
        } else {
            $this->requireAuth();
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            return $this->respondError('Пользователь не авторизован', 403);
        }

        $data = $isJson
            ? json_decode(file_get_contents('php://input'), true)
            : $_POST;

        $original = trim($data['url']);

        if (empty($original) || !filter_var($original, FILTER_VALIDATE_URL)) {
            return $this->respondError('Неверный формат URL', 400);
        }

        $short = $this->get_short_code($original);

        $model = new Url();
        $model->save($original, $short, $userId);

        $response = [
            'original_url' => $original,
            'short_code' => $short,
            'short_url' => "{$this->baseUrl}/r/{$short}"
        ];

        if ($isJson) {
            return $this->respondJson($response);
        }

        return $this->respondView('url/result', $response);
    }

    public function apiShorten()
    {
        $this->requireApiAuth();
        $userId = $_SESSION['user_id'];

        $input = json_decode(file_get_contents('php://input'), true);
        $original = trim($input['url']);

        if (!filter_var($original, FILTER_VALIDATE_URL)) {
            return $this->respondError('Некорректный URL', 400);
        }

        $urlModel = new Url();
        $shortCode = $this->get_short_code($original);

        $urlModel->save($original, $shortCode, $userId);

        return $this->respondJson([
            'original_url' => $original,
            'short_code' => $shortCode,
            'short_url' => $this->baseUrl . '/r/' . $shortCode
        ]);
    }


    public function redirect($code)
    {
        $urlModel = new Url();
        $analytics = new Analytics();

        $urlRow = $urlModel->findFullByCode($code);
        if (!$urlRow) {
            http_response_code(404);
            echo "Ссылка не найдена";
            return;
        }

        $ip = getClientIp();
        $analytics->logClick((int)$urlRow['id'], $ip);

        header("Location: " . $urlRow['original_url']);
        exit;
    }
}
