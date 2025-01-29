<?php
namespace app\Core;

use app\Helpers\Helper;
use Exception;
use JsonException;
use RuntimeException;

class Router
{
    private array $routes = [];
    private string $controllerNamespace = 'app\\Controller\\';
    private string $middlewareNamespace = 'app\\Middleware\\';
    private Request $request;

    public function __construct()
    {
        $this->request = new Request();
    }

    public function add(string $method, string $path, string $handler, array $middleware = []): void
    {
        $this->routes[strtoupper($method)][$path] = [
            'handler' => $handler,
            'middleware' => $middleware,
            'pattern' => $this->convertToRegex($path),
        ];
    }

    /**
     * @throws Exception
     */
    public function dispatch(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if (!isset($this->routes[$requestMethod])) {
            $this->sendNotFoundResponse();
            return;
        }

        foreach ($this->routes[$requestMethod] as $route) {
            if (preg_match($route['pattern'], $requestUri, $matches)) {
                $this->handleRoute($route, $matches);
                return;
            }
        }

        $this->sendNotFoundResponse();
    }

    /**
     * @throws JsonException
     */
    private function handleRoute(array $route, array $matches): void
    {
        $this->extractRouteParams($matches);

        $this->executeMiddleware($route['middleware']);

        [$controllerName, $methodName] = $this->resolveHandler($route['handler']);

        $this->executeControllerMethod($controllerName, $methodName);
    }

    private function extractRouteParams(array $matches): void
    {
        array_shift($matches);
        $routeParams = array_combine(array_keys($matches), array_values($matches));
        array_pop($routeParams);
        $this->request->merge($routeParams);
    }

    /**
     * @throws JsonException
     */
    private function executeMiddleware(array $middlewareList): void
    {
        foreach ($middlewareList as $middleware) {
            $middlewareClass = $this->middlewareNamespace . $middleware;

            if (!class_exists($middlewareClass) || !method_exists($middlewareClass, 'handle')) {
                throw new RuntimeException("Middleware not found or invalid: $middleware");
            }

            $middlewareInstance = new $middlewareClass();
            $response = $middlewareInstance->handle($this->request);

            if ($response instanceof Response) {
                $response->send();
            }
        }
    }

    private function resolveHandler(string $handler): array
    {
        $handlerParts = explode('@', $handler);

        if (count($handlerParts) !== 2) {
            throw new RuntimeException("Invalid handler format. Expected 'Controller@method'.");
        }

        return [$this->controllerNamespace . ucfirst($handlerParts[0]), $handlerParts[1]];
    }

    /**
     * @throws JsonException
     */
    private function executeControllerMethod(string $controllerName, string $methodName): void
    {
        if (!class_exists($controllerName) || !method_exists($controllerName, $methodName)) {
            throw new RuntimeException("Controller or method not found: $controllerName@$methodName");
        }

        $controller = new $controllerName();
        $response = $controller->$methodName($this->request);

        if ($response instanceof Response) {
            $response->send();
        }
    }

    private function convertToRegex(string $path): string
    {
        $pattern = preg_replace('/\{([^}]+)}/', '(?P<\1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    private function sendNotFoundResponse(): void
    {
        http_response_code(404);
        echo '404 Not Found';
    }
}