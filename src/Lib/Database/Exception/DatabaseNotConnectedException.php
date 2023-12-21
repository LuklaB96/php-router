<?php
namespace App\Lib\Database\Exception;

use App\Lib\Database\Interface\DatabaseExceptionInterface;

/**
 * Throwed when not connected to database but is trying to use its functionality
 */
class DatabaseNotConnectedException extends \Exception implements DatabaseExceptionInterface
{
    public function __construct($message = 'You are not connected to database', $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
