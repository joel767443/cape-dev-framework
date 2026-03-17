<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */

namespace WebApp\Repositories;

use InvalidArgumentException;
use PDO;
use WebApp\Database\Database;

/**
 *
 */
class BaseRepository
{
    protected string $table;

    /**
     * @param string $table
     */
    public function __construct(string $table)
    {
        if ($table === '') {
            throw new InvalidArgumentException("Table name is required.");
        }
        $this->table = $table;
    }

    /**
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        $db = new Database();
        $stmt = $db->query("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1", ['id' => $id]);
        $row = $db->fetchAssoc($stmt);
        $db->close();
        return $row ?: null;
    }

    /**
     * Alias for findById.
     *
     * @param int $id
     * @return array|null
     */
    public function find(int $id): ?array
    {
        return $this->findById($id);
    }

    /**
     * @param array $parameters
     * @param string $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return array|null
     */
    public function findByParameters(array $parameters, string $orderBy = 'id DESC', ?int $limit = null, ?int $offset = null): ?array
    {
        $rows = $this->findAll($parameters, $orderBy, $limit ?? 1, $offset);
        return $rows[0] ?? null;
    }

    /**
     * @param array $parameters
     * @param string $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function findAll(array $parameters = [], string $orderBy = 'id DESC', ?int $limit = null, ?int $offset = null): array
    {
        [$whereSql, $params] = $this->buildWhere($parameters);

        $sql = "SELECT * FROM {$this->table}{$whereSql}";
        $sql .= $this->buildOrderBy($orderBy);
        $sql .= $this->buildLimitOffset($limit, $offset);

        $db = new Database();
        $stmt = $db->query($sql, $params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db->close();

        return $rows;
    }

    /**
     * Pagination helper.
     *
     * @param array $parameters
     * @param int $page 1-based
     * @param int $perPage
     * @param string $orderBy
     * @return array{data: array, pagination: array}
     */
    public function paginate(array $parameters = [], int $page = 1, int $perPage = 20, string $orderBy = 'id DESC'): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        $total = $this->count($parameters);
        $data = $this->findAll($parameters, $orderBy, $perPage, $offset);

        return [
            'data' => $data,
            'pagination' => [
                'page' => $page,
                'perPage' => $perPage,
                'total' => $total,
                'totalPages' => (int) ceil($total / $perPage),
            ],
        ];
    }

    /**
     * @param array $parameters
     * @return int
     */
    public function count(array $parameters = []): int
    {
        [$whereSql, $params] = $this->buildWhere($parameters);
        $sql = "SELECT COUNT(*) AS cnt FROM {$this->table}{$whereSql}";

        $db = new Database();
        $stmt = $db->query($sql, $params);
        $row = $db->fetchAssoc($stmt);
        $db->close();

        return (int)($row['cnt'] ?? 0);
    }

    /**
     * @param string $orderBy
     * @return string
     */
    protected function buildOrderBy(string $orderBy): string
    {
        if ($orderBy === '') {
            return '';
        }

        // Keep this very strict; prevents accidental SQL injection.
        if (!preg_match('/^[A-Za-z0-9_]+(\\s+(ASC|DESC))?$/i', $orderBy)) {
            throw new InvalidArgumentException("Invalid orderBy value.");
        }

        return " ORDER BY {$orderBy}";
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return string
     */
    protected function buildLimitOffset(?int $limit, ?int $offset): string
    {
        $sql = '';

        if ($limit !== null) {
            $sql .= " LIMIT " . max(0, $limit);
        }
        if ($offset !== null) {
            if ($limit === null) {
                // SQLite requires LIMIT when OFFSET is present; use a very large limit.
                $sql .= " LIMIT 9223372036854775807";
            }
            $sql .= " OFFSET " . max(0, $offset);
        }

        return $sql;
    }

    /**
     * Supports:
     * - ['column' => 'value']  -> column = :p_column
     * - ['column' => [1,2,3]]  -> column IN (:p_column_0, :p_column_1, ...)
     *
     * @param array $parameters
     * @return array{0: string, 1: array}
     */
    protected function buildWhere(array $parameters): array
    {
        if (empty($parameters)) {
            return ['', []];
        }

        $clauses = [];
        $params = [];

        foreach ($parameters as $column => $value) {
            if (!preg_match('/^[A-Za-z0-9_]+$/', (string)$column)) {
                throw new InvalidArgumentException("Invalid where column.");
            }

            if (is_array($value)) {
                if (empty($value)) {
                    $clauses[] = "1 = 0";
                    continue;
                }
                $inParts = [];
                foreach (array_values($value) as $i => $v) {
                    $key = "p_{$column}_{$i}";
                    $inParts[] = ':' . $key;
                    $params[$key] = $v;
                }
                $clauses[] = "{$column} IN (" . implode(', ', $inParts) . ")";
                continue;
            }

            $key = "p_{$column}";
            $clauses[] = "{$column} = :{$key}";
            $params[$key] = $value;
        }

        return [' WHERE ' . implode(' AND ', $clauses), $params];
    }
}

