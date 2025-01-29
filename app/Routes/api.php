<?php

use app\Core\Router;
use app\Middleware\AuthMiddleware;

$router = new Router();

$router->add('GET', '/test', 'TestController@test', ['TokenMiddleware']);

$router->add('POST', '/register', 'AuthController@register', ['AuthMiddleware']);
$router->add('POST', '/login', 'AuthController@login');

try {
    $router->dispatch();

} catch (Exception $e) {
    throw new \RuntimeException($e->getMessage());
}