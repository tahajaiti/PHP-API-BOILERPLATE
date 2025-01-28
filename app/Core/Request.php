<?php

namespace App\Core;

use JsonException;

class Request {

    private array $data;
    private string $method;
    private string $uri;

    /**
     * @throws JsonException
     */
    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = $_SERVER['REQUEST_URI'];

        $this->data = array_merge($_GET, $_POST);

        if ($this->isJson()){
            $json = json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);
            if ($json){
                $this->data = array_merge($this->data, $json);
            }
        }
    }

    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->data;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->uri;
    }

    private function isJson(): bool {
        return isset($_SERVER['CONTENT_TYPE']) &&
            str_contains($_SERVER['CONTENT_TYPE'], 'application/json');
    }
}