<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */

namespace WebApp\Database;
use PDO;
use PDOException;
use PDOStatement;

/**
 * Class Database
 */
class Database
{
    /**
     * @var PDO
     */
    private $pdo;

    /**
     *
     */
    public function __construct()
    {
        try {
            $dbPath = $this->resolveSqlitePath();
            $this->ensureDirectoryExists(dirname($dbPath));

            $this->pdo = new PDO('sqlite:' . $dbPath);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            $this->initializeIfNeeded();
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    /**
     * @return string
     */
    private function resolveSqlitePath(): string
    {
        // Default location: src/Database/cape-dev.sqlite
        $default = __DIR__ . DIRECTORY_SEPARATOR . 'cape-dev.sqlite';

        $sqlitePath = config('database.sqlitePath');
        if (!$sqlitePath) {
            return $default;
        }

        // Allow absolute paths; otherwise treat as project-root relative.
        if ($this->isAbsolutePath($sqlitePath)) {
            return $sqlitePath;
        }

        return dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . ltrim($sqlitePath, DIRECTORY_SEPARATOR);
    }

    /**
     * @param string $path
     * @return bool
     */
    private function isAbsolutePath(string $path): bool
    {
        if ($path === '') {
            return false;
        }

        // Unix-like absolute path
        if ($path[0] === DIRECTORY_SEPARATOR) {
            return true;
        }

        // Windows drive letter (e.g. C:\)
        return (bool) preg_match('/^[A-Za-z]:[\\\\\\/]/', $path);
    }

    /**
     * @param string $dir
     * @return void
     */
    private function ensureDirectoryExists(string $dir): void
    {
        if (is_dir($dir)) {
            return;
        }

        if (!mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new PDOException("Could not create database directory: {$dir}");
        }
    }

    /**
     * @return void
     */
    private function initializeIfNeeded(): void
    {
        // Our schema bootstrap defines `users` (not `items`) as a core table.
        // Use it to detect whether initialization has already run.
        if ($this->tableExists('users')) {
            return;
        }

        $sqlFile = __DIR__ . DIRECTORY_SEPARATOR . 'main.sqlite.sql';
        if (!is_file($sqlFile)) {
            throw new PDOException("SQLite init SQL file missing: {$sqlFile}");
        }

        $sql = file_get_contents($sqlFile);
        if ($sql === false) {
            throw new PDOException("Could not read SQLite init SQL file: {$sqlFile}");
        }

        $this->executeSqlStatements($sql);
    }

    /**
     * @param string $table
     * @return bool
     */
    private function tableExists(string $table): bool
    {
        $stmt = $this->pdo->prepare("SELECT name FROM sqlite_master WHERE type = 'table' AND name = :name LIMIT 1");
        $stmt->execute(['name' => $table]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Execute a SQL string that may contain multiple statements.
     *
     * @param string $sql
     * @return void
     */
    private function executeSqlStatements(string $sql): void
    {
        // Let SQLite parse the whole schema. This avoids breaking triggers that contain
        // internal semicolons inside BEGIN/END blocks.
        $this->pdo->exec($sql);
    }

    /**
     * @param $query
     * @param $params
     * @return false|PDOStatement
     */
    public function query($query, $params = [])
    {
        $statement = $this->pdo->prepare($query);
        $statement->execute($params);
        return $statement;
    }

    /**
     * @param $result
     * @return mixed
     */
    public function fetchAssoc($result)
    {
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param $table
     * @param $data
     * @return false|string
     */
    public function create($table, $data)
    {
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $query = "INSERT INTO $table ($columns) VALUES ($placeholders)";

        // Prepare the query
        $statement = $this->pdo->prepare($query);

        // Bind the values
        foreach ($data as $key => $value) {
            $statement->bindValue(":$key", $value);
        }

        // Execute the query
        try {
            $statement->execute();
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            die("Error executing the query: " . $e->getMessage());
        }
    }

    /**
     * @return void
     */
    public function close()
    {
        $this->pdo = null;
    }
}

