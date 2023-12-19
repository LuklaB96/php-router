<?php
namespace App\Lib\Database\Exception;

class EmptyAttributeArrayException extends \Exception
{
    public function __construct($message = 'Attributes array cannot be empty.', $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
