<?php
namespace App\Lib\Database\Mapping;

use App\Lib\Entity\Entity;


/**
 * Gets property name and value in given class.
 */
class PropertyReader
{
    /**
     * Get all properites (Name => Value) from valid Entity object instance.
     * @param \App\Lib\Entity\Entity $object
     * @return array
     */
    public static function getProperties(Entity $object): array
    {
        $reflection = new \ReflectionClass($object);
        $properties = [];

        foreach ($reflection->getProperties() as $property) {
            if (self::hasAttribute($property)) {
                $propertyName = $property->getName();
                $propertyValue = $property->getValue($object);
                $properties[$propertyName] = $propertyValue;
            }
        }

        return $properties;
    }
    private static function hasAttribute($property): bool
    {
        $attributes = $property->getAttributes();
        if (!empty($attributes))
            return true;
        return false;
    }
}