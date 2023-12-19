<?php

namespace App\Lib\Logger;

use App\Lib\Logger\Enums\LogLevel;
use App\Lib\Config;



/**
 * Summary of LoggerMessage
 */
class LoggerConfig
{
    /**
     * Mainly used to identify object instance
     * Additionally all .log files are created in /LOG_PATH/YOUR_LOGGER_NAME/ directory.
     *
     * @var string
     */
    private $name = '';
    private $absolutePath = '';
    private LogLevel $logLevel;
    /**
     * Used to check whether the log directory is the default or not
     *
     * @var 
     */
    private $default = true;
    public function __construct(string $name = null, string $absolutePath = null, LogLevel $logLevel = null)
    {
        $this->name = $name ?? 'App';
        $this->logLevel = $logLevel ?? LogLevel::DEBUG;
        $this->absolutePath = $absolutePath ?? Config::get('LOG_PATH', __DIR__ . '/../../logs');
        //check if any config properties are set as custom (not null)
        if (isset($name) || isset($absolutePath) || isset($logLevel)) {
            $this->default = false;
        }
    }
    /**
     * Sets name of the instance.
     *
     * @param  string $name
     * @return void
     */
    public function setName(string $name)
    {
        $this->name = $name;
        if ($this->default) {
            $this->default = false;
        }
    }
    /**
     * Set absolute path to directory where all .log files are stored
     *
     * @param  string $path
     * @return void
     */
    public function setLogDir(string $path)
    {
        $this->absolutePath = $path;
        if ($this->default) {
            $this->default = false;
        }
    }
    /**
     * LogLevel will be used as prefix for your .log file e.g. LogLevel::DEBUG file will look like debug_date.log
     *
     * @param  \App\Lib\Logger\Enums\LogLevel $logLevel
     * @return void
     */
    public function setLogLevel(LogLevel $logLevel)
    {
        $this->logLevel = $logLevel;
        if ($this->default) {
            $this->default = false;
        }
    }
    /**
     * Gets current instance name
     *
     * @return array
     */
    public function getName(): string
    {
        return $this->name;
    }
    /**
     * Get current absolute path to directory where .log files are stored
     *
     * @return string
     */
    public function getLogDir(): string
    {
        return $this->absolutePath;
    }
    /**
     * Get LogLevel Enum
     *
     * @return \App\Lib\Logger\Enums\LogLevel
     */
    public function getLogLevel(): LogLevel
    {
        return $this->logLevel;
    }
    public function isDefault(): bool
    {
        return $this->default;
    }

}
