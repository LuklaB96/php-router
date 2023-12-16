<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Entity\Post;
use App\Lib\Migrations\Migrations;

$migrations = new Migrations();

//create all entities here
$post = new Post();

//create tables from entity properties
$migrations->create($post);

?>