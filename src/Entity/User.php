<?php
namespace App\Entity;

use App\Lib\Database\Enums\ColumnType;
use App\Lib\Database\Entity\Entity;
use App\Lib\Database\Mapping\Attributes\Column;

class User extends Entity
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
    protected int $id;
    #[Column(type: ColumnType::VARCHAR, length: 32, nullable: false, unique: true)]
    protected string $login;
    #[Column(type: ColumnType::VARCHAR, length: 255, nullable: false)]
    protected string $password;
    #[Column(type: ColumnType::TEXT, nullable: false, unique: true)]
    protected string $email;
    #[Column(type: ColumnType::BOOLEAN, nullable: true)]
    protected bool $activated;
    public function getId()
    {
        return $this->id;
    }
    public function setLogin(string $login): self
    {
        $this->login = $login;
        return $this;
    }
    public function getLogin(): string
    {
        return $this->login;
    }
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }
    public function getPassword(): string
    {
        return $this->password;
    }
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }
    public function getEmail(): string
    {
        return $this->email;
    }
    public function getActivated(): bool
    {
        return $this->activated;
    }
    public function setActivated(bool $state): self
    {
        $this->activated = $state;
        return $this;
    }
}
