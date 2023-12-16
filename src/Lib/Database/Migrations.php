<?php
namespace App\Lib\Database;

use App\Lib\Entity\Entity;
use App\Lib\Config;

class Migrations
{
    private $created = false;
    public function create(Entity $entity)
    {
        if ($this->created)
            return;
        $sql = "CREATE TABLE IF NOT EXISTS $entity->name (";
        foreach ($entity->getTableProperties() as $columnName => $columnProperties) {
            $columnDefinitions[] = "$columnName $columnProperties";
        }

        $sql .= implode(', ', $columnDefinitions);
        $sql .= ")";


        if ($entity->em->isConnected())
            $entity->em->execute($sql);

        //check if test db is active true|false
        $testDbActive = Config::get('TEST_DB_ACTIVE');

        //block further recursion
        $this->created = true;

        //check if testing database is set to active in config
        if ($testDbActive) {
            $this->createInTestDb($entity);
        }
    }

    private function createInTestDb(Entity $entity)
    {
        $dbhost = Config::get('DB_HOST');
        $dbname = Config::get('TEST_DB_NAME');
        $dbuser = Config::get('DB_USER');
        $dbpassword = Config::get('DB_PASSWORD');
        $entity->em->setConnection($dbhost, $dbname, $dbuser, $dbpassword);
        if ($entity->em->isConnected())
            $this->create($entity);
    }
}

?>