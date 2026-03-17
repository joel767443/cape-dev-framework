<?php

namespace WebApp\Models;

use WebApp\Database\Database;

/**
 * class class
 */
abstract class Model
{
    /**
     * Override in child model classes (e.g. protected static string $table = 'users';)
     *
     * @var string
     */
    protected static string $table = '';

    /**
     * @return string[]
     */
    protected function attributes(): array
    {
        return array_keys(get_object_vars($this));
    }

    /**
     * @param $data
     * @return void
     */
    public function loadData($data)
    {
        foreach ($data as $property => $value) {
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }
    }

    /**
     * @return array
     */
    abstract public function rules(): array;

    /**
     * @return true
     */
    public function create()
    {
        $db = new Database();
        $values = [];
        foreach ($this->attributes() as $attribute) {
            if ($attribute === 'id') {
                continue;
            }
            $values[$attribute] = $this->{$attribute} ?? null;
        }
        foreach ($values as $key => $value) {
            if ($value === null) {
                unset($values[$key]);
            }
        }
        $table = static::$table;
        if ($table === '') {
            throw new \RuntimeException(static::class . " must define protected static string \$table");
        }
        $insertedId = $db->create($table, $values);
        $db->close();
        return $insertedId;
    }

    /**
     * @param int $id
     * @return mixed|null
     */
    public static function find(int $id)
    {
        $table = static::$table;
        if ($table === '') {
            throw new \RuntimeException(static::class . " must define protected static string \$table");
        }

        $db = new Database();
        $stmt = $db->query("SELECT * FROM {$table} WHERE id = :id", ['id' => $id]);
        $row = $db->fetchAssoc($stmt);
        $db->close();

        return $row ?: null;
    }

    /**
     * @param array $where
     * @param string $orderBy
     * @return array
     */
    public static function all(array $where = [], string $orderBy = 'id DESC'): array
    {
        $table = static::$table;
        if ($table === '') {
            throw new \RuntimeException(static::class . " must define protected static string \$table");
        }

        $sql = "SELECT * FROM {$table}";
        $params = [];

        if (!empty($where)) {
            $clauses = [];
            foreach ($where as $column => $value) {
                $param = "w_" . $column;
                $clauses[] = "{$column} = :{$param}";
                $params[$param] = $value;
            }
            $sql .= " WHERE " . implode(" AND ", $clauses);
        }

        if ($orderBy !== '') {
            if (!preg_match('/^[A-Za-z0-9_]+(\\s+(ASC|DESC))?$/i', $orderBy)) {
                throw new \InvalidArgumentException("Invalid orderBy value.");
            }
            $sql .= " ORDER BY {$orderBy}";
        }

        $db = new Database();
        $stmt = $db->query($sql, $params);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $db->close();

        return $rows;
    }

    /**
     * @param array $data
     * @return bool
     */
    public static function update(array $data): bool
    {
        $table = static::$table;
        if ($table === '') {
            throw new \RuntimeException(static::class . " must define protected static string \$table");
        }

        if (!isset($data['id'])) {
            return false;
        }

        $existing = static::find((int)$data['id']);
        if ($existing === null) {
            return false;
        }

        $id = $data['id'];
        unset($data['id']);
        unset($data['errors']);

        // Filter to known model attributes only (ignore unknown keys)
        $instance = new static();
        $allowed = array_flip($instance->attributes());
        $filtered = [];
        foreach ($data as $key => $value) {
            if (!isset($allowed[$key])) {
                continue;
            }
            if ($value === null) {
                continue;
            }
            $filtered[$key] = $value;
        }

        if (empty($filtered)) {
            return true;
        }

        $setParts = [];
        foreach ($filtered as $column => $value) {
            $setParts[] = "{$column} = :{$column}";
        }

        $params = $filtered;
        $params['id'] = $id;

        $sql = "UPDATE {$table} SET " . implode(", ", $setParts) . " WHERE id = :id";

        $db = new Database();
        $db->query($sql, $params);
        $db->close();

        return true;
    }

    /**
     * @param int $id
     * @return bool
     */
    public static function delete(int $id): bool
    {
        $table = static::$table;
        if ($table === '') {
            throw new \RuntimeException(static::class . " must define protected static string \$table");
        }

        $db = new Database();
        $db->query("DELETE FROM {$table} WHERE id = :id", ['id' => $id]);
        $db->close();
        return true;
    }
}