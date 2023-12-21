<?php
namespace App\Lib\Database\Interface;


interface DatabaseExceptionInterface
{
    public function __construct($message = '', $code = 0, \Throwable $previous = null);
}
