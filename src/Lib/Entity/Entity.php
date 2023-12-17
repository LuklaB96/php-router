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
    public function update()
    {

    }
    public function delete()
    {

    }
    public function find($id)
    {

    }
    public function findAll()
    {

    }
    public function findBy(array $criteria, array $orderBy = null, int $limit = null)
    {

    }
    public function findOneBy(array $criteria, array $orderBy = null)
    {

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