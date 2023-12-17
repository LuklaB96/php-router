<?php

use App\Lib\Logger\Enums\LogLevel;
use App\Lib\Logger\LoggerConfig;
use App\Lib\Logger\Types\FileLogger;
use App\Lib\Logger\Utility\LoggerDirectory;
use phpDocumentor\Reflection\Types\Void_;
use PHPUnit\Framework\TestCase;
use App\Lib\Logger\Logger;

final class FileLoggerDefaultConfigTest extends TestCase
{
    private ?Logger $loggerWithDefaultConfig;

    protected function setUp(): void
    {
        //default logger instance
        $this->loggerWithDefaultConfig = Logger::getInstance(new FileLogger());
    }
    protected function tearDown(): void
    {
        $this->loggerWithDefaultConfig = null;
    }
    public function testInitalizable(): void
    {
        $this->assertInstanceOf(Logger::class, $this->loggerWithDefaultConfig);
    }
    public function testFormatMessage(): void
    {
        $message = 'test message';
        $formattedMessage = FileLogger::formatMessage($message);
        $this->assertEquals('[' . date('H:i:s') . ']' . " $message", $formattedMessage);
    }
    public function testMessageDefaultConfig(): void
    {
        $message = 'test message';
        $returnedMessage = $this->loggerWithDefaultConfig->log('test message');
        $formattedMessage = FileLogger::formatMessage($message);
        $this->assertEquals($formattedMessage, $returnedMessage);
    }
}