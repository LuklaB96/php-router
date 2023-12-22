<?php
namespace App\Lib\Database\Mapping;

use App\Lib\Entity\Entity;


/**
 * Gets the property name and value in the given class.
 */
class PropertyReader
{
    /**
     * Get all properties with attributes (name => value) from a valid Entity object instance.
     * Optional parameter notNull for properties with assigned values only.
     *
     * @param  \App\Lib\Entity\Entity $object
     * @return array
     */
    public static function getProperties(Entity $object, bool $null = true): array
    {
        $reflection = new \ReflectionClass($object);
        $properties = [];

        foreach ($reflection->getProperties() as $property) {
            if (self::hasAttribute($property)) {
                $propertyValue = $property->getValue($object);
                if (!empty($propertyValue) || $null) {
                    $propertyName = $property->getName();
                    $properties[$propertyName] = $propertyValue;
                }
            }
        }

        return $properties;
    }
    private static function hasAttribute($property): bool
    {
        $attributes = $property->getAttributes();
        if (!empty($attributes)) {
            return true;
        }
        return false;
    }
    /**
     * Returns an array as [name => value]
     *
     * @param  \App\Lib\Entity\Entity $entity
     * @throws \Exception
     * @return array
     */
    public static function getPrimaryProperty(Entity $entity): array
    {
        $attrs = AttributeReader::getAttributes($entity);
        foreach ($attrs as $attribute) {
            if ($attribute['primaryKey']) {
                return ['name' => $attribute['name'],
                    'value' => $attribute['value']
                ];
            }
        }
        throw new \Exception('Entity primary attribute not specified or null');
    }
}
