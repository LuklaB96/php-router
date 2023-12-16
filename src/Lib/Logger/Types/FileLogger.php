<?php
namespace App\Lib\Logger\Types;

use App\Lib\Logger\Interfaces\LoggerInterface;
use App\Lib\Logger\LoggerConfig;

class FileLogger implements LoggerInterface
{
    public function log(string $message, LoggerConfig $config): void
    {
        //file logger logic here
        if (!file_exists($config->getLogDir() . '/' . $config->getName())) {
            // create directory if does not exists
            mkdir($config->getLogDir() . '/' . $config->getName(), 0777, true);
        }

        $logFile = $config->getLogLevel()->value . '_' . date('d-M-Y') . '.log';
        $formattedMessage = '[' . date('H:i:s') . ']' . " $message";
        //add message to log file
        file_put_contents($config->getLogDir() . '/' . $config->getName() . '/' . $logFile, $formattedMessage . "\n", FILE_APPEND);
    }
}
