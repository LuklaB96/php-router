<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Lib\Config;

$dbhost = Config::get('DB_HOST');
$dbname = Config::get('DB_NAME');
$testdbname = Config::get('TEST_DB_NAME');
$dbuser = Config::get('DB_USER');
$dbpassword = Config::get('DB_PASSWORD');

$conn = new \PDO("mysql:host=$dbhost", $dbuser, $dbpassword);

$sql = "
    CREATE DATABASE IF NOT EXISTS $dbname;
    CREATE DATABASE IF NOT EXISTS $testdbname;
";

$conn->exec($sql);
echo 'database_create.php executed without errors, check database.';
?>