<?php

namespace app\Core;

use app\Helpers\Helper;
use HTMLPurifier;
use HTMLPurifier_Config;
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

        $this->sanitize();
    }

    private function sanitize():void
    {
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
        array_walk_recursive($this->data, static function (&$value) use (&$purifier) {
            if (is_string($value)) {
                $value = $purifier->purify($value);
            }
        });
    }

    public function merge(array $data):void {
        $this->data = array_merge($this->data, $data);
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

    public function getAllHeaders(): array {
        if (function_exists('getallheaders')) {
            return getallheaders();
        }

        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $headerName = str_replace('_', '-', strtolower(substr($key, 5)));
                $headers[$headerName] = $value;
            }
        }
        return $headers;
    }

    public function getHeader(string $name) {
        $headers = $this->getAllHeaders();
        $name = strtolower($name);

        foreach ($headers as $headerName => $headerValue) {
            if (strtolower($headerName) === $name) {
                return $headerValue;
            }
        }
        return null;
    }

    private function isJson(): bool {
        return isset($_SERVER['CONTENT_TYPE']) &&
            str_contains($_SERVER['CONTENT_TYPE'], 'application/json');
    }
}