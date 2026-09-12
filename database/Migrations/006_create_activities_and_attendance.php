<?php

declare(strict_types=1);

namespace Database\Migrations;

use App\Core\Database;

class CreateActivitiesAndAttendance
{
    public function up(Database $db): void
    {
        // Activities
        $db->execute("
            CREATE TABLE activities (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(200) NOT NULL,
                description TEXT,
                date DATE NOT NULL,
                start_time TIME,
                end_time TIME,
                location VARCHAR(255),
                section_id INTEGER REFERENCES sections(id) ON DELETE SET NULL,
                officer_in_charge INTEGER REFERENCES members(id) ON DELETE SET NULL,
                status VARCHAR(20) DEFAULT 'Scheduled' CHECK (status IN ('Scheduled', 'In Progress', 'Completed', 'Cancelled')),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $db->execute("CREATE INDEX idx_activities_date ON activities(date)");
        $db->execute("CREATE INDEX idx_activities_section ON activities(section_id)");
        $db->execute("CREATE INDEX idx_activities_status ON activities(status)");

        // Attendance Sessions
        $db->execute("
            CREATE TABLE attendance_sessions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                activity_id INTEGER REFERENCES activities(id) ON DELETE CASCADE,
                date DATE NOT NULL,
                section_id INTEGER REFERENCES sections(id) ON DELETE SET NULL,
                recorded_by INTEGER REFERENCES users(id) ON DELETE SET NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $db->execute("CREATE INDEX idx_attendance_sessions_activity ON attendance_sessions(activity_id)");

        // Attendance records
        $db->execute("
            CREATE TABLE attendance (
                id INT AUTO_INCREMENT PRIMARY KEY,
                session_id INTEGER REFERENCES attendance_sessions(id) ON DELETE CASCADE,
                member_id INTEGER REFERENCES members(id) ON DELETE CASCADE,
                status VARCHAR(20) DEFAULT 'Present' CHECK (status IN ('Present', 'Absent', 'Excused', 'Late')),
                notes TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE(session_id, member_id)
            )
        ");

        $db->execute("CREATE INDEX idx_attendance_session ON attendance(session_id)");
        $db->execute("CREATE INDEX idx_attendance_member ON attendance(member_id)");
    }
}
