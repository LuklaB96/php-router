<?php

namespace App\Lib\Logger;

use App\Lib\Logger\Interfaces\LoggerInterface;


/**
 * Summary of LoggerMessage
 */
class LoggerFactory
{
    public static function createLogger(LoggerInterface $logger): LoggerInterface
    {
        $interfaces = class_implements($logger);
        if (in_array(LoggerInterface::class, $interfaces)) {
            return $logger;
        } else {
            throw new \InvalidArgumentException("Provided logger does not implement LoggerInterface");
        }
    }
}
