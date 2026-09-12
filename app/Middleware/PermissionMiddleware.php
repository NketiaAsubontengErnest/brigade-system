<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Middleware;

/**
 * Requires specific permission.
 */
class PermissionMiddleware extends Middleware
{
    private string $permission;

    public function __construct(string $permission = '')
    {
        $this->permission = $permission;
    }

    public function handle(): ?\App\Core\Response
    {
        $auth = new Auth();

        if (!$auth->check()) {
            header('Location: /login');
            exit;
        }

        if ($this->permission && !$auth->hasPermission($this->permission)) {
            http_response_code(403);
            require __DIR__ . '/../views/errors/403.php';
            exit;
        }

        return null;
    }
}
