<?php
namespace App\Lib\Database\Mapping;

use App\Lib\Database\Enums\ColumnType;

class Column
{
    public string $name;
    public ColumnType $type;
    public ?int $length;
    public bool $primaryKey;
    public bool $autoIncrement;
    public bool $nullable;
    public function __construct(
        string $name,
        ColumnType $type,
        ?int $length = null,
        bool $primaryKey = false,
        bool $autoIncrement = false,
        bool $nullable = false
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->length = $length;
        $this->primaryKey = $primaryKey;
        $this->autoIncrement = $autoIncrement;
        $this->nullable = $nullable;
    }
}
