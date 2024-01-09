<?php
namespace App\Lib\Database\Mapping\Attributes;

use App\Lib\Database\Enums\ColumnType;

class Column
{
    public string $name;
    public ColumnType $type;
    public ?int $length;
    public bool $primaryKey;
    public bool $autoIncrement;
    public bool $nullable;
    /**
     * Be sure to read and follow rules for SQL column creation.
     * 
     * Functions associated with this attribute can throw Exceptions.
     * 
     * @param string                             $name
     * @param \App\Lib\Database\Enums\ColumnType $type 
     * @param int                                $length
     * @param bool                               $primaryKey
     * @param bool                               $autoIncrement
     * @param bool                               $nullable
     */
    public function __construct(
        string $name,
        ColumnType $type,
        ?int $length = 0,
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
