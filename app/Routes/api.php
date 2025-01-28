<?php

use app\Core\Router;

$router = new Router();

$router->add('GET', '/api/test', 'TestController@test');

try {
    $router->dispatch();

} catch (Exception $e) {
    throw new \RuntimeException($e->getMessage());
}