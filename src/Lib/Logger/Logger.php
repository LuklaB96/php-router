<?php

namespace App\Lib\Logger;

use App\Lib\Config;

/**
 * Logger will save all log files to 'logs/instanceName'
 * File format is 'instanceName_todayDate.log'
 * Log message format is '[HH:MM:SS] message'
 */
class Logger
{
    private static $instances = [];

    private $logDir;
    private $appName;
    private LogLevel $logLevel;
    public function __construct($appName = 'App', $config = null)
    {
        if (empty($config)) {
            $LOG_PATH = Config::get('LOG_PATH', __DIR__ . '/../../logs');

            //default config setup
            $config = [
                'logDir' => $LOG_PATH,
                'appName' => $appName,
                'logLevel' => LogLevel::DEBUG,
            ];
        }
        $this->setup($config);
    }

    /**
     * Create or get existing instance with given name, default appName = 'App'
     * @param string $appName
     * @param array $config
     * @return \App\Lib\Logger\Logger
     */
    public static function getInstance(string $appName = 'App', array $config = null): Logger
    {
        if (empty(self::$instances[$appName])) {
            return self::$instances[$appName] = new Logger($appName, $config);
        }

        return self::$instances[$appName];
    }

    private function setup($config): void
    {
        $this->logDir = $config['logDir'];
        $this->appName = $config['appName'];
        $this->logLevel = $config['logLevel'];
    }
    /**
     * Save message to log file from instance
     * @param string $message
     * @return void
     */
    public function message(string $message): string
    {
        if (!file_exists($this->logDir . '/' . $this->appName)) {
            // create directory if does not exists
            mkdir($this->logDir . '/' . $this->appName, 0777, true);
        }


        $logFile = $this->appName . '_' . date('d-M-Y') . '.log';
        $formattedMessage = '[' . date('H:i:s') . ']' . " $message";
        //add message to log file
        file_put_contents($this->logDir . '/' . $this->appName . '/' . $logFile, $formattedMessage . "\n", FILE_APPEND);

        return $message;
    }

}

