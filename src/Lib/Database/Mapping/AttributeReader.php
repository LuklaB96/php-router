<?php
namespace App\Lib\Database\Mapping;


use App\Lib\Entity\Entity;

class AttributeReader
{
    /**
     * Get all column attributes from entity
     * @param \App\Lib\Database\Mapping\Column $object
     * @return array
     */
    public static function getAttributes(Entity $object): array
    {
        $reflection = new \ReflectionClass($object);
        $attributes = [];

        foreach ($reflection->getProperties() as $property) {
            $propertyName = $property->getName();
            $propertyValue = $property->getValue($object);
            $propertyAttributes = $property->getAttributes();

            foreach ($propertyAttributes as $attribute) {
                $arguments = $attribute->getArguments();
                $arguments['value'] = $propertyValue;
                $arguments['name'] = $propertyName;
                $attributes[$propertyName] = $arguments;
            }
        }

        return $attributes;
    }
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
            $propertyName = $property->getName();
            $propertyValue = $property->getValue($object);
            $properties[$propertyName] = $propertyValue;
        }

        return $properties;
    }
    /**
     * Returns Column object created from valid attributes provided in array.
     * @param array $attributes
     * @return Column
     */
    public static function createColumn(array $attributes): Column
    {
        //check if required attributes are valid
        AttributeValidator::validate($attributes);

        $name = $attributes['name'];
        $type = $attributes['type'];
        $length = $attributes['length'] ?? null;
        $primaryKey = $attributes['primaryKey'] ?? false;
        $autoIncrement = $attributes['autoIncrement'] ?? false;
        $nullable = $attributes['nullable'] ?? false;

        return new Column($name, $type, $length, $primaryKey, $autoIncrement, $nullable);
    }
}