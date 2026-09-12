<?php

declare(strict_types=1);

namespace App\Core;

/**
 * CSRF Protection.
 */
class CSRF
{
    private const TOKEN_NAME = 'csrf_token';
    private const TOKEN_LENGTH = 64;

    /**
     * Generate a CSRF token.
     */
    public static function token(): string
    {
        $session = new Session();
        $token = $session->get(self::TOKEN_NAME);

        if (!$token) {
            $token = self::generateToken();
            $session->set(self::TOKEN_NAME, $token);
        }

        return $token;
    }

    /**
     * Generate a random token.
     */
    private static function generateToken(): string
    {
        return bin2hex(random_bytes(self::TOKEN_LENGTH / 2));
    }

    /**
     * Verify the CSRF token.
     */
    public static function verify(): bool
    {
        $session = new Session();
        $token = $session->get(self::TOKEN_NAME);

        $inputToken = $_POST[self::TOKEN_NAME] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

        if (!$token || !$inputToken || !hash_equals($token, $inputToken)) {
            http_response_code(403);
            echo json_encode(['error' => 'CSRF token mismatch']) ;
            exit;
        }

        // Regenerate token after verification
        $session->set(self::TOKEN_NAME, self::generateToken());

        return true;
    }

    /**
     * Get the token field name.
     */
    public static function fieldName(): string
    {
        return self::TOKEN_NAME;
    }
}
