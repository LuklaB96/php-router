<?php
namespace App\Lib\Database\Exception;

use App\Lib\Database\Interface\DatabaseExceptionInterface;

class EmptyColumnTypeException extends \Exception implements DatabaseExceptionInterface
{
    public function __construct($message = "Column type cannot be null.", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
