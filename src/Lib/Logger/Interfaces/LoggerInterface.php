<?php
namespace App\Lib\Logger\Interfaces;

use App\Lib\Logger\LoggerConfig;

/**
 * Summary of LoggerInterface
 */
interface LoggerInterface
{
    /**
     * Returns final message that is inserted to .log file
     *
     * @param  string                       $message
     * @param  \App\Lib\Logger\LoggerConfig $config
     * @return string
     */
    public function log(string $message, LoggerConfig $config): string;
    /**
     * Get formatted message
     *
     * @param  string $message
     * @return string
     */
    public static function formatMessage(string $message): string;
}

?>
