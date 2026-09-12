<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Middleware;

/**
 * Requires authentication.
 */
class AuthMiddleware extends Middleware
{
    public function handle(): ?\App\Core\Response
    {
        $auth = new Auth();

        if (!$auth->check()) {
            $request = $_SERVER['REQUEST_URI'] ?? '/';
            (new \App\Core\Session())->flash('warning', 'Please login to continue.');
            header('Location: ' . \base_url() . '/login?redirect=' . urlencode($request));
            exit;
        }

        return null;
    }
}
