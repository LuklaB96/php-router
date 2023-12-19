<?php
namespace App\Lib\Database\Mapping;


/**
 * Set property value in object
 */
class PropertyWriter
{
    public static function setPropertyValue($object, $propertyName, $value)
    {
        $reflectionClass = new \ReflectionClass($object);

        if ($reflectionClass->hasProperty($propertyName)) {
            $property = $reflectionClass->getProperty($propertyName);
            $property->setAccessible(true); // Make the property accessible

            // Set the new value for the property in the object
            $property->setValue($object, $value);
        } else {
            throw new \Exception("Property '$propertyName' not found in class " . get_class($object));
        }
    }

    /**
     * Set multiple properties values in object
     *
     * @param  mixed $object
     * @param  array $properties
     * @return void
     */
    public static function setPropertiesFromArray($object, array $properties)
    {
        foreach ($properties as $propertyName => $value) {
            self::setPropertyValue($object, $propertyName, $value);
        }
    }
}
