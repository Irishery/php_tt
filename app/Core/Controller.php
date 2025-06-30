<?php
class Controller
{
    public function view(string $template, array $params = [])
    {
        extract($params);
        ob_start();
        require __DIR__ . "/../Views/{$template}.php";
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layout.php';
    }

    protected function respondJson($data, int $statusCode = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    protected function respondView(string $template, array $params = []): void
    {
        $this->view($template, $params);
    }

    protected function respondError(string $message, int $statusCode = 400): void
    {
        $this->respondJson(['error' => $message], $statusCode);
    }

    protected function requireAuth()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }
    }

    protected function requireApiAuth()
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'];

        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Authorization header missing']);
            exit;
        }

        $auth = new Auth();
        $user = $auth->findByApiToken($token);
        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid token']);
            exit;
        }

        $_SESSION['user_id'] = $user['id'];
    }
}
