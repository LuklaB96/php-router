<?php
namespace App\Lib\Entity;



class Entity
{
    protected $properties = [];
    public $name = '';
    public EntityManager $em;
    function __construct()
    {
        if ($this->name == '')
            $this->name = $this->getEntityName();
        $this->em = EntityManager::getInstance();
    }
    public function insert($data)
    {
        $columns = implode(', ', array_keys($data));
        $values = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO $this->name ($columns) VALUES ($values)";

        $this->em->execute($sql, $data);
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
    private function getEntityName(): string
    {
        $params = explode('\\', get_class($this));
        return end($params);
    }
    public function getTableProperties(): array
    {
        return $this->properties;
    }
}


?>