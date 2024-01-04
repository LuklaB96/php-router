<?php
/**
 * TODO:
 *  - Add support for Database transactions, currently every query is executed separately
 *  - Add EntityInterface implementation 
 *  - Relations support
 */
namespace App\Lib\Database\Entity;

use App\Lib\Config;
use App\Lib\Database\Database;
use App\Lib\Database\Exception\DatabaseNotConnectedException;
use App\Lib\Database\Helpers\QueryBuilder;
use App\Lib\Database\Mapping\AttributeReader;
use App\Lib\Database\Mapping\PropertyReader;
use App\Lib\Database\Mapping\PropertyWriter;

/**
 * Represents model in database
 */
class Entity
{
    public Database $db;
    private EntityValidator $entityValidator;
    function __construct()
    {
        $this->db = Database::getInstance();
        $this->entityValidator = new EntityValidator();
    }
    /**
     * Insert entity data into database
     *
     * @param  bool   $testdb
     * @param  string $dbname
     * @return string
     */
    public function insert(bool $testdb = false, string $dbname = null): string
    {
        $dbname = $this->getDbName(testdb: $testdb, dbname: $dbname);

        $data = $this->getProperties(null: false); //get all entity properties, key(column name) => value
        $query = QueryBuilder::insert($data, $this->getEntityName(), $dbname);
        return $this->db->execute($query, $data);
    }
    /**
     * Update entity in database
     *
     * @param  bool   $testdb
     * @param  string $dbname
     * @return mixed
     */
    public function update(bool $testdb = false, string $dbname = null): mixed
    {
        $dbname = $this->getDbName(testdb: $testdb, dbname: $dbname);

        $data = $this->getProperties(null: false); //get all entity properties, key(column name) => value
        $query = QueryBuilder::update($data, $this->getEntityName(), $dbname);
        return $this->db->execute($query, $data);
    }
    /**
     * Delete entity from database
     *
     * @param  bool   $testdb
     * @param  string $dbname
     * @return string
     */
    public function delete(bool $testdb = false, string $dbname = null): string
    {
        $dbname = $this->getDbName(testdb: $testdb, dbname: $dbname);

        $primaryKey = PropertyReader::getPrimaryProperty($this); //Get primary key if exists
        $query = QueryBuilder::delete($this->getEntityName(), $dbname, [$primaryKey['name'] => $primaryKey['value']]);
        return $this->db->execute($query);
    }
    /**
     * Find entity by primary key in database and update instance properties
     *
     * @param  mixed $key
     * @param  mixed $testdb
     * @param  mixed $dbname
     * @return void
     * @throws \Exception
     * @throws \App\Lib\Database\Exception\DatabaseNotConnectedException
     */
    public function find($key, $testdb = false, string $dbname = null): bool
    {
        $dbname = $this->getDbName(testdb: $testdb, dbname: $dbname);
        $primaryKey = PropertyReader::getPrimaryProperty($this);
        $criteria = [$primaryKey['name'], '=', $key];
        $query = QueryBuilder::select($this->getEntityName(), $dbname, criteria: $criteria);
        $data = [$primaryKey['name'] => $key];
        $result = $this->db->execute($query, $data);


        //set values to properties for this instance
        $this->setProperties($result);
        return false;

    }
    /**
     * Find all data for this entity in database
     *
     * @param  bool $testdb
     * @return Entity[]
     */
    public function findAll(bool $testdb = false, string $dbname = null): array
    {
        $dbname = $this->getDbName(testdb: $testdb, dbname: $dbname);
        $query = QueryBuilder::select($this->getEntityName(), $dbname);

        try {
            $result = $this->db->execute($query);
        } catch (\Exception $e) {
            $result = [];
        }
        $repository = $this->createRepository($result);
        return $repository;
    }
    /**
     * Find entities in database that meets criteria passed in array
     *
     * @param  array  $criteria
     * @param  string $orderBy  syntax: column_name ASC|DESC
     * @param  int    $limit
     * @param  int    $offset
     * @param  mixed  $testdb
     * @return array|null
     */
    public function findBy(array $criteria, string $orderBy = null, int $limit = null, int $offset = null, $testdb = false, string $dbname = null): array
    {
        // ['column_name','condition','value'] = ['id','>',5]
        $dbname = $this->getDbName(testdb: $testdb, dbname: $dbname);
        $query = QueryBuilder::select($this->getEntityName(), $dbname, criteria: $criteria, limit: $limit, offset: $offset);
        $data = $this->convertCriteriaToDataArray($criteria);
        $result = $this->db->execute($query, $data);
        if ($limit === 1) {
            $this->setProperties($result);
        }
        $repository = $this->createRepository($result);
        return $repository;
    }
    /**
     * Find one entity that meets the criteria and order if specified
     *
     * @param array  $criteria syntax: array['column_name' => 'value'] ['id','>',5]
     * @param string $orderBy  syntax: column_name ASC|DESC
     * @param bool   $testdb
     * @return bool
     */
    public function findOneBy(array $criteria, string $orderBy = null, bool $testdb = false, string $dbname = null): bool
    {
        $result = $this->findBy($criteria, orderBy: $orderBy, limit: 1, testdb: $testdb, dbname: $dbname);
        if (!empty($result)) {
            return true;
        }
        return false;

    }
    /**
     * @param  bool $withNamespace if true, will return \\App\\Namespace\\Example\\ClassName, if false will return only ClassName
     * @return string entity class name with or without namespace
     */
    public function getEntityName(bool $withNamespace = false): string
    {
        if ($withNamespace) {
            return get_class($this);
        }
        $params = explode('\\', get_class($this));
        return end($params);
    }
    /**
     * @return array array with all entity properties that have attributes 
     */
    public function getAttributes(): array
    {
        //get only fields with Column attributes properly configured
        $classAttributes = AttributeReader::getAttributes($this);
        return $classAttributes;
    }
    /**
     * Set properties for current entity instance
     * @param array $properties result data from executed select * query
     * @return bool
     */
    private function setProperties(array $properties): bool
    {
        if (is_array($properties) && !empty($properties)) {
            PropertyWriter::setPropertiesFromArray($this, $properties[0]);
            return true;
        }
        return false;
    }
    /**
     * @param  bool $null if needs all or only with not empty value
     * @return array entity properties array(property_name => value)
     */
    public function getProperties(bool $null = true): array
    {
        $classProperties = PropertyReader::getProperties($this, $null);
        return $classProperties;
    }
    /**
     * @param  mixed $testdb
     * @param  mixed $dbname
     * @return string test db name if $testdb = true, custom dbname if $dbname parameter is not null/empty or base name from config file in this particular order
     */
    private function getDbName($testdb = false, string $dbname = null): string
    {
        if ($testdb) {
            $dbname = Config::get('TEST_DB_NAME');
        } else {
            $dbname = empty($dbname) ? Config::get('DB_NAME') : $dbname;
        }
        return $dbname;
    }
    /**
     * creating entity repository array if more than one entity is expected to be returned
     *
     * @param  array $data
     * @return Entity[] 
     */
    private function createRepository(array $data): array|object
    {
        if (!is_array($data) || empty($data)) {
            return [];
        }

        $entityRepository = [];
        foreach ($data as $entityProperties) {
            if (is_array($entityProperties) && !empty($entityProperties)) {
                $className = $this->getEntityName(withNamespace: true);
                $entity = new $className;
                PropertyWriter::setPropertiesFromArray($entity, $entityProperties);
                $entityRepository[] = $entity;
            }
        }
        return $entityRepository;
    }
    /**
     * Validate Entity data before sending it to database
     * Ignores properties with attribute autoIncrement set as true
     * 
     * 
     * @return bool true if all required property values are set, otherwise false
     */
    public function validate(): bool
    {
        return $this->entityValidator->validate($this);
    }
    /**
     * Converts searching conditions array into PDO execute data array.
     * 
     * Criteria array: ['column_name','>=','value'] = ['column_name','value']
     * 
     * @param array $criteria
     * @return array
     */
    private function convertCriteriaToDataArray(array $criteria): array
    {
        if (empty($criteria)) {
            return [];
        }
        $result = [$criteria[0] => $criteria[2]];
        return $result;
    }
}


?>