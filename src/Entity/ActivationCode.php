<?php
namespace App\Entity;

use App\Lib\Database\Enums\ColumnType;
use App\Lib\Database\Entity\Entity;
use App\Lib\Database\Enums\RelationType;
use App\Lib\Database\Mapping\Attributes\Column;
use App\Lib\Database\Mapping\Attributes\Relation;

class ActivationCode extends Entity
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
    #[Column(type: ColumnType::TEXT, nullable: true)]
    protected string $activation_code;
    #[Column(type: ColumnType::BOOLEAN, nullable: true)]
    protected bool $used;
    #[Relation(targetEntity: User::class, relationType: RelationType::MANY_TO_ONE)]
    protected User $user;
    protected int $user_id;
    public function getId(): int
    {
        return $this->id;
    }
    public function getUsed(): bool
    {
        return $this->used;
    }
    public function setUsed($used): self
    {
        $this->used = $used;
        return $this;
    }
    public function getUserId(): int
    {
        return $this->user_id;
    }
    public function getUser(): User
    {
        return $this->user;
    }
    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }
    public function setActivationCode($code): self
    {
        $this->activation_code = $code;
        return $this;
    }
    public function getActivationCode(): string
    {
        return $this->activation_code;
    }
}
