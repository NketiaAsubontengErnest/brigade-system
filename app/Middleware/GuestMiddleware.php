<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Middleware;

/**
 * Requires guest (not authenticated).
 */
class GuestMiddleware extends Middleware
{
    public function handle(): ?\App\Core\Response
    {
        $auth = new Auth();

        if ($auth->check()) {
            header('Location: ' . \base_url() . '/dashboard');
            exit;
        }

        return null;
    }
}
