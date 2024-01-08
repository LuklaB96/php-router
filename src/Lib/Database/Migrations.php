<?php
namespace App\Lib\Database;

use App\Lib\Database\Helpers\QueryBuilder;
use App\Lib\Database\Interface\MigrationsInterface;
use App\Lib\Database\Mapping\AttributeReader;
use App\Lib\Database\Entity\Entity;
use App\Lib\Config;

class Migrations implements MigrationsInterface
{
    /**
     * Creates table in default or specified database from entity class properties
     *
     * @param  \App\Lib\Database\Entity\Entity $entity
     * @param  string                 $dbname
     * @return void
     */
    public function create(Entity $entity, string $dbname = '')
    {
        //create main database (default)
        $this->createTable($entity);
    }
    private function createTable(Entity $entity, $dbname = '')
    {
        if ($entity->db->isConnected()) {
            //if $dbname is not specified, get default db name from config
            if ($dbname == '') {
                $dbname = Config::get('DB_NAME', 'app_db');
            }

            //we using entity name as table name, can be customized in every entity class.
            $tableName = $entity->getEntityName();
            $properties = $entity->getAttributes();

            $columns = [];
            foreach ($properties as $property) {
                $column = AttributeReader::createColumn($property);
                $columns[] = $column;
            }

            $query = QueryBuilder::createTable($columns, $tableName, $dbname);
            //if connection is valid, execute sql query
            try {
                $entity->db->execute($query);
                $message = 'ok';
            } catch (\Exception $e) {
                $message = $e->getCode();
            }
            if ($message == 'ok') {
                echo "Table $tableName created in: $dbname \r\n";
            } else {
                if ($message === '42S01') {
                    echo "Table $tableName already exists in $dbname \r\n";
                }
            }
            return;
        }
        echo "Not connected to database";
    }
}

?>