<?php
$uri = $_SERVER['REQUEST_URI'];

if ($uri === '/' || $uri === '/home') {
    echo "<h1>Добро пожаловать!</h1><p>Это минимальный PHP-проект.</p>";
} elseif ($uri === '/about') {
    echo "<h1>О нас</h1><p>Мини-сайт на одном файле PHP.</p>";
} else {
    http_response_code(404);
    echo "<h1>404</h1><p>Страница не найдена.</p>";
}
