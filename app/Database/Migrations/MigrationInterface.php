<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
namespace App\Database\Migrations;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Schema\Builder;

/**
 *
 */
interface MigrationInterface
{
    /**
     * @param Builder $schema
     * @param ConnectionInterface $db
     * @return void
     */
    public function up(Builder $schema, ConnectionInterface $db): void;

    /**
     * @param Builder $schema
     * @param ConnectionInterface $db
     * @return void
     */
    public function down(Builder $schema, ConnectionInterface $db): void;
}

