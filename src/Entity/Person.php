<?php
namespace App\Entity;

use App\Lib\Database\Enums\ColumnType;
use App\Lib\Database\Entity\Entity;
use App\Lib\Database\Enums\RelationType;
use App\Lib\Database\Mapping\Attributes\Column;
use App\Lib\Database\Mapping\Attributes\Relation;

class Person extends Entity
{
    /**
     * This is an example class that is extending Entity parent class
     * Every property should be protected/private
     * Every property should have getters and setters (except for auto increment primary keys) to read/write from database.
     * Attributes are used to tell parent class which properties should be used as column names and definitions.
     * Use #[Column(...)] attribute to define them, examples are below.
     * Use #[Relation(...)] to create a relation between entities.
     */

    /**
     * Default primary key for our table
     *
     * @var 
     */
    #[Column(type: ColumnType::INT, primaryKey: true, autoIncrement: true)]
    protected int $id;
    /**
     * Nullable varchar(32) columns
     */
    #[Column(type: ColumnType::VARCHAR, nullable: true, length: 32)]
    protected ?string $firstName;
    #[Column(type: ColumnType::VARCHAR, nullable: true, length: 32)]
    protected ?string $lastName;
    #[Column(type: ColumnType::VARCHAR, nullable: false, length: 32)]
    protected $login;

    public function getId()
    {
        return $this->id;
    }
    public function getFirstName()
    {
        return $this->firstName;
    }
    public function getLastName()
    {
        return $this->lastName;
    }
    public function getLogin()
    {
        return $this->login;
    }
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }
    public function setLogin($login)
    {
        $this->login = $login;
    }
}
