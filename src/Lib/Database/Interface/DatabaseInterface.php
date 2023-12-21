<?php
namespace App\Lib\Database\Interface;

/**
 * @property ?DatabaseInterface $instance
 */
interface DatabaseInterface
{
    /**
     * Sets connection to database
     * Supports: MySQL
     *
     * @param  string $dbhost
     * @param  string $dbuser
     * @param  string $dbpassword
     * @return bool
     */
    public function setConnection(#[\SensitiveParameter] string $dbhost, #[\SensitiveParameter] string $dbuser, #[\SensitiveParameter] string $dbpassword = null): bool;
    /**
     * Get existing instance if exists, return new otherwise
     *
     * @return \App\Lib\Database\Database
     */
    public static function getInstance(): DatabaseInterface;
    /**
     * Check if the connection instance was successfully created.
     *
     * @return bool
     */
    public function isConnected(): bool;
    /**
     * Execute single SQL query
     *
     * @param  string $query 
     * @param  array  $data  for prepared statements and bound parameters
     * @return string|array
     */
    public function execute(string $query, array $data = []): string|array;
}
