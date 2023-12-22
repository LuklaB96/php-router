<?php
namespace App\Lib\Database\Mapping;


/**
 * Set property value in object
 */
class PropertyWriter
{
    /**
     * Set the property value of a given object if a correlation between propertyName and object property has been found.
     *
     * @param  object $object
     * @param  string $propertyName
     * @param  mixed  $value
     * @throws \Exception If a property with the given name is not found.
     * @return void
     */
    public static function setPropertyValue(object $object, string $propertyName, mixed $value)
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
     * Set multiple property values if correlation has been found in the properties array for the given object.
     *
     * @param  object              $object     Class instance
     * @param  array<string,mixed> $properties Array structure needs to look like ['property_name' => 'property_value'].
     * @return void
     */
    public static function setPropertiesFromArray(object $object, array $properties)
    {
        foreach ($properties as $propertyName => $value) {
            self::setPropertyValue($object, $propertyName, $value);
        }
    }
}
