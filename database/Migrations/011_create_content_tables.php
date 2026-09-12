<?php

declare(strict_types=1);

namespace Database\Migrations;

use App\Core\Database;

class CreateContentTables
{
    public function up(Database $db): void
    {
        // Announcements
        $db->execute("
            CREATE TABLE IF NOT EXISTS announcements (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                content TEXT NOT NULL,
                publish_date DATE DEFAULT CURRENT_DATE,
                expiry_date DATE,
                is_public TINYINT(1) DEFAULT 0,
                status VARCHAR(20) DEFAULT 'Published' CHECK (status IN ('Draft', 'Published', 'Archived')),
                created_by INTEGER REFERENCES users(id) ON DELETE SET NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        try { $db->execute("CREATE INDEX idx_announcements_status ON announcements(status)"); } catch (\Throwable $e) {}
        try { $db->execute("CREATE INDEX idx_announcements_date ON announcements(publish_date)"); } catch (\Throwable $e) {}

        // News
        $db->execute("
            CREATE TABLE IF NOT EXISTS news (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL UNIQUE,
                content TEXT NOT NULL,
                featured_image VARCHAR(500),
                author INTEGER REFERENCES users(id) ON DELETE SET NULL,
                published_date DATE DEFAULT CURRENT_DATE,
                status VARCHAR(20) DEFAULT 'Draft' CHECK (status IN ('Draft', 'Published', 'Archived')),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        try { $db->execute("CREATE INDEX idx_news_slug ON news(slug)"); } catch (\Throwable $e) {}
        try { $db->execute("CREATE INDEX idx_news_status ON news(status)"); } catch (\Throwable $e) {}

        // Gallery Albums
        $db->execute("
            CREATE TABLE IF NOT EXISTS gallery_albums (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(200) NOT NULL,
                description TEXT,
                cover_image VARCHAR(500),
                status VARCHAR(20) DEFAULT 'Active' CHECK (status IN ('Active', 'Archived')),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Gallery Images
        $db->execute("
            CREATE TABLE IF NOT EXISTS gallery_images (
                id INT AUTO_INCREMENT PRIMARY KEY,
                album_id INTEGER REFERENCES gallery_albums(id) ON DELETE CASCADE,
                file_path VARCHAR(500) NOT NULL,
                caption VARCHAR(500),
                uploaded_by INTEGER REFERENCES users(id) ON DELETE SET NULL,
                sort_order INTEGER DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        try { $db->execute("CREATE INDEX idx_gallery_images_album ON gallery_images(album_id)"); } catch (\Throwable $e) {}

        // Documents
        $db->execute("
            CREATE TABLE IF NOT EXISTS documents (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                description TEXT,
                file_path VARCHAR(500) NOT NULL,
                file_type VARCHAR(50),
                file_size INTEGER,
                is_public TINYINT(1) DEFAULT 0,
                uploaded_by INTEGER REFERENCES users(id) ON DELETE SET NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Notifications
        $db->execute("
            CREATE TABLE IF NOT EXISTS notifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
                title VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                type VARCHAR(50) DEFAULT 'info',
                is_read TINYINT(1) DEFAULT 0,
                link VARCHAR(500),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        try { $db->execute("CREATE INDEX idx_notifications_user ON notifications(user_id)"); } catch (\Throwable $e) {}
        try { $db->execute("CREATE INDEX idx_notifications_read ON notifications(is_read)"); } catch (\Throwable $e) {}

        // Audit Logs
        $db->execute("
            CREATE TABLE IF NOT EXISTS audit_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
                action VARCHAR(100) NOT NULL,
                entity VARCHAR(100) NOT NULL,
                entity_id INTEGER,
                description TEXT,
                ip_address VARCHAR(45),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        try { $db->execute("CREATE INDEX idx_audit_logs_user ON audit_logs(user_id)"); } catch (\Throwable $e) {}
        try { $db->execute("CREATE INDEX idx_audit_logs_entity ON audit_logs(entity)"); } catch (\Throwable $e) {}
        try { $db->execute("CREATE INDEX idx_audit_logs_date ON audit_logs(created_at)"); } catch (\Throwable $e) {}
    }
}
