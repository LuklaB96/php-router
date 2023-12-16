<?php

use App\Lib\Logger\LoggerConfig;
use App\Lib\Logger\Types\FileLogger;
use PHPUnit\Framework\TestCase;
use App\Lib\Logger\Logger;

final class LoggerTest extends TestCase
{
    public function testMessage(): void
    {
        //Assign
        $loggerCfg = new LoggerConfig();
        $loggerCfg->setName('Test');
        $logger = Logger::getInstance(new FileLogger(), $loggerCfg);

        //Act
        $message = $logger->message("test message");

        //Assert
        $this->assertEquals("test message", $message);
    }
}