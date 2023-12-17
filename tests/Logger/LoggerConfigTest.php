<?php

use App\Lib\Logger\Enums\LogLevel;
use App\Lib\Logger\LoggerConfig;
use phpDocumentor\Reflection\Types\Void_;
use PHPUnit\Framework\TestCase;

final class LoggerConfigTest extends TestCase
{
    private ?LoggerConfig $config;

    protected function setUp(): void
    {
        $this->config = new LoggerConfig();
    }
    public function testInitializable(): void
    {
        $this->assertInstanceOf(LoggerConfig::class, $this->config);
    }
    public function testGetSetName(): void
    {
        $this->config->setName('test');

        $this->assertEquals('test', $this->config->getName());
    }
    public function testGetSetLogDir(): void
    {
        $dirPath = __DIR__ . '/logs';
        $this->config->setLogDir($dirPath);

        $this->assertEquals($dirPath, $this->config->getLogDir());
    }
    public function testGetSetLogLevel(): void
    {
        $logLevel = LogLevel::DEBUG;
        $this->config->setLogLevel($logLevel);
        $this->assertEquals($logLevel, $this->config->getLogLevel());
    }
}