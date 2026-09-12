<?php

declare(strict_types=1);

namespace App\Core;

/**
 * HTTP Response class.
 */
class Response
{
    private int $statusCode;
    private string $body;
    private array $headers;

    public function __construct(string $body = '', int $statusCode = 200, array $headers = [])
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
        $this->headers = $headers;
    }

    public static function json(mixed $data, int $statusCode = 200): self
    {
        $body = json_encode($data, JSON_UNESCAPED_UNICODE);
        return new self($body, $statusCode, ['Content-Type' => 'application/json']);
    }

    public static function redirect(string $url, int $statusCode = 302): self
    {
        $response = new self('', $statusCode, ['Location' => $url]);
        $response->sendHeaders();
        exit;
    }

    public function sendHeaders(): void
    {
        http_response_code($this->statusCode);
        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }
    }
}
