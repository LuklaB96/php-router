<?php
namespace App\Lib\PropAccessor;


class PropertyAccessor
{
    protected $object;
    private $propertyNames = [];

    public function __construct(object $object)
    {
        $this->object = $object;
        $this->propertyNames = $this->getPropertyNames();
    }

    public function getPropertyNames(): array
    {
        $reflection = new \ReflectionClass($this->object);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PRIVATE);

        $propertyNames = [];

        foreach ($properties as $property) {
            $propertyNames[] = $property->getName();
        }
        return $propertyNames;
    }
    public function setProperty($propertyName, $value)
    {
        $reflection = new \ReflectionObject($this->object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($this->object, $value);
    }

    public function getProperty($propertyName)
    {
        $reflection = new \ReflectionObject($this->object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        return $property->getValue($this->object);
    }
}

?>