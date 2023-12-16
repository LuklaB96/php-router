<?php
namespace App\Entity;

use App\Lib\Entity\Entity;


class Post extends Entity
{
    protected $properties = [
        'id' => 'INT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
        'title' => 'VARCHAR(250) NOT NULL',
        'body' => 'LONGTEXT NOT NULL',
    ];
}