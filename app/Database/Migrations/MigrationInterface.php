<?php

namespace App\Database\Migrations;

use WebApp\Database\Database;

interface MigrationInterface
{
    public function up(Database $db): void;

    public function down(Database $db): void;
}

