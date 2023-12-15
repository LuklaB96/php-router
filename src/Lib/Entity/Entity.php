<?php
namespace App\Lib\Entity;

use App\Lib\Config;

class Entity
{
    private $dbhost = Config::get('DB_HOST');
    private $dbname = Config::get('DB_NAME');
    private $dbuser = Config::get('DB_USER');
    private $dbpassword = Config::get('DB_PASSWORD');
    private $conn;
    private $entity = '';
    function __construct()
    {
        $this->entity = get_class($this);
        $this->conn = new \PDO("mysql:host=$this->dbhost;dbname=$this->dbname", $this->dbuser, $this->dbpassword);
    }
    public function insert()
    {

    }
}


?>