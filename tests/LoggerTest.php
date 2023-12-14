<?php

use PHPUnit\Framework\TestCase;
use App\Lib\Logger\Logger;

final class LoggerTest extends TestCase
{
    public function testMessage(): void
    {
        //Assign
        $logger = Logger::getInstance();

        //Act
        $message = $logger->message("test message");

        //Assert
        $this->assertEquals("test message", $message);
    }
}