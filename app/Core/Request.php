<?php

declare(strict_types=1);

namespace App\Core;

/**
 * HTTP Request handler.
 */
class Request
{
    private array $query;
    private array $body;
    private array $files;
    private array $server;
    private array $headers;

    public function __construct()
    {
        $this->query = $_GET;
        $this->body = $_POST;
        $this->files = $_FILES;
        $this->server = $_SERVER;
        $this->headers = $this->parseHeaders();
    }

    /**
     * Get a value from the request.
     */
    public function input(string $key, mixed $default = null): mixed
    {
        if (isset($this->body[$key])) {
            return $this->body[$key];
        }
        if (isset($this->query[$key])) {
            return $this->query[$key];
        }
        return $default;
    }

    /**
     * Get all input data.
     */
    /**
     * Alias of input(). Several controllers read parameters as $request->get(...).
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->input($key, $default);
    }

    public function all(): array
    {
        return array_merge($this->query, $this->body);
    }

    /**
     * Get only specified inputs.
     */
    public function only(array $keys): array
    {
        $all = $this->all();
        $filtered = [];
        foreach ($keys as $key) {
            $filtered[$key] = $all[$key] ?? null;
        }
        return $filtered;
    }

    /**
     * Get all inputs except specified keys.
     */
    public function except(array $keys): array
    {
        return array_diff_key($this->all(), array_flip($keys));
    }

    /**
     * Get the request method.
     */
    public function method(): string
    {
        $method = $this->server['REQUEST_METHOD'] ?? 'GET';

        // Handle PUT/DELETE via _method
        if ($method === 'POST' && isset($this->body['_method'])) {
            return strtoupper($this->body['_method']);
        }

        return strtoupper($method);
    }

    /**
     * Check if the request is AJAX.
     */
    public function isAjax(): bool
    {
        return !empty($this->server['HTTP_X_REQUESTED_WITH']) &&
            strtolower($this->server['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Check if request is POST.
     */
    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    /**
     * Get a file from the request.
     */
    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    /**
     * Get the URI path.
     */
    public function path(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH);
        return $path === false ? '/' : $path;
    }

    /**
     * Get the full URL.
     */
    public function url(): string
    {
        return $this->server['REQUEST_URI'] ?? '/';
    }

    /**
     * Get query parameter.
     */
    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    /**
     * Get all query parameters.
     */
    public function queries(): array
    {
        return $this->query;
    }

    /**
     * Get header value.
     */
    public function header(string $name, mixed $default = null): mixed
    {
        $key = strtolower(str_replace('-', '_', $name));
        return $this->headers[$key] ?? $default;
    }

    /**
     * Parse HTTP headers.
     */
    private function parseHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $header = strtolower(str_replace('_', '-', substr($key, 5)));
                $headers[$header] = $value;
            }
        }
        return $headers;
    }

    /**
     * Get JSON body.
     */
    public function json(): mixed
    {
        $body = file_get_contents('php://input');
        return json_decode($body, true);
    }

    /**
     * Get IP address.
     */
    public function ip(): string
    {
        return $this->server['REMOTE_ADDR'] ?? '127.0.0.1';
    }
}
