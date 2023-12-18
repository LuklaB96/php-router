<?php
namespace App\Entity;

use App\Lib\Database\Enums\ColumnType;
use App\Lib\Entity\Entity;
use App\Lib\Database\Mapping\Column;

class ExampleEntity extends Entity
{
    /**
     * Custom entity name, leave it as empty string if you want it to be automatically set to class name.
     * You can delete this variable if you dont need it because it is extended from Entity class anyway.
     * @var string
     */
    protected $name = '';
    /**
     * Your table properties, key as column name, value as column properties.
     * @var array
     */
    protected $properties = [
        'id' => 'INT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
        'title' => 'VARCHAR(250) NOT NULL',
        'description' => 'LONGTEXT NOT NULL',
    ];

    #[Column(type: ColumnType::INT, primaryKey: true, autoIncrement: true)]
    private $id;
    #[Column(type: ColumnType::TEXT, nullable: true, length: 100)]
    private $title = 'test';
}