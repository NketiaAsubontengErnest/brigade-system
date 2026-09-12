<?php

declare(strict_types=1);

namespace Database\Migrations;

use App\Core\Database;

class CreateEvents
{
    public function up(Database $db): void
    {
        $db->execute("
            CREATE TABLE events (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(200) NOT NULL,
                description TEXT,
                start_date DATE NOT NULL,
                end_date DATE,
                start_time TIME,
                end_time TIME,
                location VARCHAR(255),
                registration_deadline DATE,
                maximum_participants INTEGER,
                fee DECIMAL(10,2) DEFAULT 0,
                cover_image VARCHAR(500),
                status VARCHAR(20) DEFAULT 'Upcoming' CHECK (status IN ('Upcoming', 'Ongoing', 'Completed', 'Cancelled')),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $db->execute("CREATE INDEX idx_events_date ON events(start_date)");
        $db->execute("CREATE INDEX idx_events_status ON events(status)");

        // Event registrations
        $db->execute("
            CREATE TABLE event_registrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                event_id INTEGER REFERENCES events(id) ON DELETE CASCADE,
                member_id INTEGER REFERENCES members(id) ON DELETE CASCADE,
                status VARCHAR(20) DEFAULT 'Registered' CHECK (status IN ('Registered', 'Attended', 'Cancelled')),
                registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE(event_id, member_id)
            )
        ");

        $db->execute("CREATE INDEX idx_event_registrations_event ON event_registrations(event_id)");
        $db->execute("CREATE INDEX idx_event_registrations_member ON event_registrations(member_id)");
    }
}
