<?php
namespace app\Core;

use app\Core\Request;
use app\Core\Response;
use app\Helpers\Helper;
use Exception;

class Router
{
    private array $routes = [];
    private string $controllerNamespace = 'app\\Controller\\';
    private string $middlewareNamespace = 'app\\Middleware\\';
    private Request $request;

    public function __construct(){
        $this->request = new Request();
    }

    public function add(string $method, string $path, string $handler, array $middleware = []): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }

    /**
     * @throws Exception
     */
    public function dispatch(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            $pattern = $this->convertToRegex($route['path']);

            if ($route['method'] === $requestMethod && preg_match($pattern, $requestUri, $matches)) {
                array_shift($matches);
                $args = array_values($matches);

                foreach ($route['middleware'] as $middleware) {
                    if (class_exists($middleware) && method_exists($middleware, 'handle')) {
                        $middlewareInstance = new $middleware();
                        $response = $middlewareInstance->handle($this->request);
                        if ($response instanceof Response) {
                            $response->send();
                        }
                    } else {
                        throw new \RuntimeException("Middleware is not found or invalid: $middleware");
                    }
                }

                $handlerName = explode('@', $route['handler']);
                if (count($handlerName) !== 2) {
                    throw new \RuntimeException("Invalid handler format. Expected 'Controller@method'.");
                }

                $controllerName = ucfirst($handlerName[0]);
                $className = $this->controllerNamespace . $controllerName;
                $methodName = $handlerName[1];
                
                if (class_exists($className) && method_exists($className, $methodName)) {
                    $response = (new $className())->$methodName($this->request, $args);
                    $response->send();
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
