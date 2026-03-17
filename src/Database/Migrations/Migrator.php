<?php

namespace WebApp\Database\Migrations;

use App\Database\Migrations\MigrationInterface;
use WebApp\Database\Database;

final class Migrator
{
    public function __construct(
        private readonly Database $db,
        private readonly MigrationRepository $repo
    ) {
    }

    /**
     * @param array<string, class-string<MigrationInterface>> $available
     * @return array<int, array{name: string, class: class-string<MigrationInterface>}>
     */
    public function pending(array $available): array
    {
        $applied = $this->repo->appliedMigrations();
        $pending = [];

        foreach ($available as $name => $class) {
            if (isset($applied[$name])) {
                continue;
            }
            $pending[] = ['name' => $name, 'class' => $class];
        }

        return $pending;
    }

    /**
     * @param array<int, array{name: string, class: class-string<MigrationInterface>}> $pending
     */
    public function run(array $pending): int
    {
        $this->repo->ensureTable();
        if ($pending === []) {
            return 0;
        }

        $batch = $this->repo->lastBatch() + 1;
        $ran = 0;

        foreach ($pending as $m) {
            $name = $m['name'];
            $class = $m['class'];

            /** @var MigrationInterface $migration */
            $migration = new $class();

            $this->db->pdo()->beginTransaction();
            try {
                $migration->up($this->db);
                $this->repo->log($name, $batch);
                $this->db->pdo()->commit();
                $ran++;
            } catch (\Throwable $e) {
                $this->db->pdo()->rollBack();
                throw $e;
            }
        }

        return $ran;
    }
}

