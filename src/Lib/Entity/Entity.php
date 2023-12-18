<?php
namespace App\Lib\Entity;

use App\Lib\Config;
use App\Lib\Database\Helpers\SQLQueryBuilder;
use App\Lib\Database\Mapping\AttributeReader;
use App\Lib\Database\Mapping\PropertyReader;
use App\Lib\Database\Mapping\PropertyWriter;


class Entity
{
    /**
     * Table name
     * @var string
     */
    protected $name = '';
    public EntityManager $em;
    function __construct()
    {
        if ($this->name == '')
            $this->setDefaultEntityName();
        $this->em = EntityManager::getInstance();
    }
    public function insert(bool $testdb = false, string $dbname = null): mixed
    {
        $dbname = $this->getDbName(testdb: $testdb, dbname: $dbname);

        $data = PropertyReader::getProperties($this);
        $sql = SQLQueryBuilder::createInsertQuery($data, $this->name, $dbname);
        return $this->em->execute($sql, $data);
    }
    public function update(bool $testdb = false, string $dbname = null): mixed
    {
        $dbname = $this->getDbName(testdb: $testdb, dbname: $dbname);

        $data = PropertyReader::getProperties($this);
        $primaryKey = PropertyReader::getPrimaryProperty($this);

        $query = SQLQueryBuilder::createUpdateQuery($data, $this->name, $dbname);

        return $this->em->execute($query, $data);
    }
    public function delete($id, bool $testdb = false, string $dbname = null): string
    {
        $dbname = $this->getDbName(testdb: $testdb, dbname: $dbname);
        $sql = "DELETE FROM $dbname.$this->name WHERE id = :id";
        $data = ['id' => $id];

        return $this->em->execute($sql, $data);
    }
    public function find($key, $testdb = false, $dbname = null)
    {
        $dbname = $this->getDbName(testdb: $testdb, dbname: $dbname);
        $primaryKey = PropertyReader::getPrimaryProperty($this);
        $query = SQLQueryBuilder::createSelectQuery($dbname, $this->getEntityName(), conditions: [$primaryKey['name'] => '']);
        $data = [$primaryKey['name'] => $key];
        $result = $this->em->execute($query, $data); // Assuming query method returns the result set

        //set values to properties for this instance
        PropertyWriter::setPropertiesFromArray($this, $result[0]);
    }
    public function findAll($testdb = false)
    {
        $dbname = $testdb ? Config::get('TEST_DB_NAME') : Config::get('DB_NAME');
        $sql = "SELECT * FROM $dbname.$this->name";

        return $this->em->execute($sql); // Assuming query method returns the result set
    }
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

        return $this->em->execute($sql, $data); // Assuming query method returns the result set
    }
    public function findOneBy(array $criteria, array $orderBy = null, $testdb = false)
    {
        $results = $this->findBy($criteria, $orderBy, 1, $testdb);
        return !empty($results) ? $results[0] : null;
    }
    public function getEntityName(): string
    {
        return $this->name;
    }
    private function setDefaultEntityName()
    {
        $params = explode('\\', get_class($this));
        $this->name = end($params);
    }
    public function getProperties(): array
    {
        //get only fields with Column attributes properly configured
        $classAttributes = AttributeReader::getAttributes($this);
        return $classAttributes;
    }
    public function setProperties(array $properties)
    {
        PropertyWriter::setPropertiesFromArray($this, $properties);
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