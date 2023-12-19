<?php
namespace App\Lib\Database\Interface;

use \App\Lib\Entity\Entity;

/**
 * 
 */
interface MigrationsInterface
{
    /**
     * Creates table in default or specified database from entity class properties
     *
     * @param  \App\Lib\Entity\Entity $entity
     * @param  string                 $dbname
     * @return void
     */
    public function create(Entity $entity, string $dbname = '');
}
