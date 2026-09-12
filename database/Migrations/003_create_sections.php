<?php

declare(strict_types=1);

namespace Database\Migrations;

use App\Core\Database;

class CreateSections
{
    public function up(Database $db): void
    {
        $db->execute("
            CREATE TABLE sections (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                description TEXT,
                age_range VARCHAR(50),
                status VARCHAR(20) DEFAULT 'Active' CHECK (status IN ('Active', 'Inactive')),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Insert default sections
        $sections = [
            ['Buds', 'Junior section for young children', '5-7'],
            ['Explorers', 'Junior section', '8-11'],
            ['Junior Brigade', 'Middle section', '12-14'],
            ['Senior Brigade', 'Senior section', '15-18'],
            ['Venturers', 'Young adult section', '19-25'],
        ];

        foreach ($sections as [$name, $desc, $age]) {
            $db->execute(
                "INSERT INTO sections (name, description, age_range) VALUES (:name, :desc, :age)",
                ['name' => $name, 'desc' => $desc, 'age' => $age]
            );
        }
    }
}
