<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Authentication handler.
 */
class Auth
{
    private Session $session;
    private ?Database $db = null;

    public function __construct()
    {
        $this->session = new Session();
    }

    /**
     * Get the database connection.
     */
    private function db(): Database
    {
        if ($this->db === null) {
            $this->db = Database::getInstance();
        }
        return $this->db;
    }

    /**
     * Attempt to log in.
     */
    public function attempt(string $email, string $password): bool
    {
        $user = $this->db()->fetchOne(
            "SELECT u.*, r.name as role_name 
             FROM users u 
             LEFT JOIN roles r ON r.id = u.role_id 
             WHERE LOWER(u.email) = LOWER(:email) AND u.status = 'Active'",
            ['email' => $email]
        );

        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }

        // Update last login
        $this->db()->execute(
            "UPDATE users SET last_login = NOW() WHERE id = :id",
            ['id' => $user['id']]
        );

        // Store user in session (excluding password)
        unset($user['password']);
        $this->session->put([
            'user_id' => $user['id'],
            'user' => $user,
            'logged_in' => true,
        ]);

        // Load user permissions
        $this->loadPermissions((int)$user['id']);

        // Regenerate session
        $this->session->regenerate();

        // Log the login
        $this->logAction('login', 'user', (int)$user['id'], 'User logged in');

        return true;
    }

    /**
     * Load user permissions into session.
     */
    private function loadPermissions(int $userId): void
    {
        $permissions = $this->db()->fetchAll(
            "SELECT p.name 
             FROM permissions p 
             INNER JOIN role_permissions rp ON rp.permission_id = p.id 
             INNER JOIN users u ON u.role_id = rp.role_id 
             WHERE u.id = :user_id",
            ['user_id' => $userId]
        );

        $this->session->set('user_permissions', array_column($permissions, 'name'));
    }

    /**
     * Log the user out.
     */
    public function logout(): void
    {
        $userId = $this->user()['id'] ?? null;
        if ($userId) {
            $this->logAction('logout', 'user', (int)$userId, 'User logged out');
        }
        $this->session->destroy();
    }

    /**
     * Check if the user is authenticated.
     */
    public function check(): bool
    {
        return $this->session->get('logged_in', false) && $this->session->get('user_id');
    }

    /**
     * Get the authenticated user.
     */
    public function user(): ?array
    {
        if (!$this->check()) {
            return null;
        }
        $user = $this->session->get('user');
        if ($user && !isset($user['role_name']) && !empty($user['role_id'])) {
            $role = $this->db()->fetchOne("SELECT name FROM roles WHERE id = :id", ['id' => (int)$user['role_id']]);
            if ($role) {
                $user['role_name'] = $role['name'];
                $this->session->set('user', $user);
            }
        }
        return $user;
    }

    /**
     * Get the authenticated user's ID.
     */
    public function id(): ?int
    {
        return $this->session->get('user_id');
    }

    /**
     * Check if the user has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        $user = $this->user();
        if (!$user) {
            return false;
        }

        // Super Admin (role_id 1 or role_name 'Super Admin') has all permissions
        if (($user['role_id'] ?? 0) == 1 || strcasecmp($user['role_name'] ?? '', 'Super Admin') === 0) {
            return true;
        }

        $permissions = $this->session->get('user_permissions', []);
        return in_array($permission, $permissions);
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        $user = $this->user();
        if (!$user) return false;
        if (strcasecmp($role, 'Super Admin') === 0 && ($user['role_id'] ?? 0) == 1) {
            return true;
        }
        return strcasecmp($user['role_name'] ?? '', $role) === 0;
    }

    /**
     * Check if the user is a Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('Super Admin');
    }

    /**
     * Log an audit action.
     */
    public function logAction(string $action, string $entity, ?int $entityId, string $description): void
    {
        try {
            $this->db()->execute(
                "INSERT INTO audit_logs (user_id, action, entity, entity_id, description, ip_address, created_at) 
                 VALUES (:user_id, :action, :entity, :entity_id, :description, :ip_address, NOW())",
                [
                    'user_id' => $this->id(),
                    'action' => $action,
                    'entity' => $entity,
                    'entity_id' => $entityId,
                    'description' => $description,
                    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                ]
            );
        } catch (\Throwable $e) {
            // Don't let audit log failures break the application
            error_log('Audit log failed: ' . $e->getMessage());
        }
    }

    /**
     * Get current user ID (static helper).
     */
    public static function currentUserId(): ?int
    {
        return (new self())->id();
    }
}
