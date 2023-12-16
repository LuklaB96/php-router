<?php
namespace App\Lib\Migrations;

use App\Lib\Entity\Entity;

class Migrations
{
    public function create(Entity $entity)
    {
        //get current entity class name and set it as table name
        $sql = "CREATE TABLE IF NOT EXISTS $entity->name (";
        foreach ($entity->getTableProperties() as $columnName => $columnProperties) {
            $columnDefinitions[] = "$columnName $columnProperties";
        }

        $sql .= implode(', ', $columnDefinitions);
        $sql .= ")";

        if ($entity->em->isConnected()) {
            $entity->em->execute($sql);
        }
    }
}

?>