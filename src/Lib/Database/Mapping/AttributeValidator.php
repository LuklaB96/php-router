<?php
namespace App\Lib\Database\Mapping;

use App\Lib\Database\Exception\EmptyColumnNameException;
use App\Lib\Database\Exception\EmptyColumnTypeException;
use App\Lib\Database\Exception\EmptyAttributeArrayException;

class AttributeValidator
{
    /**
     * Validate all required parameters to create a database column.
     *
     * @param  array $attributes
     * @throws \App\Lib\Database\Exception\EmptyAttributeArrayException
     * @throws \App\Lib\Database\Exception\EmptyColumnNameException
     * @throws \App\Lib\Database\Exception\EmptyColumnTypeException
     * @return void
     */
    public static function validate(array $attributes)
    {
        if (empty($attributes)) {
            throw new EmptyAttributeArrayException();
        }
        $name = $attributes['name'] ?? '';
        if (empty($name)) {
            throw new EmptyColumnNameException();
        }
        $type = $attributes['type'] ?? null;
        if (empty($type)) {
            throw new EmptyColumnTypeException();
        }
        $length = $attributes['length'] ?? 0;
        if (!is_numeric($length)) {
            throw new \Exception('Length parameter needs to be a numeric type.');
        }
    }
}
