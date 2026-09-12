<?php

declare(strict_types=1);

namespace Database\Migrations;

use App\Core\Database;

class CreateCompanyProfile
{
    public function up(Database $db): void
    {
        $db->execute("
            CREATE TABLE company_profile (
                id INT AUTO_INCREMENT PRIMARY KEY,
                company_name VARCHAR(255) DEFAULT '21st and 24th Accra Boys and Girls Brigade',
                church_name VARCHAR(255),
                company_number VARCHAR(50),
                motto VARCHAR(255),
                description TEXT,
                founded_date DATE,
                address TEXT,
                location VARCHAR(255),
                phone VARCHAR(30),
                email VARCHAR(255),
                website VARCHAR(255),
                logo VARCHAR(500),
                cover_image VARCHAR(500),
                mission TEXT,
                vision TEXT,
                member_number_prefix VARCHAR(10) DEFAULT 'BGB',
                currency_symbol VARCHAR(5) DEFAULT 'GH₵',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Insert default profile
        $db->execute("
            INSERT INTO company_profile (company_name, motto, description, mission, vision)
            VALUES (
                '21st and 24th Accra Boys and Girls Brigade',
                'Sure and Steadfast',
                'A Christian youth organization dedicated to the spiritual, physical, and mental development of young people through activities, training, and community service.',
                'To develop young people through Christian training, discipline, and service to God and community.',
                'To raise a generation of disciplined, God-fearing young people who are responsible citizens and future leaders.'
            )
        ");
    }
}
