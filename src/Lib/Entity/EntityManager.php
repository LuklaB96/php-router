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
        $dbname = Config::get('DB_NAME');
        $dbuser = Config::get('DB_USER');
        $dbpassword = Config::get('DB_PASSWORD');

        try {
            $this->conn = new \PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpassword);
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

    public function execute($sql)
    {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
    }

}

?>