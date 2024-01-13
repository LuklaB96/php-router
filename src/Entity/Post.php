<?php
namespace App\Entity;

use App\Lib\Database\Enums\ColumnType;
use App\Lib\Database\Entity\Entity;
use App\Lib\Database\Enums\RelationType;
use App\Lib\Database\Mapping\Attributes\Column;
use App\Lib\Database\Mapping\Attributes\Relation;

class Post extends Entity
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
    protected string $title;
    #[Column(type: ColumnType::TEXT, nullable: false)]
    protected string $content;
    #[Relation(targetEntity: User::class, relationType: RelationType::MANY_TO_ONE)]
    protected User $user;
    protected int $user_id;
    #[Column(type: ColumnType::TIMESTAMP, nullable: false)]
    protected string $created_at;

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }
    public function getId(): int
    {
        return $this->id;
    }
    public function getAuthorId(): int
    {
        return $this->user_id;
    }
    public function getAuthor(): User
    {
        return $this->user;
    }
    public function setAuthor(User $user): self
    {
        $this->user = $user;
        return $this;
    }
    public function getTitle(): string
    {
        return $this->title;
    }
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }
    public function getContent()
    {
        return $this->content;
    }
    public function setContent($content): self
    {
        $this->content = $content;
        return $this;
    }
}
