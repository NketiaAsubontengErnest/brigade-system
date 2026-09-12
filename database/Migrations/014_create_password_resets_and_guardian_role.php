<?php

declare(strict_types=1);

namespace Database\Migrations;

use App\Core\Database;

class CreatePasswordResetsAndGuardianRole
{
    public function up(Database $db): void
    {
        // 1. Password resets table
        $db->execute("
            CREATE TABLE IF NOT EXISTS password_resets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL,
                token VARCHAR(100) NOT NULL,
                expires_at TIMESTAMP NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        try {
            $db->execute("CREATE INDEX idx_password_resets_email ON password_resets(email)");
            $db->execute("CREATE INDEX idx_password_resets_token ON password_resets(token)");
        } catch (\Throwable $e) {
            // Indexes may already exist
        }

        // 2. Add user_id column to guardians if not exists
        try {
            $db->execute("ALTER TABLE guardians ADD COLUMN user_id INTEGER REFERENCES users(id) ON DELETE SET NULL");
        } catch (\Throwable $e) {
            // Column may already exist
        }

        try {
            $db->execute("CREATE INDEX idx_guardians_user ON guardians(user_id)");
        } catch (\Throwable $e) {
            // Index may already exist
        }

        // 3. Ensure Guardian role exists in roles table
        $guardianRole = $db->fetchOne("SELECT id FROM roles WHERE LOWER(name) = 'guardian'");
        if (!$guardianRole) {
            $db->execute(
                "INSERT INTO roles (name, description) VALUES (:name, :desc)",
                ['name' => 'Guardian', 'desc' => 'Parent/Guardian of brigade members']
            );
        }
    }
}
