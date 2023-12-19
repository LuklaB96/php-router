<?php
namespace App\Lib\Database\Exception;

class EmptyColumnNameException extends \Exception
{
    public function __construct($message = "Column name cannot be empty.", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
