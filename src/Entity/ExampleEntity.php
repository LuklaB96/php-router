<?php
namespace App\Entity;

use App\Lib\Database\Enums\ColumnType;
use App\Lib\Database\Entity\Entity;
use App\Lib\Database\Mapping\Attributes\Column;

class ExampleEntity extends Entity
{
    /**
     * This is an example class that is extending Entity parent class
     * Every property should be protected/private
     * Every property should have getters and setters to read/write from database.
     * Attributes are used to tell parent class which properties should be used as column names and definitions.
     * Use #[Column] attribute to define them, examples are below.
     */

    /**
     * Default primary key for our table
     *
     * @var 
     */
    #[Column(type: ColumnType::INT, primaryKey: true, autoIncrement: true, length: 6)]
    protected $id;
    /**
     * Custom entity name, leave it as empty string if you want it to be automatically set to class name.
     * You can delete this variable if you dont need it because it is extended from Entity class anyway.
     *
     * @var string
     */
    protected $name;

    #[Column(type: ColumnType::LONGTEXT, nullable: true)]
    protected $title;
    #[Column(type: ColumnType::TEXT, nullable: true)]
    protected $description;

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getTitle()
    {
        return $this->title;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function setTitle($title)
    {
        $this->title = $title;
    }
    public function setDescription($description)
    {
        $this->description = $description;
    }
}
