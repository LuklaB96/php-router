<?php
namespace App\Lib\Database\Mapping\Attributes;

use App\Lib\Database\Entity\Entity;
use App\Lib\Database\Enums\RelationType;


class Relation
{
    public string $targetEntity;
    public RelationType $relationType;
    public Column $primaryKeyColumn;

    public function __construct(string $targetEntity, RelationType $relationType)
    {
        $this->targetEntity = $targetEntity;
        $this->relationType = $relationType;
    }
}
