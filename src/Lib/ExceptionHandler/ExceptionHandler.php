<?php
namespace App\Lib\ExceptionHandler;

use App\Lib\Config;
use App\Lib\Logger\Enums\LogLevel;
use App\Lib\Logger\Logger;
use App\Lib\Logger\Types\FileLogger;

class ExceptionHandler
{
    private string $exceptionRoute;
    private Logger $logger;
    public function __construct(string $exceptionRoute = '')
    {
        $this->exceptionRoute = empty($exceptionRoute) ? Config::get('EXCEPTION_ROUTE') : $exceptionRoute;
        $this->logger = Logger::getInstance(new FileLogger());
    }
    public function handle(\Throwable $exception)
    {
        $code = $exception->getCode() === 0 ? 'ERROR' : $exception->getCode();
        $message = $exception->getMessage();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $trace = $exception->getTrace();

        $exceptionMessage = "[EXCEPTION][$code] $message in $file: $line";
        error_log($exceptionMessage);
        $this->logger->log($exceptionMessage, logLevel: LogLevel::ERROR);
        foreach ($trace as $key => $traceInfo) {
            $traceMessage = "[EXCEPTION][TRACE] #$key $traceInfo[file]: $traceInfo[function]()";
            error_log($traceMessage);
            $this->logger->log($traceMessage, logLevel: LogLevel::ERROR);
        }
        if ($_SERVER['REQUEST_URI'] !== $this->exceptionRoute) {
            header("Location: $this->exceptionRoute");
        }


    }
}

?>