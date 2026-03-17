<?php

namespace App\Database\Seeders;

use WebApp\Database\Database;

/**
 *
 */
interface SeederInterface
{
    /**
     * @param Database $db
     * @return void
     */
    public function run(Database $db): void;
}

