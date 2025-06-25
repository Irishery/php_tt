<?php
class Router
{
    private $routes = [];

    public function get($pattern, $action)
    {
        $this->addRoute('GET', $pattern, $action);
    }

    public function post($pattern, $action)
    {
        $this->addRoute('POST', $pattern, $action);
    }

    private function addRoute($method, $pattern, $action)
    {
        $this->routes[] = ['method' => $method, 'pattern' => "#^$pattern$#", 'action' => $action];
    }

    public function dispatch($uri, $method)
    {
        $uri = strtok($uri, '?');
        foreach ($this->routes as $route) {
            if ($method === $route['method'] && preg_match($route['pattern'], $uri, $params)) {
                list($controller, $method) = explode('@', $route['action']);
                array_shift($params);
                call_user_func_array([new $controller, $method], $params);
                return;
            }
        }
        http_response_code(404);
        echo "404 Not Found";
    }
}
