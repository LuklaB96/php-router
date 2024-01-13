<?php
namespace App\Lib\ErrorHandler;

use App\Lib\Config;
use App\Lib\Logger\Enums\LogLevel;
use App\Lib\Logger\Logger;
use App\Lib\Logger\Types\FileLogger;

class ErrorHandler
{
    private Logger $logger;
    private string $errorRoute = '';
    public function __construct(string $errorRoute = '')
    {
        $this->$errorRoute = empty($errorRoute) ? Config::get('ERROR_ROUTE') : $errorRoute;
        $this->logger = Logger::getInstance(new FileLogger());
    }
    public function handle($errno, $errstr, $errfile, $errline)
    {
        $errorTypeName = $this->getErrorLevel($errno);
        $errorMessage = "[$errorTypeName] $errstr in $errfile: $errline";
        $this->logger->log($errorMessage, logLevel: LogLevel::ERROR);
        error_log($errorMessage);
        if ($_SERVER['REQUEST_URI'] !== $this->errorRoute) {
            header("Location: $this->errorRoute");
        }
    }
    private function getErrorLevel($errno): string
    {
        switch ($errno) {
            case E_ERROR:
                return 'FATAL';
            case E_WARNING:
                return 'WARNING';
            case E_NOTICE:
                return 'NOTICE';
            default:
                return 'UNKNOWN ERROR';
        }

    }
}

?>