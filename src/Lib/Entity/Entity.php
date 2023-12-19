<?php
namespace App\Lib\Entity;

use App\Lib\Config;
use App\Lib\Database\Helpers\SQLQueryBuilder;
use App\Lib\Database\Mapping\AttributeReader;
use App\Lib\Database\Mapping\PropertyReader;
use App\Lib\Database\Mapping\PropertyWriter;


class Entity
{
    public EntityManager $em;
    function __construct()
    {
        $this->em = EntityManager::getInstance();
    }
    /**
     * Insert entity data into database
     * @param bool $testdb
     * @param string $dbname
     * @return mixed
     */
    public function insert(bool $testdb = false, string $dbname = null): mixed
    {
        $dbname = $this->getDbName(testdb: $testdb, dbname: $dbname);

        $data = PropertyReader::getProperties($this);
        $sql = SQLQueryBuilder::insert($data, $this->getEntityName(), $dbname);
        return $this->em->execute($sql, $data);
    }
    /**
     * Update entity in database
     * @param bool $testdb
     * @param string $dbname
     * @return mixed
     */
    public function update(bool $testdb = false, string $dbname = null): mixed
    {
        $dbname = $this->getDbName(testdb: $testdb, dbname: $dbname);

        $data = PropertyReader::getProperties($this, notNull: true);

        $query = SQLQueryBuilder::update($data, $this->getEntityName(), $dbname);

        return $this->em->execute($query, $data);
    }
    /**
     * Delete entity from database
     * @param bool $testdb
     * @param string $dbname
     * @return string
     */
    public function delete(bool $testdb = false, string $dbname = null): string
    {
        $dbname = $this->getDbName(testdb: $testdb, dbname: $dbname);
        $primaryKey = PropertyReader::getPrimaryProperty($this);
        $query = SQLQueryBuilder::delete($this->getEntityName(), $dbname, [$primaryKey['name'] => $primaryKey['value']]);
        return $this->em->execute($query);
    }
    /**
     * Find entity by primary key in database and update instance properties
     * @param mixed $key
     * @param mixed $testdb
     * @param mixed $dbname
     * @return void
     */
    public function find($key, $testdb = false, $dbname = null)
    {
        $dbname = $this->getDbName(testdb: $testdb, dbname: $dbname);
        $primaryKey = PropertyReader::getPrimaryProperty($this);
        $query = SQLQueryBuilder::select($this->getEntityName(), $dbname, conditions: [$primaryKey['name'] => '']);
        $data = [$primaryKey['name'] => $key];
        $result = $this->em->execute($query, $data);

        //set values to properties for this instance
        if (is_array($result) && !empty($result)) {
            PropertyWriter::setPropertiesFromArray($this, $result[0]);
        }
    }
    /**
     * Find all data fron this entity in database
     * @param mixed $testdb
     * @return array|string
     */
    public function findAll($testdb = false)
    {
        $dbname = $testdb ? Config::get('TEST_DB_NAME') : Config::get('DB_NAME');
        $sql = "SELECT * FROM $dbname.$this->name";

        return $this->em->execute($sql);
    }
    /**
     * Find entities in database that meets criteria passed in array
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param mixed $testdb
     * @return array|string
     */
    public function findBy(array $criteria, array $orderBy = null, int $limit = null, $testdb = false)
    {
        $dbname = $testdb ? Config::get('TEST_DB_NAME') : Config::get('DB_NAME');
        $whereClause = '';
        $data = [];
        foreach ($criteria as $key => $value) {
            $whereClause .= "$key = :$key AND ";
            $data[$key] = $value;
        }
        $whereClause = rtrim($whereClause, 'AND ');

        $orderByClause = '';
        if ($orderBy !== null) {
            $orderByClause = ' ORDER BY ';
            foreach ($orderBy as $key => $value) {
                $orderByClause .= "$key $value, ";
            }
            $orderByClause = rtrim($orderByClause, ', ');
        }

        $limitClause = ($limit !== null) ? " LIMIT $limit" : '';

        $sql = "SELECT * FROM $dbname.$this->name WHERE $whereClause$orderByClause$limitClause";

        return $this->em->execute($sql, $data);
    }
    /**
     * Find one entity that meets the criteria and order if specified
     * @param array $criteria
     * @param array $orderBy
     * @param mixed $testdb
     * @return mixed
     */
    public function findOneBy(array $criteria, array $orderBy = null, $testdb = false)
    {
        $results = $this->findBy($criteria, $orderBy, 1, $testdb);
        return !empty($results) ? $results[0] : null;
    }
    /**
     * Get entity class name
     * @return string
     */
    public function getEntityName(): string
    {
        $params = explode('\\', get_class($this));
        return end($params);
    }
    /**
     * Get all entity properties with attributes 
     * @return array
     */
    public function getAttributes(): array
    {
        //get only fields with Column attributes properly configured
        $classAttributes = AttributeReader::getAttributes($this);
        return $classAttributes;
    }
    private function setProperties(array $properties)
    {
        PropertyWriter::setPropertiesFromArray($this, $properties);
    }
    /**
     * Get entity properties array(name => value)
     * Optional null if those are only needed.
     * @param bool $null Optional if only with properties with values needed
     * @return array
     */
    public function getProperties(bool $null = true): array
    {
        $classProperties = PropertyReader::getProperties($this, $null);
        return $classProperties;
    }
    private function getDbName($testdb = false, $dbname = null): string
    {
        if ($testdb) {
            $dbname = $testdb ? Config::get('TEST_DB_NAME') : $dbname;
        } else {
            $dbname = empty($dbname) ? Config::get('DB_NAME') : $dbname;
        }
        return $dbname;
    }
}


?>