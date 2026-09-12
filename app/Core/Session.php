<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Session management.
 */
class Session
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Set cookie path to base URL so the session works on all routes
            $cookiePath = '/' !== '' ? '/' : '/';
            if (function_exists('base_url')) {
                $cookiePath = base_url() ?: '/';
            }
            session_start([
                'cookie_httponly' => true,
                'cookie_secure' => false,
                'cookie_samesite' => 'Lax',
                'cookie_path' => $cookiePath,
                'use_strict_mode' => true,
            ]);
        }
    }

    /**
     * Set a session value.
     */
    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Get a session value.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if a session key exists.
     */
    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Remove a session value.
     */
    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Flash a message for the next request.
     */
    public function flash(string $type, string $message): void
    {
        $_SESSION['flash'][$type] = $message;
    }

    /**
     * Get and clear flash messages.
     */
    public function getFlash(string $type): ?string
    {
        if (isset($_SESSION['flash'][$type])) {
            $message = $_SESSION['flash'][$type];
            unset($_SESSION['flash'][$type]);
            return $message;
        }
        return null;
    }

    /**
     * Get all flash messages.
     */
    public function getFlashes(): array
    {
        $flashes = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $flashes;
    }

    /**
     * Regenerate the session ID.
     */
    public function regenerate(): void
    {
        session_regenerate_id(true);
    }

    /**
     * Destroy the session.
     */
    public function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
    }

    /**
     * Set flash data (alias).
     */
    public function flashData(string $type, string $message): void
    {
        $this->flash($type, $message);
    }

    /**
     * Set multiple values.
     */
    public function put(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }
}
