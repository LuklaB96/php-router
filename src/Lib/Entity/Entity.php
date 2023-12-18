<?php
namespace App\Lib\Entity;

use App\Lib\Config;


class Entity
{
    protected $properties = [];
    protected $name = '';
    public EntityManager $em;
    function __construct()
    {
        if ($this->name == '')
            $this->setDefaultEntityName();
        $this->em = EntityManager::getInstance();
    }
    public function insert($data, $testdb = false): string
    {
        $dbname = $testdb ? Config::get('TEST_DB_NAME') : Config::get('DB_NAME');
        $columns = implode(', ', array_keys($data));
        $values = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO $dbname.$this->name ($columns) VALUES ($values)";

        return $this->em->execute($sql, $data);
    }
    public function update($id, $data, $testdb = false): string
    {
        $dbname = $testdb ? Config::get('TEST_DB_NAME') : Config::get('DB_NAME');
        $setClause = '';
        foreach ($data as $key => $value) {
            $setClause .= "$key = :$key, ";
        }
        $setClause = rtrim($setClause, ', ');
        $sql = "UPDATE $dbname.$this->name SET $setClause WHERE id = :id";

        $data['id'] = $id;

        return $this->em->execute($sql, $data);
    }
    public function delete($id, $testdb = false): string
    {
        $dbname = $testdb ? Config::get('TEST_DB_NAME') : Config::get('DB_NAME');
        $sql = "DELETE FROM $dbname.$this->name WHERE id = :id";
        $data = ['id' => $id];

        return $this->em->execute($sql, $data);
    }
    public function find($id, $testdb = false)
    {
        $dbname = $testdb ? Config::get('TEST_DB_NAME') : Config::get('DB_NAME');
        $sql = "SELECT * FROM $dbname.$this->name WHERE id = :id";
        $data = ['id' => $id];

        return $this->em->execute($sql, $data); // Assuming query method returns the result set
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
    public function getTableProperties(): array
    {
        return $this->properties;
    }
}


?>