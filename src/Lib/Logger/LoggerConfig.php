<?php

namespace App\Lib\Logger;

use App\Lib\Logger\Enums\LogLevel;
use App\Lib\Config;



/**
 * Summary of LoggerMessage
 */
class LoggerConfig
{
    private $name = '';
    private $absolutePath = '';
    private LogLevel $logLevel;

    public function __construct()
    {
        $this->name = 'App';
        $this->logLevel = LogLevel::DEBUG;
        $this->absolutePath = Config::get('LOG_PATH', __DIR__ . '/../../logs');
    }
    /**
     * Mainly used to identify object instance
     * Additionally all .log files are created in /log/YOUR_LOGGER_NAME_/file.log directory.
     * @param string $name
     * @return void
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }
    /**
     * Set absolute path to directory where all .log files are stored
     * @param string $path
     * @return void
     */
    public function setLogDir(string $path)
    {
        $this->absolutePath = $path;
    }
    /**
     * LogLevel will be used as prefix for your .log file e.g. LogLevel::DEBUG file will look like debug_date.log
     * @param \App\Lib\Logger\Enums\LogLevel $logLevel
     * @return void
     */
    public function setLogLevel(LogLevel $logLevel)
    {
        $this->logLevel = $logLevel;
    }
    /**
     * Gets current instance name
     * @return array
     */
    public function getName(): string
    {
        return $this->name;
    }
    /**
     * Get current absolute path to directory where .log files are stored
     * @return string
     */
    public function getLogDir(): string
    {
        return $this->absolutePath;
    }
    /**
     * Get LogLevel Enum
     * @return \App\Lib\Logger\Enums\LogLevel
     */
    public function getLogLevel(): LogLevel
    {
        return $this->logLevel;
    }

}