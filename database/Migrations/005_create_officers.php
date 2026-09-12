<?php

declare(strict_types=1);

namespace Database\Migrations;

use App\Core\Database;

class CreateOfficers
{
    public function up(Database $db): void
    {
        $db->execute("
            CREATE TABLE positions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL UNIQUE,
                description TEXT,
                status VARCHAR(20) DEFAULT 'Active' CHECK (status IN ('Active', 'Inactive')),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $db->execute("
            CREATE TABLE officers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                member_id INTEGER REFERENCES members(id) ON DELETE CASCADE,
                position_id INTEGER REFERENCES positions(id) ON DELETE SET NULL,
                start_date DATE DEFAULT CURRENT_DATE,
                end_date DATE,
                status VARCHAR(20) DEFAULT 'Active' CHECK (status IN ('Active', 'Inactive')),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $db->execute("CREATE INDEX idx_officers_member ON officers(member_id)");
        $db->execute("CREATE INDEX idx_officers_position ON officers(position_id)");
        $db->execute("CREATE INDEX idx_officers_status ON officers(status)");

        // Insert default positions
        $positions = [
            ['Captain', 'Company commander'],
            ['Vice Captain', 'Deputy company commander'],
            ['Secretary', 'Administrative officer'],
            ['Assistant Secretary', 'Deputy administrative officer'],
            ['Treasurer', 'Financial officer'],
            ['Assistant Treasurer', 'Deputy financial officer'],
            ['Chaplain', 'Spiritual leader'],
            ['Training Officer', 'Training coordinator'],
            ['Assistant Training Officer', 'Deputy training coordinator'],
            ['Welfare Officer', 'Member welfare'],
            ['Public Relations Officer', 'External communications'],
        ];

        foreach ($positions as [$name, $desc]) {
            $db->execute(
                "INSERT INTO positions (name, description) VALUES (:name, :desc)",
                ['name' => $name, 'desc' => $desc]
            );
        }
    }
}
