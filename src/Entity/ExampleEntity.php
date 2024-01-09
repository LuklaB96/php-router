<?php
namespace App\Entity;

use App\Lib\Database\Enums\ColumnType;
use App\Lib\Database\Entity\Entity;
use App\Lib\Database\Enums\RelationType;
use App\Lib\Database\Mapping\Attributes\Column;
use App\Lib\Database\Mapping\Attributes\Relation;

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
    /**
     * Relation attribute will be used by framework to create new column that will be representing our relation.
     * Variable can be named as we wish it to be, framework will use class name and property with primary key attribute as column name, 
     * if our class is named Person, and primary key is called id, the end result will be person_id.
     * 
     */
    #[Relation(targetEntity: Person::class, relationType: RelationType::MANY_TO_ONE)]
    protected Person $person;
    /**
     * We need to create additional relation variable, it needs to be exactly classname_entityPrimaryKeyName, otherwise it will not work.
     * @var int
     */
    protected int $person_id;
    public function getPerson(): Person
    {
        return $this->person;
    }
    public function setPerson(Person $person): self
    {
        $this->person = $person;
        return $this;
    }

    public function getId()
    {
        return $this->id;
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
