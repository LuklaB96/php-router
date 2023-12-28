<?php
namespace App\Lib\Database\Mapping;


use App\Lib\Database\Mapping\Attributes\Column;
use App\Lib\Database\Entity\Entity;

class AttributeReader
{
    /**
     * Get all column attributes from the entity.
     * @param \App\Lib\Database\Entity\Entity $object
     * @return array
     */
    public static function getAttributes(Entity $object): array
    {
        $reflection = new \ReflectionClass($object);

        //storing all attributes from the Entity class object that can be managed easily.
        $attributes = [];
        //loop to get all properties from reflection
        foreach ($reflection->getProperties() as $property) {
            //get property name, value and attributes
            $propertyName = $property->getName();
            $propertyValue = $property->getValue($object);
            $propertyAttributes = $property->getAttributes();

            //loop through attributes, get all attributes passed, and additionally store the name and value of the property so it will be easier to access it later.
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
     * Returns Column object created from valid attributes provided in array.
     *
     * @param  array $attributes
     * @return \App\Lib\Database\Mapping\Attributes\Column
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
