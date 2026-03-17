<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
namespace WebApp\Database\Migrations;

use Illuminate\Database\ConnectionInterface;

/**
 *
 */
final class MigrationRepository
{
    /**
     * @param ConnectionInterface $db
     */
    public function __construct(private readonly ConnectionInterface $db)
    {
    }

    /**
     * @return void
     */
    public function ensureTable(): void
    {
        $this->db->statement(
            "CREATE TABLE IF NOT EXISTS migrations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                migration TEXT NOT NULL UNIQUE,
                batch INTEGER NOT NULL,
                ran_at TEXT NOT NULL
            )"
        );
    }

    /**
     * @return int
     */
    public function lastBatch(): int
    {
        $this->ensureTable();
        return (int) ($this->db->table('migrations')->max('batch') ?? 0);
    }

    /**
     * @return array<string, true>
     */
    public function appliedMigrations(): array
    {
        $this->ensureTable();
        $applied = [];
        foreach ($this->db->table('migrations')->select('migration')->get() as $row) {
            $name = (string) ($row->migration ?? '');
            if ($name !== '') {
                $applied[$name] = true;
            }
        }
        return $applied;
    }

    /**
     * @param string $migration
     * @param int $batch
     * @return void
     */
    public function log(string $migration, int $batch): void
    {
        $this->ensureTable();
        $this->db->table('migrations')->insert([
            'migration' => $migration,
            'batch' => $batch,
            'ran_at' => date('c'),
        ]);
    }
}

