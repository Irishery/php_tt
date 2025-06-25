<?php
class Controller
{
    public function view(string $template, array $params = [])
    {
        extract($params); // превращает ['error' => 'x'] в $error
        ob_start();
        require __DIR__ . "/../Views/{$template}.php";
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layout.php';
    }

    protected function respond($data, $statusCode = 200)
    {
        $acceptJson = strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false;
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        $wantsJson = $acceptJson || $isAjax || isset($_GET['format']) && $_GET['format'] === 'json';

        if ($wantsJson) {
            header('Content-Type: application/json');
            http_response_code($statusCode);
            echo json_encode($data);
        } else {
            $this->view('url/result', $data);
        }
    }

    protected function requireAuth()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }
    }
}
