<?php
namespace App\Lib\ExceptionHandler;

use App\Lib\Config;

class ExceptionHandler
{
    private string $exceptionRoute;
    public function __construct(string $exceptionRoute = '')
    {
        $this->exceptionRoute = empty($exceptionRoute) ? Config::get('EXCEPTION_ROUTE') : $exceptionRoute;
    }
    public function handle(\Throwable $exception)
    {
        error_log($exception);
    }
}

?>