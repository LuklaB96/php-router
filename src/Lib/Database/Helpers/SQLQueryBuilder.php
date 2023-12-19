<?php
namespace App\Lib\Database\Helpers;

use App\Lib\Database\Mapping\Column;

/**
 * This class helps to create queries like INSERT|UPDATE|SELECT|DELETE|CREATE etc. for our database from variable attributes and class properties
 */
class SQLQueryBuilder
{
    /**
     * Returns a create table query string
     * @param array $columns
     * @param string $tableName
     * @param string $dbname
     * @param bool $checkExists
     * @return string
     */
    public static function createTable(array $columns, string $tableName, string $dbname, bool $checkExists = false): string
    {

        $query = "CREATE TABLE `$dbname`.`$tableName` (";
        if ($checkExists) {
            $query = "CREATE TABLE IF NOT EXISTS `$dbname`.`$tableName` (";
        }
        $columnDefinitions = [];
        //get table properties from entity class
        foreach ($columns as $column) {

            $columnDefinitions[] = "`$column->name`" . ' ' . self::createColumnDefinition($column);
        }

        $query .= implode(', ', $columnDefinitions);
        $query .= ");";
        return $query;
    }
    /**
     * Creates a column definition from avaible data in Column object
     * @param \App\Lib\Database\Mapping\Column $column
     * @throws \Exception
     * @return string
     */
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

    /**
     * Creates full insert query and returns it as a string.
     * array with data needs to have ['key' => 'value'] structure where key is column name, and value is valid value to be inserted into this collumn.
     * @param array $data
     * @param string $tableName
     * @param string $dbname
     * @return string
     */
    public static function insert(array $data, string $tableName, string $dbname): string
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $query = "INSERT INTO `$dbname`.`$tableName` ($columns) VALUES ($placeholders);";
        return $query;
    }
    public static function update(array $data, string $tableName, string $dbName): string
    {
        $setClause = [];
        foreach ($data as $key => $value) {
            $setClause[] = "$key = :$key";
        }

        $setClause = implode(', ', $setClause);

        $query = "UPDATE `$dbName`.`$tableName` SET $setClause WHERE id = :id;";

        return $query;
    }
    public static function select(string $tableName, string $dbName, ?array $columns = null, ?array $conditions = null): string
    {
        $columnsStr = '*';
        if ($columns !== null) {
            $columnsStr = implode(', ', $columns);
        }
        $query = "SELECT $columnsStr FROM `$dbName`.`$tableName`";

        if ($conditions !== null && !empty($conditions)) {
            $conditionsStr = self::selectFilter($conditions);
            $query .= " WHERE $conditionsStr;";
        }

        return $query;
    }
    public static function delete(string $tableName, string $dbName, array $conditions): string
    {
        $conditionsStr = self::selectFilter($conditions);

        $query = "DELETE FROM `$dbName`.`$tableName` WHERE $conditionsStr;";

        return $query;
    }
    private static function selectFilter(array $conditions): string
    {
        $conditionStr = [];
        foreach ($conditions as $key => $value) {
            $conditionStr[] = "$key = :$key";
        }

        return implode(' AND ', $conditionStr);
    }
}