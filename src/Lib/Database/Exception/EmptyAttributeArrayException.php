<?php
namespace App\Lib\Database\Exception;

use App\Lib\Database\Interface\DatabaseExceptionInterface;

class EmptyAttributeArrayException extends \Exception implements DatabaseExceptionInterface
{
    public function __construct($message = 'Attributes array cannot be empty.', $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
