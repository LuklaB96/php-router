<?php
namespace App\Lib\Entity;

use App\Lib\Config;

class EntityManager
{
    /**
     * Singleton object instance
     * @var 
     */
    private static ?EntityManager $instance = null;
    /**
     * Main PDO connection
     * @var 
     */
    private $conn;
    /**
     * Can be used as a container for last error thrown by database
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
    /**
     * Sets connection to database, currently only mysql is supported
     * @param string $dbhost
     * @param string $dbuser
     * @param string $dbpassword
     * @return bool
     */
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
    /**
     * Get existing instance if exists, return new otherwise
     * @return \App\Lib\Entity\EntityManager
     */
    public static function getInstance(): EntityManager
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Check if PDO instance has estabilished connection
     * @return bool
     */
    public function isConnected(): bool
    {
        return $this->conn !== null;
    }

    /**
     * Execute single sql query
     * @param mixed $sql
     * @param array $data
     * @return void
     */
    public function execute($sql, array $data = []): string|array
    {
        if ($this->isConnected() == false)
            throw new \Exception("You are not connected to database: $this->dbError");
        $stmt = $this->conn->prepare($sql);
        if (empty($data)) {
            try {
                $stmt->execute();
                return $this->handleExecutionResult($stmt, $sql);
            } catch (\PDOException $e) {
                return $this->handleExecutionException($e);
            }
        }

        try {
            $stmt->execute($data);
            return $this->handleExecutionResult($stmt, $sql);
        } catch (\PDOException $e) {
            return $this->handleExecutionException($e);
        }
    }

    private function handleExecutionResult($stmt, $sql)
    {
        // Check if the query is a SELECT statement
        $isSelectQuery = strtoupper(substr(trim($sql), 0, 6)) === 'SELECT';

        if ($isSelectQuery) {
            // If it's a SELECT query, return all rows data
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            // For other queries, return 'ok'
            return 'ok';
        }
    }

    private function handleExecutionException($e)
    {
        return [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
        ];
    }
}

?>