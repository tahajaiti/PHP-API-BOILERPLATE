<?php

use app\Core\Router;
use app\Middleware\AuthMiddleware;

$router = new Router();

$router->add('GET', '/test', 'TestController@test', ['AuthMiddleware']);

$router->add('POST', '/register', 'AuthController@register');
$router->add('GET', '/users', 'AuthController@all');
$router->add('GET', '/users/{id}', 'AuthController@get');

try {
    $router->dispatch();

} catch (Exception $e) {
    throw new \RuntimeException($e->getMessage());
}