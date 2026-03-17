<?php

namespace App\Database\Seeders;

use WebApp\Database\Database;

interface SeederInterface
{
    public function run(Database $db): void;
}

