<?php
class Controller
{
    protected function view($name, $data = [])
    {
        extract($data);
        require __DIR__ . "/../Views/$name.php";
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
            $this->view('result', $data);
        }
    }
}
