<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Lib\Config;

$dbhost = Config::get('DB_HOST', '127.0.0.1');
$dbuser = Config::get('DB_USER', 'root');
$dbpassword = Config::get('DB_PASSWORD', '');
$dbname = Config::get('DB_NAME', 'app_db');

$conn = new \PDO("mysql:host=$dbhost", $dbuser, $dbpassword);

$sql = "CREATE DATABASE IF NOT EXISTS $dbname;";


$conn->exec($sql);
echo "Database $dbname created.</br>";
?>