<?php

declare(strict_types=1);

namespace Database\Migrations;

use App\Core\Database;

class CreateMembers
{
    public function up(Database $db): void
    {
        $db->execute("
            CREATE TABLE members (
                id INT AUTO_INCREMENT PRIMARY KEY,
                member_number VARCHAR(30) UNIQUE,
                first_name VARCHAR(100) NOT NULL,
                middle_name VARCHAR(100),
                last_name VARCHAR(100) NOT NULL,
                date_of_birth DATE,
                gender VARCHAR(10) CHECK (gender IN ('Male', 'Female')),
                phone VARCHAR(30),
                email VARCHAR(255),
                address TEXT,
                profile_photo VARCHAR(500),
                date_joined DATE DEFAULT CURRENT_DATE,
                section_id INTEGER REFERENCES sections(id) ON DELETE SET NULL,
                rank VARCHAR(100),
                status VARCHAR(20) DEFAULT 'Pending' CHECK (status IN ('Active', 'Inactive', 'Suspended', 'Former', 'Pending')),
                notes TEXT,
                user_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
                verification_token VARCHAR(64) UNIQUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $db->execute("CREATE INDEX idx_members_number ON members(member_number)");
        $db->execute("CREATE INDEX idx_members_name ON members(last_name, first_name)");
        $db->execute("CREATE INDEX idx_members_section ON members(section_id)");
        $db->execute("CREATE INDEX idx_members_status ON members(status)");
        $db->execute("CREATE INDEX idx_members_gender ON members(gender)");

        // Guardians
        $db->execute("
            CREATE TABLE guardians (
                id INT AUTO_INCREMENT PRIMARY KEY,
                member_id INTEGER REFERENCES members(id) ON DELETE CASCADE,
                full_name VARCHAR(200) NOT NULL,
                relationship VARCHAR(100),
                phone VARCHAR(30),
                email VARCHAR(255),
                address TEXT,
                is_primary TINYINT(1) DEFAULT 0,
                emergency_contact TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $db->execute("CREATE INDEX idx_guardians_member ON guardians(member_id)");
    }
}
