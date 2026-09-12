<?php

declare(strict_types=1);

namespace Database\Migrations;

use App\Core\Database;

class CreateRolesAndPermissions
{
    public function up(Database $db): void
    {
        // Roles
        $db->execute("
            CREATE TABLE roles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL UNIQUE,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Permissions
        $db->execute("
            CREATE TABLE permissions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL UNIQUE,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Role-Permission pivot
        $db->execute("
            CREATE TABLE role_permissions (
                role_id INTEGER REFERENCES roles(id) ON DELETE CASCADE,
                permission_id INTEGER REFERENCES permissions(id) ON DELETE CASCADE,
                PRIMARY KEY (role_id, permission_id)
            )
        ");

        // Users
        $db->execute("
            CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                full_name VARCHAR(200) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                phone VARCHAR(30),
                password VARCHAR(255) NOT NULL,
                role_id INTEGER REFERENCES roles(id) ON DELETE SET NULL,
                status VARCHAR(20) DEFAULT 'Active' CHECK (status IN ('Active', 'Inactive', 'Suspended')),
                last_login TIMESTAMP,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $db->execute("CREATE INDEX idx_users_email ON users(email)");
        $db->execute("CREATE INDEX idx_users_role ON users(role_id)");
        $db->execute("CREATE INDEX idx_users_status ON users(status)");

        // Insert default roles
        $roles = [
            ['Super Admin', 'Full system access'],
            ['Captain', 'Company leader with full operational access'],
            ['Secretary', 'Administrative officer'],
            ['Treasurer', 'Financial officer'],
            ['Training Officer', 'Training and development officer'],
            ['Welfare Officer', 'Member welfare officer'],
            ['Officer', 'General officer'],
            ['Member', 'Regular brigade member'],
        ];

        foreach ($roles as [$name, $desc]) {
            $db->execute(
                "INSERT INTO roles (name, description) VALUES (:name, :desc)",
                ['name' => $name, 'desc' => $desc]
            );
        }

        // Insert permissions
        $permissions = [
            'members.view', 'members.create', 'members.edit', 'members.delete', 'members.approve',
            'attendance.view', 'attendance.create', 'attendance.edit',
            'activities.view', 'activities.create', 'activities.edit', 'activities.delete',
            'events.view', 'events.create', 'events.edit', 'events.delete',
            'dues.view', 'dues.create', 'dues.edit', 'dues.delete',
            'payments.view', 'payments.create', 'payments.edit', 'payments.void',
            'finance.view', 'finance.create', 'finance.edit', 'finance.approve',
            'training.view', 'training.create', 'training.edit',
            'badges.view', 'badges.create', 'badges.award',
            'awards.view', 'awards.create',
            'announcements.view', 'announcements.create', 'announcements.edit', 'announcements.delete',
            'news.view', 'news.create', 'news.edit', 'news.delete',
            'gallery.view', 'gallery.create', 'gallery.delete',
            'documents.view', 'documents.create', 'documents.delete',
            'reports.view', 'reports.export',
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'settings.view', 'settings.edit',
            'officers.view', 'officers.create', 'officers.edit',
            'sections.view', 'sections.create', 'sections.edit',
        ];

        foreach ($permissions as $perm) {
            $db->execute(
                "INSERT INTO permissions (name) VALUES (:name)",
                ['name' => $perm]
            );
        }

        // Assign all permissions to Super Admin (role_id = 1)
        $db->execute("
            INSERT INTO role_permissions (role_id, permission_id)
            SELECT 1, id FROM permissions
        ");

        // Assign relevant permissions to Captain (role_id = 2)
        $captainPerms = [
            'members.view', 'members.create', 'members.edit', 'members.approve',
            'attendance.view', 'attendance.create', 'attendance.edit',
            'activities.view', 'activities.create', 'activities.edit',
            'events.view', 'events.create', 'events.edit',
            'dues.view', 'dues.create', 'dues.edit',
            'payments.view', 'payments.create',
            'finance.view',
            'training.view', 'training.create', 'training.edit',
            'badges.view', 'badges.create', 'badges.award',
            'awards.view', 'awards.create',
            'announcements.view', 'announcements.create', 'announcements.edit',
            'reports.view', 'reports.export',
            'officers.view', 'sections.view',
        ];
        foreach ($captainPerms as $perm) {
            $db->execute("
                INSERT INTO role_permissions (role_id, permission_id)
                SELECT 2, id FROM permissions WHERE name = :perm
            ", ['perm' => $perm]);
        }        // Treasurer (role_id = 4)
        $treasurerPerms = [
            'members.view',
            'dues.view', 'dues.create', 'dues.edit',
            'payments.view', 'payments.create', 'payments.void',
            'finance.view', 'finance.create', 'finance.edit', 'finance.approve',
            'reports.view', 'reports.export',
        ];
        foreach ($treasurerPerms as $perm) {
            $db->execute(
                "INSERT INTO role_permissions (role_id, permission_id)
                SELECT 4, id FROM permissions WHERE name = :perm"
            , ['perm' => $perm]);
        }

        // Secretary (role_id = 3) - also gets dues and finance permissions
        $secretaryPerms = [
            'members.view',
            'dues.view', 'dues.create', 'dues.edit',
            'payments.view', 'payments.create',
            'finance.view', 'finance.create',
            'announcements.view', 'announcements.create', 'announcements.edit',
            'news.view', 'news.create', 'news.edit',
            'reports.view', 'reports.export',
            'officers.view', 'sections.view',
        ];
        foreach ($secretaryPerms as $perm) {
            $db->execute(
                "INSERT INTO role_permissions (role_id, permission_id)
                SELECT 3, id FROM permissions WHERE name = :perm"
            , ['perm' => $perm]);
        }
    }
}
