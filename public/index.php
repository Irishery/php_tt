<?php
require_once __DIR__ . '/../app/Core/Router.php';
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Core/Controller.php';

spl_autoload_register(function ($class) {
    $paths = ['Controllers', 'Models'];
    foreach ($paths as $folder) {
        $file = __DIR__ . "/../app/$folder/$class.php";
        if (file_exists($file)) require_once $file;
    }
});

$router = new Router();

// URL 
$router->get('/', 'UrlController@index');
$router->post('/shorten', 'UrlController@shorten');
$router->get('/r/(.*)', 'UrlController@redirect');

// AUTH
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');

$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');

$router->get('/logout', 'AuthController@logout');

// ANAL
$router->get('/analytics', 'AnalyticsController@index');



$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
