<?php

declare(strict_types=1);

namespace Database\Migrations;

use App\Core\Database;

class CreateRanksTable
{
    public function up(Database $db): void
    {
        // Create ranks table
        $db->execute("
            CREATE TABLE IF NOT EXISTS ranks (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL UNIQUE,
                description TEXT,
                level INT DEFAULT 1,
                status VARCHAR(20) DEFAULT 'Active' CHECK (status IN ('Active', 'Inactive')),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Insert default ranks
        $ranks = [
            ['Rifleman', 'Basic rank', 1],
            ['Lance Corporal', 'First promotion', 2],
            ['Corporal', 'Non-commissioned officer', 3],
            ['Sergeant', 'Senior NCO', 4],
            ['Staff Sergeant', 'Senior staff NCO', 5],
            ['Warrant Officer', 'Warrant officer rank', 6],
        ];

        foreach ($ranks as [$name, $desc, $level]) {
            $db->execute(
                "INSERT INTO ranks (name, description, level) VALUES (:name, :desc, :level) ON DUPLICATE KEY UPDATE name = name",
                ['name' => $name, 'desc' => $desc, 'level' => $level]
            );
        }

        // Add type column to sections if it doesn't exist
        try {
            $db->execute("ALTER TABLE sections ADD COLUMN type VARCHAR(50) DEFAULT 'Section'");
        } catch (\Throwable $e) {
            // Column may already exist
        }

        // Add is_officer column to members if it doesn't exist
        try {
            $db->execute("ALTER TABLE members ADD COLUMN is_officer TINYINT(1) DEFAULT 0");
        } catch (\Throwable $e) {
            // Column may already exist
        }

        // Add position_id to members if it doesn't exist
        try {
            $db->execute("ALTER TABLE members ADD COLUMN position_id INT REFERENCES positions(id) ON DELETE SET NULL");
        } catch (\Throwable $e) {
            // Column may already exist
        }

        // Add rank_id to members if it doesn't exist
        try {
            $db->execute("ALTER TABLE members ADD COLUMN rank_id INT REFERENCES ranks(id) ON DELETE SET NULL");
        } catch (\Throwable $e) {
            // Column may already exist
        }

        // Update existing sections with types
        $db->execute("UPDATE sections SET type = 'Junior' WHERE name IN ('Buds', 'Explorers', 'Junior Brigade')");
        $db->execute("UPDATE sections SET type = 'Section' WHERE name IN ('Senior Brigade', 'Venturers')");

        // Update company name
        $db->execute("UPDATE company_profile SET company_name = '21st and 24th Accra Boys and Girls Brigade' WHERE company_name != '21st and 24th Accra Boys and Girls Brigade'");
    }
}
