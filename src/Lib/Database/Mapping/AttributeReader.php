<?php
namespace App\Lib\Database\Mapping;


use App\Lib\Database\Mapping\Attributes\Column;
use App\Lib\Database\Entity\Entity;
use App\Lib\Database\Mapping\Attributes\Relation;

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
            $propertyValue = null;
            if ($property->isInitialized($object)) {
                $propertyValue = $property->getValue($object);
            }
            $propertyAttributes = $property->getAttributes();

            //loop through attributes, get all attributes passed, and additionally store the name and value of the property so it will be easier to access it later.
            foreach ($propertyAttributes as $attribute) {
                $arguments = $attribute->getArguments();
                $arguments['attributeType'] = 'none';
                if ($attribute->getName() === Column::class) {
                    $arguments['attributeType'] = 'Column';
                }
                if ($attribute->getName() === Relation::class) {
                    $arguments['attributeType'] = 'Relation';
                    //get target entity primary key for relation
                    $arguments['propertyPrimaryKey'] = PropertyReader::getPrimaryProperty(new $arguments['targetEntity'], false);
                }
                $arguments['value'] = $propertyValue;
                $arguments['name'] = $propertyName;
                $attributes[$propertyName] = $arguments;
            }
        }
        return $attributes;
    }
    public static function hasAttribute(Entity $object, string $attributeClassName): bool
    {
        $reflection = new \ReflectionClass($object);
        foreach ($reflection->getProperties() as $property) {
            $propertyAttributes = $property->getAttributes();

            foreach ($propertyAttributes as $attribute) {
                if ($attribute->getName() === $attributeClassName) {
                    return true;
                }
            }
        }
        return false;
    }
    public static function getRelationsData(Entity $object): array
    {
        $data = [];
        $reflection = new \ReflectionClass($object);
        foreach ($reflection->getProperties() as $property) {
            $propertyAttributes = $property->getAttributes();

            foreach ($propertyAttributes as $attribute) {
                if ($attribute->getName() === Relation::class) {
                    $relationObject = $property->getValue($object);
                    $arguments = $attribute->getArguments();
                    $relationPrimaryKey = PropertyReader::getPrimaryProperty($relationObject, true);
                    $params = explode('\\', $arguments['targetEntity']);
                    $relationEntity = end($params);
                    $data[strtolower($relationEntity) . '_' . $relationPrimaryKey['name']] = $relationPrimaryKey['value'];
                }
            }
        }
        return $data;
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
        AttributeValidator::validateColumn($attributes);

        $name = $attributes['name'];
        $type = $attributes['type'];
        $length = $attributes['length'] ?? null;
        $primaryKey = $attributes['primaryKey'] ?? false;
        $autoIncrement = $attributes['autoIncrement'] ?? false;
        $nullable = $attributes['nullable'] ?? false;

        return new Column($name, $type, $length, $primaryKey, $autoIncrement, $nullable);
    }
}
