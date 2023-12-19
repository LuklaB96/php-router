<?php
namespace App\Lib\Entity;

use App\Lib\Database\Mapping\PropertyWriter;
use App\Lib\Entity\Entity;

class EntityRepository
{
    private $entities;
    private $entityName;
    public function __construct(string $class)
    {
        $this->entityName = $class;
    }
    public function create(array $data)
    {
        if (!is_array($data) || empty($data))
            return [];
        $entity = new Entity();
        $entityRepository = [];
        foreach ($data as $entityProperties) {
            if (is_array($entityProperties) && !empty($entityProperties)) {
                $className = $this->entityName;
                $entity = new $className;
                PropertyWriter::setPropertiesFromArray($entity, $entityProperties);
                $entityRepository[] = $entity;
            }
        }
        return $entityRepository;
    }
}