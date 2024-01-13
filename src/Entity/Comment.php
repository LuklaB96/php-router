<?php
namespace App\Entity;

use App\Lib\Database\Enums\ColumnType;
use App\Lib\Database\Entity\Entity;
use App\Lib\Database\Enums\RelationType;
use App\Lib\Database\Mapping\Attributes\Column;
use App\Lib\Database\Mapping\Attributes\Relation;

class Comment extends Entity
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
    protected string $content;
    /**
     * Relation attribute will be used by framework to create new column that will be representing our relation.
     * Variable can be named as we wish it to be, framework will use class name and property with primary key attribute as column name, 
     * if our class is named Person, and primary key is called id, the end result will be person_id.
     * 
     * RelationType should point out if 
     * 
     */
    #[Relation(targetEntity: Post::class, relationType: RelationType::MANY_TO_ONE)]
    protected Post $post;
    /**
     * We need to create additional relation variable, it needs to be exactly classname_entityPrimaryKeyName, otherwise it will not work.
     * @var int
     */
    protected int $post_id;
    #[Relation(targetEntity: User::class, relationType: RelationType::MANY_TO_ONE)]
    protected User $user;
    protected int $user_id;
    #[Column(type: ColumnType::TIMESTAMP)]
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
    public function getPost(): Post
    {
        return $this->post;
    }
    public function setPost(Post $post): self
    {
        $this->post = $post;
        return $this;
    }
    public function getContent(): string
    {
        return $this->content;
    }
    public function setContent($content): self
    {
        $this->content = $content;
        return $this;
    }
}
