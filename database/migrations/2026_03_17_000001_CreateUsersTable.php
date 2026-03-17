<?php

namespace App\Database\Migrations;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

final class CreateUsersTable implements MigrationInterface
{
    public function up(Builder $schema, ConnectionInterface $db): void
    {
        $schema->create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });
    }

    public function down(Builder $schema, ConnectionInterface $db): void
    {
        $schema->dropIfExists('users');
    }
}

