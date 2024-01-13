<?php

namespace App\Lib\Logger;

use App\Lib\Logger\Enums\LogLevel;
use App\Lib\Logger\Interfaces\LoggerInterface;

/**
 * Logger will save all log files to 'logs/instanceName'
 * File format is 'LogLevel_todayDate.log'
 * Log message format is '[HH:MM:SS] message'
 */
class Logger
{
    private static $instances = [];
    private LoggerInterface $logger;
    private LoggerConfig $config;
    /**
     * 
     * @param \App\Lib\Logger\Interfaces\LoggerInterface $logger
     * @param LoggerConfig                               $config
     */
    private function __construct(LoggerInterface $logger, LoggerConfig $config)
    {
        //check if $logger is valid instance for any of current Logger Types
        $this->logger = LoggerFactory::createLogger($logger);
        $this->config = $config;
    }

    /**
     * Create or get existing instance with given name, default appName = 'App'
     *
     * @param  \App\Lib\Logger\Interfaces\LoggerInterface $logger
     * @param  \App\Lib\Logger\LoggerConfig               $config
     * @return \App\Lib\Logger\Logger
     */
    public static function getInstance(LoggerInterface $logger, LoggerConfig $config = new LoggerConfig()): Logger
    {
        if (empty(self::$instances[$config->getName()])) {
            return self::$instances[$config->getName()] = new Logger($logger, $config);
        }

        return self::$instances[$config->getName()];
    }
    /**
     * Save the message to a predetermined environment like file,database etc.
     *
     * @param  string $message
     * @return void
     */
    public function log(string $message, LogLevel $logLevel = LogLevel::DEBUG): string
    {
        $this->config->setLogLevel($logLevel);
        $formattedMessage = $this->logger->log($message, $this->config);
        return $formattedMessage;
    }
    public function getConfig(): LoggerConfig
    {
        return $this->config;
    }

}

