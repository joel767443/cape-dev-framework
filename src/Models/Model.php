<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */

namespace WebApp\Models;

use WebApp\Database\Database;
use WebApp\Repositories\BaseRepository;

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
     * @return BaseRepository
     */
    protected static function repository(): BaseRepository
    {
        $table = static::$table;
        if ($table === '') {
            throw new \RuntimeException(static::class . " must define protected static string \$table");
        }

        return new BaseRepository($table);
    }

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
        return static::repository()->findById($id);
    }

    /**
     * @param array $where
     * @param string $orderBy
     * @return array
     */
    public static function all(array $where = [], string $orderBy = 'id DESC'): array
    {
        return static::repository()->findAll($where, $orderBy);
    }

    /**
     * Instance-style access (requested): $modelInstance->findAll(...)
     *
     * @param array $where
     * @param string $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function findAll(array $where = [], string $orderBy = 'id DESC', ?int $limit = null, ?int $offset = null): array
    {
        return static::repository()->findAll($where, $orderBy, $limit, $offset);
    }

    /**
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        return static::repository()->findById($id);
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
        return static::repository()->findByParameters($parameters, $orderBy, $limit, $offset);
    }

    /**
     * @param array $parameters
     * @param int $page
     * @param int $perPage
     * @param string $orderBy
     * @return array{data: array, pagination: array}
     */
    public function paginate(array $parameters = [], int $page = 1, int $perPage = 20, string $orderBy = 'id DESC'): array
    {
        return static::repository()->paginate($parameters, $page, $perPage, $orderBy);
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