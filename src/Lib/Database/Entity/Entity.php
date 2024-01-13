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
use App\Lib\Database\Helpers\QueryBuilder;
use App\Lib\Database\Mapping\AttributeReader;
use App\Lib\Database\Mapping\Attributes\Column;
use App\Lib\Database\Mapping\Attributes\Relation;
use App\Lib\Database\Mapping\PropertyReader;
use App\Lib\Database\Mapping\PropertyWriter;

/**
 * Represents model in database
 */
class Entity
{
    public Database $db;
    public ?\Exception $exception;
    private EntityValidator $entityValidator;
    private $exists;
    function __construct()
    {
        $this->db = Database::getInstance();
        $this->entityValidator = new EntityValidator();
        $this->exception = new \Exception;
        $this->exists = false;
    }
    /**
     * Insert entity data into database
     * 
     * @param  bool   $testdb
     * @param  string $dbname
     * @return string
     */
    public function insert(string $dbname = null): bool
    {
        $dbname = $this->getDbName(dbname: $dbname);

        $data = $this->getProperties(null: false, targetAttribute: Column::class); //get all entity column properties, key(column name) => value
        //get relation properties
        if ($this->hasRelations()) {
            $relationData = AttributeReader::getRelationsData($this);
            $data = array_merge($data, $relationData);
        }
        //var_dump($data);
        $query = QueryBuilder::insert($data, $this->getEntityName(), $dbname);

        try {
            $this->db->execute($query, $data);

            //update entity
            if ($this->db->getLastInsertedId() !== 0) {
                $this->find($this->db->getLastInsertedId());
            }
            return true;
        } catch (\Exception $e) {
            $this->exception = $e;
            return false;
        }

    }
    /**
     * Update entity in database
     *
     * @param  bool   $testdb
     * @param  string $dbname
     * @return bool
     */
    public function update(string $dbname = null): bool
    {
        $dbname = $this->getDbName(dbname: $dbname);

        $data = $this->getProperties(null: false); //get all entity properties, key(column name) => value
        $query = QueryBuilder::update($data, $this->getEntityName(), $dbname);
        error_log($query);
        try {
            $this->db->execute($query, $data);
            return true;
        } catch (\Exception $e) {
            $this->exception = $e;
            return false;
        }
    }
    /**
     * Delete entity from database
     *
     * @param  bool   $testdb
     * @param  string $dbname
     * @return string
     */
    public function delete(string $dbname = null): bool
    {
        $dbname = $this->getDbName(dbname: $dbname);

        $primaryKey = PropertyReader::getPrimaryProperty($this); //Get primary key if exists
        $criteria = [$primaryKey['name'], '=', $primaryKey['value']];
        $query = QueryBuilder::delete($this->getEntityName(), $dbname, $criteria);
        echo $query;
        try {
            $this->db->execute($query, [$primaryKey['name'] => $primaryKey['value']]);
            return true;
        } catch (\Exception $e) {
            $this->exception = $e;
            return false;
        }
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
    public function find($key, string $dbname = null): bool
    {
        $dbname = $this->getDbName(dbname: $dbname);
        $primaryKey = $this->getPrimaryKey();
        $criteria = [[$primaryKey['name'], '=', $key]];
        $query = QueryBuilder::select($this->getEntityName(), $dbname, criteria: $criteria);
        $data = $this->convertCriteriaToDataArray($criteria);
        try {
            $result = $this->db->execute($query, $data);

            //set values to properties for this instance if request is not empty
            if (!empty($result)) {
                $this->setProperties($result);
                return true;
            }
        } catch (\Exception $e) {
            $this->exception = $e;
            return false;
        }
        return false;
    }
    /**
     * Find all data for this entity in database
     *
     * @param  bool $testdb
     * @return Entity[]
     */
    public function findAll(string $dbname = null): array
    {
        $dbname = $this->getDbName(dbname: $dbname);
        $query = QueryBuilder::select($this->getEntityName(), $dbname);

        try {
            $result = $this->db->execute($query);
        } catch (\Exception $e) {
            $result = [];
            $this->exception = $e;
        }
        $repository = $this->createRepository($result);
        return $repository;
    }
    public function count(array $criteria = null): int
    {
        $data = [];
        if (isset($criteria) && !is_array($criteria[0])) {
            $criteria = [$criteria];
            $data = $this->convertCriteriaToDataArray($criteria);
        }
        $dbname = $this->getDbName();
        $query = QueryBuilder::count($this->getEntityName(), $dbname, criteria: $criteria);

        $result = $this->db->execute($query, $data);
        if (isset($result[0]['COUNT(*)'])) {
            return $result[0]['COUNT(*)'];
        }
        return 0;

    }
    /**
     * Find entities in database that meets criteria passed in array
     *
     * @param  array  $criteria
     * @param  string $orderBy  syntax: column_name ASC|DESC
     * @param  int    $limit
     * @param  int    $offset
     * @param  mixed  $testdb
     * @return array
     */
    public function findBy(array $criteria = null, string $orderBy = null, int $limit = null, int $offset = null, string $dbname = null): array
    {
        $data = [];
        //convert array to accessible one
        if (isset($criteria) && !is_array($criteria[0])) {
            $criteria = [$criteria];
            $data = $this->convertCriteriaToDataArray($criteria);
        }
        $dbname = $this->getDbName(dbname: $dbname);
        $query = QueryBuilder::select($this->getEntityName(), $dbname, criteria: $criteria, limit: $limit, offset: $offset);
        $repository = [];
        try {
            $result = $this->db->execute($query, $data);
            if ($limit === 1) {
                $this->setProperties($result);
                return ['found' => true];
            }
            $repository = $this->createRepository($result);
        } catch (\Exception $e) {
            $this->exception = $e;
        }
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
    public function findOneBy(array $criteria = null, string $orderBy = null, string $dbname = null): bool
    {
        $result = $this->findBy($criteria, orderBy: $orderBy, limit: 1, dbname: $dbname);
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
            $this->invokeRelations();
            $this->exists = true;
            return true;
        }
        return false;
    }
    /**
     * @param  bool $null if needs all or only with not empty value
     * @return array entity properties array(property_name => value)
     */
    public function getProperties(bool $null = true, string $targetAttribute = null): array
    {
        $entityProperties = PropertyReader::getProperties($this, $null, $targetAttribute);
        return $entityProperties;
    }
    /**
     * 
     * @param string $dbname
     * @return string
     */
    private function getDbName(string $dbname = null): string
    {
        $dbname = empty($dbname) ? Config::get('DB_NAME', 'app_db') : $dbname;
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
                $entity->invokeRelations();
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
        $data = [];
        $count = 1;
        foreach ($criteria as $condition) {
            $data["$condition[0]$count"] = $condition[2];
            $count++;
        }
        return $data;
    }
    private function hasRelations(): bool
    {
        return AttributeReader::hasAttribute($this, Relation::class);
    }
    private function setRelations()
    {

    }
    public function getPrimaryKey(): array
    {
        return PropertyReader::getPrimaryProperty($this);
    }
    public function getEntityRelations(): array
    {
        return PropertyReader::getEntityRelations($this);
    }
    private function invokeRelations(): bool
    {
        $relations = $this->getEntityRelations();
        foreach ($relations as $relation) {
            $value = PropertyReader::getPropertyValue($this, $relation['foreignKey']);
            if ($value !== null) {
                $entity = PropertyWriter::initEntity($relation['targetEntity']);
                $entity->find($value);
                PropertyWriter::setPropertyValue($this, $relation['propertyName'], $entity);
            }
        }
        return true;
    }
    public function exists(): bool
    {
        return $this->exists;
    }
}


?>