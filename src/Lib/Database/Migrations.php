<?php
namespace App\Lib\Database;

use App\Lib\Database\Helpers\SQLQueryBuilder;
use App\Lib\Database\Mapping\AttributeReader;
use App\Lib\Entity\Entity;
use App\Lib\Config;

class Migrations
{
    /**
     * Creates table in default or specified database from entity class properties
     * @param \App\Lib\Entity\Entity $entity
     * @param string $dbname
     * @return void
     */
    public function create(Entity $entity, string $dbname = '')
    {
        //create main database (default)
        $this->createTable($entity);

        //create table in test db if set to true in config.php
        $testDbRequired = Config::get('TEST_DB_ACTIVE');
        if ($testDbRequired) {
            $dbname = Config::get('TEST_DB_NAME');
            $this->createTable($entity, $dbname);
        }
    }
    private function createTable(Entity $entity, $dbname = '')
    {
        //if $dbname is not specified, get default db name from config
        if ($dbname == '')
            $dbname = Config::get('DB_NAME');

        //we using entity name as table name, can be customized in every entity class.
        $tableName = $entity->getEntityName();
        $properties = $entity->getAttributes();

        $columns = [];
        foreach ($properties as $property) {
            $column = AttributeReader::createColumn($property);
            $columns[] = $column;
        }

        $sql = SQLQueryBuilder::createTable($columns, $tableName, $dbname);
        //if connection is valid, execute sql query
        if ($entity->em->isConnected()) {
            $message = $entity->em->execute($sql);
            if ($message == 'ok') {
                echo "Table $tableName created in: $dbname \r\n";
            } else {
                echo $message['message'] . "\r\n";
            }
        }
    }
}

?>