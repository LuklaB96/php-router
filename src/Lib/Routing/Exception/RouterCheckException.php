<?php
namespace App\Lib\Routing\Exception;

class RouterCheckException extends \Exception
{
    public function __construct(string $message = null, $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
