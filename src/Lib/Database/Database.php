<?php
namespace App\Lib\Database;

use App\Lib\Database\Exception\DatabaseNotConnectedException;
use App\Lib\Database\Interface\DatabaseInterface;
use App\Lib\Config;

class Database implements DatabaseInterface
{
    /**
     * Singleton object instance
     *
     * @var 
     */
    private static ?DatabaseInterface $instance = null;
    /**
     * Main PDO connection
     *
     * @var 
     */
    private $conn;
    /**
     * Can be used as a container for last error thrown by database
     *
     * @var string
     */
    private $dbError = '';
    private function __construct()
    {
        $dbhost = Config::get('DB_HOST');
        $dbuser = Config::get('DB_USER');
        $dbpassword = Config::get('DB_PASSWORD');

        $this->setConnection($dbhost, $dbuser, $dbpassword);
    }
    public function setConnection(#[\SensitiveParameter] string $dbhost, #[\SensitiveParameter] string $dbuser, #[\SensitiveParameter] string $dbpassword = null): bool
    {
        $dsn = "mysql:host=$dbhost;";
        try {
            $this->conn = new \PDO($dsn, $dbuser, $dbpassword);
            return true;
        } catch (\PDOException $e) {
            $this->conn = null;
            $this->dbError = $e->getMessage();
            return false;
        }
    }
    public static function getInstance(): DatabaseInterface
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function isConnected(): bool
    {
        return $this->conn !== null;
    }
    public function execute(#[\SensitiveParameter] $query, array $data = []): string|array
    {
        if ($this->isConnected() == false) {
            throw new DatabaseNotConnectedException();
        }
        $stmt = $this->conn->prepare($query);
        if (empty($data)) {
            try {
                $stmt->execute();
                return $this->handleExecutionResult($stmt, $query);
            } catch (\PDOException $e) {
                return $this->handleExecutionException($e);
            }
        }

        try {
            $stmt->execute($data);
            return $this->handleExecutionResult($stmt, $query);
        } catch (\PDOException $e) {
            return $this->handleExecutionException($e);
        }
    }
    /**
     * This method checks the type of query executed and handles the result accordingly.
     *
     * @param  \PDOStatement $stmt 
     * @param  string        $query The executed SQL query
     * @return array|string 
     */
    private function handleExecutionResult(\PDOStatement $stmt, #[\SensitiveParameter] string $query)
    {
        // Check if the query is a SELECT statement
        $isSelectQuery = strtoupper(substr(trim($query), 0, 6)) === 'SELECT';

        if ($isSelectQuery) {
            // If it's a SELECT query, return all rows data
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            // For other queries, return 'ok'
            return 'ok';
        }
    }

    /**
     * Handles exception if thrown by execute
     *
     * @param  mixed $e
     * @return \PDOException
     */
    private function handleExecutionException($e)
    {
        return $e;
    }
}

?>