<?php
namespace App\Lib\Database\Mapping;

use App\Lib\Database\Entity\Entity;


/**
 * Gets the property name and value in the given class.
 */
class PropertyReader
{
    /**
     * Get all properties with attributes (name => value) from a valid Entity object instance.
     * Optional parameter notNull for properties with assigned values only.
     *
     * @param  \App\Lib\Database\Entity\Entity $object
     * @return array
     */
    public static function getProperties(Entity $object, bool $null = true, string $targetAttribute = null): array
    {
        $reflection = new \ReflectionClass($object);
        $properties = [];
        foreach ($reflection->getProperties() as $property) {
            if (self::hasAttribute($property, targetAttribute: $targetAttribute)) {
                $propertyValue = null;
                if ($property->isInitialized($object)) {
                    $propertyValue = $property->getValue($object);
                }
                if (!empty($propertyValue) || $null) {
                    $propertyName = $property->getName();
                    $properties[$propertyName] = $propertyValue;
                }
            }
        }

        return $properties;
    }
    private static function hasAttribute($property, string $targetAttribute = null): bool
    {
        $attributes = $property->getAttributes();
        if (!empty($attributes)) {
            if ($targetAttribute !== null) {
                foreach ($attributes as $attribute) {
                    if ($attribute->getName() === $targetAttribute) {
                        return true;
                    }
                }
                return false;
            }
            return true;
        }
        return false;
    }
    /**
     * Returns an array as [name => value]
     *
     * @param  \App\Lib\Database\Entity\Entity $entity
     * @throws \Exception
     * @return array
     */
    public static function getPrimaryProperty(Entity $entity, bool $withValue = true): array
    {
        $attrs = AttributeReader::getAttributes($entity);
        foreach ($attrs as $attribute) {
            if ($attribute['primaryKey']) {
                $column = AttributeReader::createColumn($attribute);
                //var_dump($column);
                $value = $withValue ? $attribute['value'] : null;
                return [
                    'name' => $attribute['name'],
                    'value' => $value,
                    'columnDefinition' => $column
                ];
            }
        }
        throw new \Exception('Entity primary attribute not specified or null');
    }
}
