<?php

namespace app\Core;

use JetBrains\PhpStorm\NoReturn;
use JsonException;

class Response
{
    private mixed $data;
    private int $status;
    private array $headers;

    public function __construct(mixed $data, int $status = 200, array $headers = [])
    {
        $this->data = $data;
        $this->status = $status;
        $this->headers = array_merge([
            'Content-Type' => 'application/json'
        ], $headers);
    }

    public static function json(mixed $data = null, int $status = 200, array $headers = []): Response
    {
        return new static($data, $status, $headers);
    }

    public static function success(mixed $data = null, string $message = 'Success'): Response
    {
        return new static ([
            'success' => true,
            'data' => $data,
            'message' => $message
        ]);
    }

    public static function error(string $message = 'Error', int $status = 400, mixed $errors = null): static
    {
        return new static([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }

    public function withHeader(string $key, string $value): static
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * @throws JsonException
     */
    #[NoReturn] public function send(): void
    {
        http_response_code($this->status);

        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }

        echo json_encode($this->data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
        exit;
    }
}