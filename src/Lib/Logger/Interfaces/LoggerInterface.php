<?php
namespace App\Lib\Logger\Interfaces;

use App\Lib\Logger\LoggerConfig;

/**
 * Summary of LoggerInterface
 */
interface LoggerInterface
{
    public function log(string $message, LoggerConfig $config): void;
}

?>