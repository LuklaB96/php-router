<?php
namespace App\Lib\Database\Exception;

use App\Lib\Database\Interface\DatabaseExceptionInterface;

class EmptyColumnNameException extends \Exception implements DatabaseExceptionInterface
{
    public function __construct($message = "Column name cannot be empty.", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
