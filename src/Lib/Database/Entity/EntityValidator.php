<?php
namespace App\Lib\Database\Entity;

use App\Lib\Database\Entity\Entity;
use App\Lib\Database\Mapping\AttributeReader;

class EntityValidator
{
    /**
     * Check if all required property values are set
     * @param \App\Lib\Database\Entity\Entity $entity
     * @return bool
     */
    public function validate(Entity $entity): bool
    {
        $properties = AttributeReader::getAttributes($entity);
        $valid = $this->checkRequiredProperties($properties);
        return $valid;
    }
    private function checkRequiredProperties($properties): bool
    {
        foreach ($properties as $property) {
            if ($this->propertyIsRequired($property) === false) {
                continue;
            }
            if ($this->propertyHasValue($property) === false) {
                return false;
            }
        }
        return true;
    }
    /**
     * For now it is straight checking, 
     * @param mixed $property
     * @return bool
     */
    private function propertyIsRequired($property): bool
    {
        //If autoIncrement is true, property is not required, database engine will deal with it automatically.
        if (isset($property['autoIncrement']) && $property['autoIncrement'] === true) {
            return false;
        }
        //If primaryKey is set to true, but lacks autoIncrement, property is required.
        if (isset($property['primaryKey']) && $property['primaryKey'] === true) {
            return true;
        }
        //If nullable is false, property is required.
        if (isset($property['nullable']) && $property['nullable'] === false) {
            return true;
        }
        //By default, property is not required.
        return false;
    }
    private function propertyHasValue($property): bool
    {
        if (!empty($property['value'])) {
            return true;
        }
        return false;
    }
}
?>