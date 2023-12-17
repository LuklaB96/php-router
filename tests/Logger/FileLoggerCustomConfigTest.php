<?php

use App\Lib\Logger\Enums\LogLevel;
use App\Lib\Logger\LoggerConfig;
use App\Lib\Logger\Types\FileLogger;
use App\Lib\Logger\Utility\LoggerDirectory;
use phpDocumentor\Reflection\Types\Void_;
use PHPUnit\Framework\TestCase;
use App\Lib\Logger\Logger;

final class FileLoggerCustomConfigTest extends TestCase
{
    private ?LoggerConfig $config;
    private ?Logger $loggerWithCustomConfig;

    protected function setUp(): void
    {
        //config setup
        $this->config = new LoggerConfig();
        $this->config->setName('CustomLogger');
        $this->config->setLogDir(__DIR__ . '/CustomLogDir');
        $this->config->setLogLevel(LogLevel::INFO);

        //custom config logger instance
        $this->loggerWithCustomConfig = Logger::getInstance(new FileLogger(), $this->config);
    }
    protected function tearDown(): void
    {
        $this->loggerWithDefaultConfig = null;
        $this->loggerWithCustomConfig = null;
        //delete custom directory if created during tests.
        LoggerDirectory::delete($this->config);
        $this->config = null;
    }
    public function testInitalizable(): void
    {
        $this->assertInstanceOf(Logger::class, $this->loggerWithCustomConfig);
    }
    public function testFormatMessage(): void
    {
        $message = 'test message';
        $formattedMessage = FileLogger::formatMessage($message);
        $this->assertEquals('[' . date('H:i:s') . ']' . " $message", $formattedMessage);
    }
    public function testMessageCustomConfig(): void
    {
        $message = 'test message';
        $returnedMessage = $this->loggerWithCustomConfig->log('test message');
        $formattedMessage = FileLogger::formatMessage($message);
        $this->assertEquals($formattedMessage, $returnedMessage);
    }
}