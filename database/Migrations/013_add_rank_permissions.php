<?php

declare(strict_types=1);

namespace Database\Migrations;

use App\Core\Database;

/**
 * Ranks are maintained by the Super Admin and the Captain. Previously the
 * ranks screen sat behind settings.view/settings.edit, which only the Super
 * Admin holds - so a Captain could not add a rank. These dedicated
 * permissions let the Captain manage ranks without opening up the rest of
 * the settings area (company profile, audit logs, and so on).
 */
class AddRankPermissions
{
    public function up(Database $db): void
    {
        foreach ([
            ['ranks.view', 'View the list of member ranks'],
            ['ranks.manage', 'Create, edit and remove member ranks'],
        ] as [$name, $description]) {
            $db->execute(
                "INSERT INTO permissions (name, description) VALUES (:name, :description)
                 ON DUPLICATE KEY UPDATE description = VALUES(description)",
                ['name' => $name, 'description' => $description]
            );
        }

        // Super Admin (1) and Captain (2)
        foreach ([1, 2] as $roleId) {
            $db->execute(
                "INSERT IGNORE INTO role_permissions (role_id, permission_id)
                 SELECT :role, id FROM permissions WHERE name IN ('ranks.view', 'ranks.manage')",
                ['role' => $roleId]
            );
        }

        // Guardians are optional, so make sure the columns tolerate empty input
        foreach ([
            "ALTER TABLE guardians MODIFY relationship VARCHAR(100) NULL",
            "ALTER TABLE guardians MODIFY phone VARCHAR(30) NULL",
            "ALTER TABLE guardians MODIFY email VARCHAR(255) NULL",
        ] as $sql) {
            try {
                $db->execute($sql);
            } catch (\Throwable $e) {
                // Column already nullable
            }
        }

        // Keep a member's guardians together for the member edit screen
        try {
            $db->execute("CREATE INDEX idx_guardians_member_primary ON guardians(member_id, is_primary)");
        } catch (\Throwable $e) {
            // Index already exists
        }
    }
}
