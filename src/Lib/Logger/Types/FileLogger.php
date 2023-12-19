<?php
namespace App\Lib\Logger\Types;

use App\Lib\Logger\Interfaces\LoggerInterface;
use App\Lib\Logger\LoggerConfig;
use App\Lib\Logger\Utility\LoggerDirectory;

class FileLogger implements LoggerInterface
{
    /**
     * Returns final message that is inserted to .log file in specified directory
     *
     * @param  string                       $message
     * @param  \App\Lib\Logger\LoggerConfig $config
     * @return string
     */
    public function log(string $message, LoggerConfig $config): string
    {
        //file logger logic here
        LoggerDirectory::create($config);

        $logFile = $config->getLogLevel()->value . '_' . date('d-M-Y') . '.log';
        $formattedMessage = self::formatMessage($message);
        //add message to log file, creates file if does not exist
        file_put_contents($config->getLogDir() . '/' . $config->getName() . '/' . $logFile, $formattedMessage . "\n", FILE_APPEND);

        return $formattedMessage;
    }
    /**
     * Get formatted message
     *
     * @param  string $message
     * @return string
     */
    public static function formatMessage(string $message): string
    {
        return '[' . date('H:i:s') . ']' . " $message";
    }
}
