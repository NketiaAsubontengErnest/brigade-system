<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Base Model class.
 */
class Model
{
    protected Database $db;
    protected string $table = '';
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $hidden = ['password'];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Find by primary key.
     */
    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        return $this->db->fetchOne($sql, ['id' => $id]);
    }

    /**
     * Find or fail.
     */
    public function findOrFail(int $id): array
    {
        $record = $this->find($id);
        if (!$record) {
            throw new \RuntimeException("Record not found in {$this->table}");
        }
        return $record;
    }

    /**
     * Find by a column value.
     */
    public function findBy(string $column, mixed $value): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = :value";
        return $this->db->fetchOne($sql, ['value' => $value]);
    }

    /**
     * Get all records.
     */
    public function all(array $orderBy = ['id' => 'ASC']): array
    {
        $orderClause = $this->buildOrderBy($orderBy);
        $sql = "SELECT * FROM {$this->table} {$orderClause}";
        return $this->db->fetchAll($sql);
    }

    /**
     * Create a new record.
     */
    public function create(array $data): int
    {
        $data['created_at'] = $data['created_at'] ?? date('Y-m-d H:i:s');
        $data['updated_at'] = $data['updated_at'] ?? date('Y-m-d H:i:s');

        // Filter only fillable fields
        $data = $this->filterFillable($data);

        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $this->db->execute($sql, $data);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Update a record.
     */
    public function update(int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        // Filter only fillable fields
        $data = $this->filterFillable($data);

        $setClauses = [];
        $bindings = ['id' => $id];

        foreach ($data as $column => $value) {
            $setClauses[] = "{$column} = :{$column}";
            $bindings[$column] = $value;
        }

        $setClause = implode(', ', $setClauses);
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = :id";

        $stmt = $this->db->execute($sql, $bindings);
        return $this->db->rowCount($stmt) > 0;
    }

    /**
     * Delete a record.
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->execute($sql, ['id' => $id]);
        return $this->db->rowCount($stmt) > 0;
    }

    /**
     * Count records.
     */
    public function count(string $where = '1=1', array $params = []): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE {$where}";
        $result = $this->db->fetchOne($sql, $params);
        return (int)($result['count'] ?? 0);
    }

    /**
     * Paginate results.
     */
    public function paginate(
        int $perPage = 15,
        int $page = 1,
        string $where = '1=1',
        array $params = [],
        array $orderBy = ['id' => 'DESC']
    ): array {
        $total = $this->count($where, $params);
        $totalPages = max(1, (int)ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;

        $orderClause = $this->buildOrderBy($orderBy);
        $sql = "SELECT * FROM {$this->table} WHERE {$where} {$orderClause} LIMIT :limit OFFSET :offset";

        $params['limit'] = $perPage;
        $params['offset'] = $offset;

        $results = $this->db->fetchAll($sql, $params);

        return [
            'data' => $results,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'has_prev' => $page > 1,
            'has_next' => $page < $totalPages,
        ];
    }

    /**
     * Search records.
     */
    public function search(string $query, array $columns, string $where = '1=1', array $params = []): array
    {
        // Native prepared statements (ATTR_EMULATE_PREPARES is off) reject a
        // named placeholder used more than once, so each column gets its own.
        $searchConditions = [];
        foreach (array_values($columns) as $i => $column) {
            $searchConditions[] = "LOWER({$column}) LIKE LOWER(:search{$i})";
            $params["search{$i}"] = "%{$query}%";
        }
        $searchClause = implode(' OR ', $searchConditions);
        $fullWhere = "({$searchClause}) AND {$where}";

        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE {$fullWhere} ORDER BY {$this->primaryKey} DESC",
            $params
        );
    }

    /**
     * Check if a record exists.
     */
    public function exists(string $where, array $params = []): bool
    {
        $sql = "SELECT 1 FROM {$this->table} WHERE {$where} LIMIT 1";
        return $this->db->fetchOne($sql, $params) !== null;
    }

    /**
     * Begin a database transaction.
     */
    public function beginTransaction(): bool
    {
        return $this->db->beginTransaction();
    }

    /**
     * Commit the transaction.
     */
    public function commit(): bool
    {
        return $this->db->commit();
    }

    /**
     * Rollback the transaction.
     */
    public function rollBack(): bool
    {
        return $this->db->rollBack();
    }

    /**
     * Filter data to only fillable fields.
     */
    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }
        return array_intersect_key($data, array_flip($this->fillable));
    }

    /**
     * Build an ORDER BY clause.
     */
    protected function buildOrderBy(array $orderBy): string
    {
        if (empty($orderBy)) {
            return '';
        }
        $clauses = [];
        foreach ($orderBy as $column => $direction) {
            $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
            $clauses[] = "{$column} {$direction}";
        }
        return 'ORDER BY ' . implode(', ', $clauses);
    }

    /**
     * Raw query.
     */
    protected function raw(string $sql, array $params = []): array
    {
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Raw query - fetch one.
     */
    protected function rawOne(string $sql, array $params = []): ?array
    {
        return $this->db->fetchOne($sql, $params);
    }

    /**
     * Execute raw SQL.
     */
    protected function rawExecute(string $sql, array $params = []): \PDOStatement
    {
        return $this->db->execute($sql, $params);
    }
}
