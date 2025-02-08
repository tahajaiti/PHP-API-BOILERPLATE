<?php

use app\Core\Router;

$router = new Router();

$router->add('POST', '/register', 'AuthController@create', ['AuthMiddleware']);
$router->add('POST', '/login', 'AuthController@login');

$router->add('GET', '/users', 'UserController@index');
$router->add('GET', '/users/{id}', 'UserController@getById');
$router->add('PUT', '/users/{id}', 'UserController@update');
$router->add('DELETE', '/users/{id}', 'UserController@delete');

try {
    $router->dispatch();
} catch (Exception $e) {
    throw new \RuntimeException($e->getMessage());
}