<?php

use app\Core\Router;
use app\Middleware\AuthMiddleware;

$router = new Router();

$router->add('GET', '/api/test', 'TestController@test', [
    AuthMiddleware::class
]);

try {
    $router->dispatch();

} catch (Exception $e) {
    throw new \RuntimeException($e->getMessage());
}