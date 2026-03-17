<?php

namespace WebApp\Database\Migrations;

use WebApp\Database\Database;

final class MigrationRepository
{
    public function __construct(private readonly Database $db)
    {
    }

    public function ensureTable(): void
    {
        $this->db->pdo()->exec(
            "CREATE TABLE IF NOT EXISTS migrations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                migration TEXT NOT NULL UNIQUE,
                batch INTEGER NOT NULL,
                ran_at TEXT NOT NULL
            )"
        );
    }

    public function lastBatch(): int
    {
        $this->ensureTable();
        $stmt = $this->db->query("SELECT MAX(batch) AS b FROM migrations");
        $row = $stmt ? $stmt->fetch(\PDO::FETCH_ASSOC) : false;
        return (int) ($row['b'] ?? 0);
    }

    /**
     * @return array<string, true>
     */
    public function appliedMigrations(): array
    {
        $this->ensureTable();
        $stmt = $this->db->query("SELECT migration FROM migrations");
        $applied = [];
        if ($stmt) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $name = (string) ($row['migration'] ?? '');
                if ($name !== '') {
                    $applied[$name] = true;
                }
            }
        }
        return $applied;
    }

    public function log(string $migration, int $batch): void
    {
        $this->ensureTable();
        $this->db->query(
            "INSERT INTO migrations (migration, batch, ran_at) VALUES (:migration, :batch, :ran_at)",
            [
                'migration' => $migration,
                'batch' => $batch,
                'ran_at' => date('c'),
            ]
        );
    }
}

