<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, string $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, string $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $uri, string $method): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        # exact match
        if (isset($this->routes[$method][$path])) {
            $this->invokeHandler($this->routes[$method][$path]);
            return;
        }

        # dynamic match
        foreach (($this->routes[$method] ?? []) as $route => $handler) {
            if (strpos($route, '{') === false) {
                continue; // Skip non-dynamic routes
            }

            preg_match_all('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', $route, $m);
            $paramNames = $m[1] ?? [];

            $regex = preg_replace('/\{[a-zA-Z_][a-zA-Z0-9_]*\}/', '([^/]+)', $route);
            $regex = '#^' . $regex . '$#';

            if(!preg_match($regex, $path, $matches)) {
                continue;
            }

            array_shift($matches); // Remove full match

            $params = [];
            foreach ($paramNames as $index => $name) {
                $params[$name] = $matches[$index] ?? null;
            }

            $this->invokeHandler($handler, $params);
            return;
        }

        http_response_code(404);
        \App\Core\View::render('errors/404', ['seo' =>  ['title' => 'Not found']]);
    }

    private function invokeHandler(string $handler, array $params = []): void
    {
        [$controllerName, $method] = explode('@', $handler);
        $controllerClass = 'App\\Controllers\\' . $controllerName;

        if (!class_exists($controllerClass)) {
            http_response_code(500);
            echo "Controller $controllerClass not found";
            return;
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $method)) {
            http_response_code(500);
            echo "Method $method not found in controller $controllerClass";
            return;
        }

        // inject params by method param name (slug)
        $reflection = new \ReflectionMethod($controller, $method);
        $args = [];
        foreach ($reflection->getParameters() as $param) {
            $name = $param->getName();
            $args[] = $params[$name] ?? null;
        }

        $reflection->invokeArgs($controller, $args);
    }
}