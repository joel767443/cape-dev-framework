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
     * @var array
     */
    public array $errors = [];
    /**
     *
     */
    public const RULE_REQUIRED = 'required';
    /**
     *
     */
    public const IS_INT = 'is int';
    /**
     *
     */
    public const IS_FLOAT = 'is float';
    /**
     *
     */
    public const IS_STRING = 'is string';

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
     * @return bool
     */
    public function validate(): bool
    {
        foreach ($this->rules() as $attribute => $rules) {

            $value = $this->$attribute;

            foreach ($rules as $rule) {

                $ruleName = $rule;

                if (!is_string($ruleName)) {
                    $ruleName = $rule[0];
                }

                if ($ruleName === self::RULE_REQUIRED && !$value) {
                    $this->addError($attribute, self::RULE_REQUIRED);
                }

                if ($ruleName === self::IS_INT && !is_int($value)) {
                    $this->addError($attribute, self::IS_INT);
                }
                if ($ruleName === self::IS_FLOAT && !is_float($value)) {
                    $this->addError($attribute, self::IS_FLOAT);
                }
                if ($ruleName === self::IS_STRING && !is_string($value)) {
                    $this->addError($attribute, self::IS_STRING);
                }
            }
        }

        return empty($this->errors);
    }

    /**
     * @return true
     */
    public function create()
    {
        $db = new Database();
        $values = (array)$this;
        unset($values["errors"]);
        unset($values["id"]);
        foreach ($values as $key => $value) {
            if ($value === null) {
                unset($values[$key]);
            }
        }
        $table = static::$table;
        if ($table === '') {
            throw new \RuntimeException(static::class . " must define protected static string \$table");
        }
        $db->create($table, $values);
        $db->close();
        return true;
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

        if (empty($data)) {
            return true;
        }

        $setParts = [];
        foreach ($data as $column => $value) {
            $setParts[] = "{$column} = :{$column}";
        }

        $params = $data;
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

    /**
     * @param string $attribute
     * @param string $rule
     * @return void
     */
    private function addError(string $attribute, string $rule)
    {
        $message = $this->errorMessages()[$rule] ?? '';

        $this->errors[$attribute][] = $message;
    }

    /**
     * @return string[]
     */
    public function errorMessages(): array
    {
        return [
            self::RULE_REQUIRED => 'This field is required.',
            self::IS_INT => 'This field must be an integer.',
            self::IS_FLOAT => 'This field must be a float.',
            self::IS_STRING => 'This field must be a string.',
        ];
    }
}