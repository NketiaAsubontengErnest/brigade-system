<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Base Middleware class.
 */
abstract class Middleware
{
    abstract public function handle(): ?Response;

    protected function redirect(string $url, string $message = '', string $type = 'warning'): Response
    {
        $session = new Session();
        if ($message) {
            $session->flash($type, $message);
        }
        header('Location: ' . $url);
        exit;
    }

    protected function jsonError(string $message, int $code = 403): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode(['error' => $message]);
        exit;
    }
}
