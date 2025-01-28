<?php

namespace app\Core;

use Exception;

class Router
{
    private array $routes = [];
    private string $controllerNamespace = 'app\\Controller\\';

    public function add($method, $path, $handler): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler
        ];
    }

    /**
     * @throws Exception
     */
    public function dispatch()
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            $pattern = $this->convertToRegex($route['path']);

            if ($route['method'] === $requestMethod && preg_match($pattern, $requestUri, $matches)) {
                array_shift($matches);

                $args = array_values($matches);

                $handlerName = explode('@', $route['handler']);
                if (count($handlerName) !== 2) {
                    throw new \RuntimeException("Invalid handler format. Expected 'Controller@method'.");
                }

                $controllerName = ucfirst($handlerName[0]);
                $className = $this->controllerNamespace . $controllerName;
                $methodName = $handlerName[1];
                
                if (class_exists($className) && method_exists($className, $methodName)) {
                    $controller = new $className();
                    return call_user_func_array([$controller, $methodName], $args);
                }

                throw new \RuntimeException("Controller or method not found: $className@$methodName");
            }
        }

        http_response_code(404);
        echo '404 Not found';
    }

    private function convertToRegex($path): string
    {
        $pattern = preg_replace('/\{([^}]+)}/', '(?P<\1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }
}
