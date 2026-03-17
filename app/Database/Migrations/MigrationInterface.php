<?php

namespace App\Database\Migrations;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Schema\Builder;

interface MigrationInterface
{
    public function up(Builder $schema, ConnectionInterface $db): void;

    public function down(Builder $schema, ConnectionInterface $db): void;
}

