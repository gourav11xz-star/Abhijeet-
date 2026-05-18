<?php
class Router
{
    protected $routes = [];
    protected $notFoundHandler;

    public function __construct()
    {
        $this->routes = ['GET' => [], 'POST' => []];
        $this->notFoundHandler = function () {
            http_response_code(404);
            echo "404 Not Found";
        };
    }

    // Register GET route
    public function get($path, $callback)
    {
        $this->routes['GET'][$path] = $callback;
    }

    // Register POST route
    public function post($path, $callback)
    {
        $this->routes['POST'][$path] = $callback;
    }

    // Set 404 handler
    public function set404($callback)
    {
        $this->notFoundHandler = $callback;
    }

    // Dispatch the request
    public function dispatch()
    {
        $uri = '';
        $method = $_SERVER['REQUEST_METHOD'];

        // Check if rewritten URL is available
        if (isset($_GET['url'])) {
            $uri = '/' . $_GET['url'];
        } else {
            $uri = '/';
        }

        // Ensure uri starts with /
        if (empty($uri) || $uri[0] !== '/') {
            $uri = '/' . $uri;
        }

        // Helper to strip trailing slash
        if (strlen($uri) > 1) {
            $uri = rtrim($uri, '/');
        }

        // Iterate through routes
        $routes = $this->routes[$method] ?? [];
        foreach ($routes as $route => $callback) {
            // Convert route to regex if it's not already (simple implementation)
            // For now, assume routes in web.php use regex for dynamic parts like ([0-9]+)
            // We need to match exact string first for performance if no regex chars

            $routeRegex = '#^' . $route . '$#';

            if (preg_match($routeRegex, $uri, $matches)) {
                array_shift($matches); // Remove full match

                if (is_string($callback)) {
                    $parts = explode('@', $callback);
                    $controllerName = $parts[0];
                    $methodName = $parts[1];

                    require_once '../app/controllers/' . $controllerName . '.php';
                    $controller = new $controllerName();
                    // Pass matches to controller method
                    call_user_func_array([$controller, $methodName], $matches);
                    return;
                } elseif (is_callable($callback)) {
                    call_user_func_array($callback, $matches);
                    return;
                }
            }
        }

        // 404
        if ($this->notFoundHandler) {
            call_user_func($this->notFoundHandler);
        } else {
            http_response_code(404);
            echo "404 Not Found";
        }
    }
}
