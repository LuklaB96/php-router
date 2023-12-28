<?php
namespace App\Lib\Database\Entity;

use App\Lib\Database\Entity\Entity;
use App\Lib\Database\Mapping\AttributeReader;

class EntityValidator
{
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
    private function propertyIsRequired($property): bool
    {
        if (isset($property['autoIncrement'])) {
            if ($property['autoIncrement'] === true) {
                return false;
            }
        }
        if (isset($property['primaryKey'])) {
            if ($property['primaryKey'] === true) {
                return true;
            }
        }
        if (isset($property['nullable']) && isset($property['autoIncrement'])) {
            if ($property['nullable'] === false && $property['autoIncrement'] === true) {
                return false;
            }
        }
        if (isset($property['nullable'])) {
            if ($property['nullable'] === false) {
                return true;
            }
        }
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