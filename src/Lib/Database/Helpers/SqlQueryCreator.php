<?php
namespace App\Lib\Database\Helpers;

use App\Lib\Database\Mapping\Column;

class SqlQueryCreator
{
    /**
     * Returns a create table query string
     * @param array $columns
     * @param string $tableName
     * @param string $dbname
     * @param bool $checkExists
     * @return string
     */
    public static function createTableQuery(array $columns, string $tableName, string $dbname, bool $checkExists = false): string
    {

        $sql = "CREATE TABLE `$dbname`.`$tableName` (";
        if ($checkExists) {
            $sql = "CREATE TABLE IF NOT EXISTS `$dbname`.`$tableName` (";
        }
        $columnDefinitions = [];
        //get table properties from entity class
        foreach ($columns as $column) {

            $columnDefinitions[] = "`$column->name`" . ' ' . self::createColumnDefinition($column);
        }

        $sql .= implode(', ', $columnDefinitions);
        $sql .= ")";
        return $sql;
    }
    private static function createColumnDefinition(Column $column): string
    {
        $definition = strtoupper($column->type->value);
        if ($column->length) {
            $definition .= "($column->length)";
        }
        if ($column->autoIncrement) {
            $definition .= ' AUTO_INCREMENT';
        }
        if ($column->primaryKey) {
            $definition .= ' PRIMARY KEY';
        }
        if ($column->nullable) {
            if ($column->autoIncrement || $column->primaryKey) {
                throw new \Exception('Columns with auto increment or primary key can not be set to nullable');
            }
            $definition .= ' NULL';
        } else {
            $definition .= ' NOT NULL';
        }
        return $definition;
    }
}