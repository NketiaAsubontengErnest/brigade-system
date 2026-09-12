<?php

declare(strict_types=1);

namespace Database\Migrations;

use App\Core\Database;

class CreateTrainingBadgesAwards
{
    public function up(Database $db): void
    {
        // Training Courses
        $db->execute("
            CREATE TABLE training_courses (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(200) NOT NULL,
                description TEXT,
                duration VARCHAR(100),
                instructor VARCHAR(200),
                status VARCHAR(20) DEFAULT 'Active' CHECK (status IN ('Active', 'Completed', 'Inactive')),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Training Enrollments
        $db->execute("
            CREATE TABLE training_enrollments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                member_id INTEGER REFERENCES members(id) ON DELETE CASCADE,
                course_id INTEGER REFERENCES training_courses(id) ON DELETE CASCADE,
                start_date DATE DEFAULT CURRENT_DATE,
                completion_date DATE,
                score DECIMAL(5,2),
                status VARCHAR(20) DEFAULT 'Enrolled' CHECK (status IN ('Enrolled', 'In Progress', 'Completed', 'Failed')),
                notes TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE(member_id, course_id)
            )
        ");

        $db->execute("CREATE INDEX idx_training_enrollments_member ON training_enrollments(member_id)");
        $db->execute("CREATE INDEX idx_training_enrollments_course ON training_enrollments(course_id)");

        // Badges
        $db->execute("
            CREATE TABLE badges (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(200) NOT NULL,
                description TEXT,
                requirements TEXT,
                image VARCHAR(500),
                status VARCHAR(20) DEFAULT 'Active' CHECK (status IN ('Active', 'Inactive')),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Member Badges
        $db->execute("
            CREATE TABLE member_badges (
                id INT AUTO_INCREMENT PRIMARY KEY,
                member_id INTEGER REFERENCES members(id) ON DELETE CASCADE,
                badge_id INTEGER REFERENCES badges(id) ON DELETE CASCADE,
                date_awarded DATE DEFAULT CURRENT_DATE,
                awarded_by INTEGER REFERENCES members(id) ON DELETE SET NULL,
                notes TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE(member_id, badge_id)
            )
        ");

        $db->execute("CREATE INDEX idx_member_badges_member ON member_badges(member_id)");
        $db->execute("CREATE INDEX idx_member_badges_badge ON member_badges(badge_id)");

        // Awards
        $db->execute("
            CREATE TABLE awards (
                id INT AUTO_INCREMENT PRIMARY KEY,
                member_id INTEGER REFERENCES members(id) ON DELETE CASCADE,
                name VARCHAR(200) NOT NULL,
                description TEXT,
                date_awarded DATE DEFAULT CURRENT_DATE,
                awarded_by VARCHAR(200),
                notes TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $db->execute("CREATE INDEX idx_awards_member ON awards(member_id)");
    }
}
