<?php
namespace App\Lib\Entity;

use App\Lib\Config;

class EntityManager
{
    private static $instances = null;
    private $conn;
    private function __construct()
    {
        $dbhost = Config::get('DB_HOST');
        $dbuser = Config::get('DB_USER');
        $dbpassword = Config::get('DB_PASSWORD');

        $this->setConnection($dbhost, $dbuser, $dbpassword);
    }
    public function setConnection($dbhost, $dbuser, $dbpassword = '')
    {
        $dsn = "mysql:host=$dbhost;";
        try {
            $this->conn = new \PDO($dsn, $dbuser, $dbpassword);
        } catch (\PDOException $e) {
            $this->conn = null;
        }
    }
    public static function getInstance()
    {
        if (self::$instances == null) {
            self::$instances = new self();
        }
        return self::$instances;
    }

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
    public function execute($sql, array $data = []): string
    {
        $stmt = $this->conn->prepare($sql);
        if (empty($data)) {
            try {
                $result = $stmt->execute();
                return 'ok';
            } catch (\PDOException $e) {
                return $e->getMessage();
            }
        }
        try {
            $stmt->execute($data);
            return 'ok';
        } catch (\PDOException $e) {
            return $e->getMessage();
        }
    }

}

?>