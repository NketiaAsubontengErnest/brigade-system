<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Database connection singleton using PDO with MySQL.
 */
class Database
{
    private static ?Database $instance = null;
    private \PDO $pdo;

    private function __construct()
    {
        $primary = [
            'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
            'port' => $_ENV['DB_PORT'] ?? '3306',
            'database' => $_ENV['DB_DATABASE'] ?? 'brigade_system',
            'username' => $_ENV['DB_USERNAME'] ?? 'root',
            'password' => $_ENV['DB_PASSWORD'] ?? '',
        ];

        // Build list of configs to attempt (Primary first, then Fallbacks)
        $configs = [$primary];

        // 1. If primary host is remote/online, add local XAMPP as offline fallback
        if (!in_array(strtolower($primary['host']), ['127.0.0.1', 'localhost'])) {
            $configs[] = [
                'host' => '127.0.0.1',
                'port' => '3306',
                'database' => 'brigade_system',
                'username' => 'root',
                'password' => '',
            ];
        }

        // 2. If online DB credentials are specified, add online DB as secondary fallback
        if (!empty($_ENV['ONLINE_DB_HOST'])) {
            $configs[] = [
                'host' => $_ENV['ONLINE_DB_HOST'],
                'port' => $_ENV['ONLINE_DB_PORT'] ?? '3306',
                'database' => $_ENV['ONLINE_DB_DATABASE'] ?? '',
                'username' => $_ENV['ONLINE_DB_USERNAME'] ?? '',
                'password' => $_ENV['ONLINE_DB_PASSWORD'] ?? '',
            ];
        }

        $lastException = null;

        foreach ($configs as $cfg) {
            if (empty($cfg['host']) || empty($cfg['database'])) {
                continue;
            }

            $dsn = "mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['database']};charset=utf8mb4";
            $options = [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::ATTR_PERSISTENT => false,
                \PDO::ATTR_TIMEOUT => 4, // 4-second timeout per attempt
            ];

            try {
                $this->pdo = new \PDO($dsn, $cfg['username'], $cfg['password'], $options);
                return; // Successfully connected!
            } catch (\PDOException $e) {
                $lastException = $e;
            }
        }

        throw new \RuntimeException('Database connection failed: ' . ($lastException ? $lastException->getMessage() : 'No connection candidate succeeded.'));
    }

    /**
     * Prevent cloning.
     */
    private function __clone() {}

    /**
     * Prevent unserialization.
     */
    public function __wakeup()
    {
        throw new \RuntimeException('Cannot unserialize singleton');
    }

    /**
     * Get singleton instance.
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get the PDO connection.
     */
    public function getConnection(): \PDO
    {
        return $this->pdo;
    }

    /**
     * Begin a transaction.
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Commit a transaction.
     */
    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    /**
     * Rollback a transaction.
     */
    public function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }

    /**
     * Check if in transaction.
     */
    public function inTransaction(): bool
    {
        return $this->pdo->inTransaction();
    }

    /**
     * Execute a prepared statement with bindings.
     */
    public function execute(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch a single row.
     */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->execute($sql, $params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Fetch all rows.
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->execute($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Get last insert ID.
     */
    public function lastInsertId(string $name = ''): string|false
    {
        return $this->pdo->lastInsertId($name);
    }

    /**
     * Get row count from last statement.
     */
    public function rowCount(\PDOStatement $stmt): int
    {
        return $stmt->rowCount();
    }
}
