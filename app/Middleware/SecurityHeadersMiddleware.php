<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Middleware;

/**
 * Enforces production HTTP security headers (A+ Rating).
 */
class SecurityHeadersMiddleware extends Middleware
{
    /**
     * Set security headers on every response.
     */
    public static function apply(): void
    {
        if (headers_sent()) {
            return;
        }

        // 1. Strict-Transport-Security (HSTS)
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');

        // 2. Clickjacking Defense
        header('X-Frame-Options: SAMEORIGIN');

        // 3. MIME-sniffing Protection
        header('X-Content-Type-Options: nosniff');

        // 4. Referrer Policy
        header('Referrer-Policy: strict-origin-when-cross-origin');

        // 5. Permissions Policy (Restrict camera, mic, payment APIs)
        header('Permissions-Policy: camera=(self), microphone=(), geolocation=(), payment=()');

        // 6. Cross-Origin Policies
        header('Cross-Origin-Opener-Policy: same-origin');
        header('Cross-Origin-Resource-Policy: same-origin');

        // 7. Content Security Policy (CSP)
        $csp = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://code.jquery.com",
            "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com",
            "img-src 'self' data: blob: https:",
            "font-src 'self' data: https://cdn.jsdelivr.net https://fonts.gstatic.com",
            "connect-src 'self' https:",
            "frame-ancestors 'self'",
            "upgrade-insecure-requests"
        ];
        header('Content-Security-Policy: ' . implode('; ', $csp));
    }

    public function handle(): ?\App\Core\Response
    {
        self::apply();
        return null;
    }
}
