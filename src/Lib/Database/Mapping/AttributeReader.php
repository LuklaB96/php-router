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

        //storing all attributes from Entity class object that can be managed easly.
        $attributes = [];
        //loop to get all properties from reflection
        foreach ($reflection->getProperties() as $property) {
            //get property name, value and attributes
            $propertyName = $property->getName();
            $propertyValue = $property->getValue($object);
            $propertyAttributes = $property->getAttributes();

            //loop through attributes, get all attribute arugments passed, additionally store name and value of the property, so it will be easier to access it (name is unique)
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