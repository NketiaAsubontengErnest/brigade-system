<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Simple but powerful URL Router.
 */
class Router
{
    private array $routes = [];
    private string $prefix = '';
    private array $groupMiddleware = [];
    private array $namedRoutes = [];

    /**
     * Register a GET route.
     */
    /**
     * Normalize action to 'Controller@method' string.
     */
    private function normalizeAction($action): string
    {
        if (is_array($action) && count($action) === 2) {
            return $action[0] . '@' . $action[1];
        }
        return (string) $action;
    }

    public function get(string $path, $action, string $name = ''): self
    {
        $this->addRoute('GET', $path, $this->normalizeAction($action), $name);
        return $this;
    }

    /**
     * Register a POST route.
     */
    public function post(string $path, $action, string $name = ''): self
    {
        $this->addRoute('POST', $path, $this->normalizeAction($action), $name);
        return $this;
    }

    /**
     * Register a PUT route.
     */
    public function put(string $path, $action, string $name = ''): self
    {
        $this->addRoute('PUT', $path, $this->normalizeAction($action), $name);
        return $this;
    }

    /**
     * Register a DELETE route.
     */
    public function delete(string $path, $action, string $name = ''): self
    {
        $this->addRoute('DELETE', $path, $this->normalizeAction($action), $name);
        return $this;
    }

    /**
     * Register a route that accepts any HTTP method.
     */
    public function any(string $path, $action, string $name = ''): self
    {
        foreach (['GET', 'POST', 'PUT', 'DELETE'] as $method) {
            $this->addRoute($method, $path, $this->normalizeAction($action), $name);
        }
        return $this;
    }

    /**
     * Route group with prefix and middleware.
     */
    public function group(string $prefix, array $middleware, callable $callback): void
    {
        $oldPrefix = $this->prefix;
        $oldMiddleware = $this->groupMiddleware;

        $this->prefix = $oldPrefix . $prefix;
        $this->groupMiddleware = array_merge($oldMiddleware, $middleware);

        $callback($this);

        $this->prefix = $oldPrefix;
        $this->groupMiddleware = $oldMiddleware;
    }

    /**
     * Add a route.
     */
    private function addRoute(string $method, string $path, string $action, string $name = ''): void
    {
        $fullPath = $this->prefix . '/' . trim($path, '/');
        $fullPath = '/' . trim($fullPath, '/');

        if ($fullPath === '') {
            $fullPath = '/';
        }

        $this->routes[] = [
            'method' => $method,
            'path' => $fullPath,
            'action' => $action,
            'middleware' => $this->groupMiddleware,
            'name' => $name,
        ];

        if ($name) {
            $this->namedRoutes[$name] = $fullPath;
        }
    }

    /**
     * Match a route against the request.
     */
    public function match(string $method, string $uri): ?array
    {
        $uri = '/' . trim($uri, '/');

        if ($uri === '') {
            $uri = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->matchRoute($route['path'], $uri);
            if ($params !== false) {
                return [
                    'route' => $route,
                    'params' => $params,
                ];
            }
        }

        return null;
    }

    /**
     * Match a route pattern against a URI.
     */
    private function matchRoute(string $pattern, string $uri): array|false
    {
        $patternParts = explode('/', trim($pattern, '/'));
        $uriParts = explode('/', trim($uri, '/'));

        if (count($patternParts) !== count($uriParts)) {
            return false;
        }

        $params = [];

        foreach ($patternParts as $index => $part) {
            if (str_starts_with($part, '{') && str_ends_with($part, '}')) {
                $paramName = trim($part, '{}');
                $params[$paramName] = $uriParts[$index];
            } elseif ($part !== $uriParts[$index]) {
                return false;
            }
        }

        return $params;
    }

    /**
     * Get the named route URL.
     */
    public function url(string $name, array $params = []): string
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new \RuntimeException("Route named '{$name}' not found");
        }

        $url = $this->namedRoutes[$name];

        foreach ($params as $key => $value) {
            $url = str_replace('{' . $key . '}', (string)$value, $url);
        }

        return $url;
    }

    /**
     * Dispatch the current request.
     */
    public function dispatch(string $method, string $uri): mixed
    {
        $result = $this->match($method, $uri);

        if ($result === null) {
            return $this->dispatchNotFound();
        }

        $route = $result['route'];
        $params = $result['params'];

        // Run middleware
        foreach ($route['middleware'] as $middlewareClass) {
            $middleware = new $middlewareClass();
            $response = $middleware->handle();
            if ($response !== null) {
                return $response;
            }
        }

        // Parse controller@action
        $parts = explode('@', $route['action']);
        if (count($parts) !== 2) {
            throw new \RuntimeException("Invalid route action: {$route['action']}");
        }

        [$controllerClass, $action] = $parts;
        $controller = new $controllerClass();

        if (!method_exists($controller, $action)) {
            throw new \RuntimeException("Method {$action} not found in {$controllerClass}");
        }

        return call_user_func_array([$controller, $action], $params);
    }

    /**
     * Dispatch a 404.
     */
    private function dispatchNotFound(): void
    {
        http_response_code(404);
        require __DIR__ . '/../views/errors/404.php';
        exit;
    }

    /**
     * Get all registered routes.
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}
