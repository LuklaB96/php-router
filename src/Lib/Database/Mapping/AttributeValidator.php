<?php
namespace App\Lib\Database\Mapping;

use App\Lib\Database\Exception\EmptyColumnNameException;
use App\Lib\Database\Exception\EmptyColumnTypeException;
use App\Lib\Database\Exception\EmptyAttributeArrayException;

class AttributeValidator
{
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
    }
}