<?php

declare(strict_types=1);

namespace Database;

use App\Core\Database;

/**
 * Migration runner.
 */
class MigrationRunner
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Create the migrations tracking table.
     */
    public function createMigrationsTable(): void
    {
        $this->db->execute("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL UNIQUE,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    /**
     * Get list of already-run migrations.
     */
    public function getRan(): array
    {
        $results = $this->db->fetchAll("SELECT name FROM migrations ORDER BY id");
        return array_column($results, 'name');
    }

    /**
     * Run all pending migrations.
     */
    public function run(): void
    {
        $this->createMigrationsTable();
        $ran = $this->getRan();

        $migrationsDir = __DIR__ . '/Migrations';
        $files = glob($migrationsDir . '/*.php');
        sort($files);

        foreach ($files as $file) {
            $name = basename($file);
            if (in_array($name, $ran)) {
                continue;
            }

            echo "Running migration: {$name}\n";

            require_once $file;

            $baseName = pathinfo($name, PATHINFO_FILENAME);
            $baseName = preg_replace('/^\d+_/', '', $baseName);
            $className = str_replace(' ', '', ucwords(str_replace('_', ' ', $baseName)));
            $class = 'Database\\Migrations\\' . $className;
            $migration = new $class();
            $migration->up($this->db);

            $this->db->execute(
                "INSERT INTO migrations (name) VALUES (:name)",
                ['name' => $name]
            );

            echo "  ✓ Completed\n";
        }
    }

    /**
     * Run all database seeders.
     */
    public function seed(): void
    {
        $seedersDir = __DIR__ . '/Seeders';
        $files = glob($seedersDir . '/*.php');
        sort($files);

        foreach ($files as $file) {
            $name = basename($file);
            echo "Running seeder: {$name}\n";

            require_once $file;

            $baseName = pathinfo($name, PATHINFO_FILENAME);
            $className = str_replace(' ', '', ucwords(str_replace('_', ' ', $baseName)));
            $class = 'Database\\Seeders\\' . $className;
            $seeder = new $class();
            $seeder->run($this->db);

            echo "  ✓ Seeded\n";
        }
    }
}
