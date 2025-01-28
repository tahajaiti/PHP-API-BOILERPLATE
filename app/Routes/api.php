<?php

use app\Core\Router;
use app\Middleware\AuthMiddleware;

$router = new Router();

$router->add('GET', '/test', 'TestController@test', [
    AuthMiddleware::class
]);
$router->add('POST', '/register', 'AuthController@register');

try {
    $router->dispatch();

} catch (Exception $e) {
    throw new \RuntimeException($e->getMessage());
}