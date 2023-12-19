<?php
namespace App\Lib\Database\Exception;

class EmptyColumnTypeException extends \Exception
{
    public function __construct($message = "Column type cannot be null.", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
