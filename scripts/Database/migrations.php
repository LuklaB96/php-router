<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Entity\Comment;
use App\Entity\ActivationCode;
use App\Entity\Post;
use App\Entity\User;
use App\Lib\Database\Migrations;

$migrations = new Migrations();

//create all entities here
$post = new Post();
$comment = new Comment();
$user = new User();
$emailActivation = new ActivationCode();

//create tables from entity properties
//tables without foreign keys
$migrations->create($user);

//tables with foreign keys
$migrations->create($post);
$migrations->create($comment);
$migrations->create($emailActivation);



?>