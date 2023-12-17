<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Entity\ExampleEntity;
use App\Lib\Database\Migrations;

$migrations = new Migrations();

//create all entities here
$exampleEntity = new ExampleEntity();

//create tables from entity properties
$migrations->create($exampleEntity);

?>