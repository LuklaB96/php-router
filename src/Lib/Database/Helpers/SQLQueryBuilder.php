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
     * @param array $columns array with valid \App\Lib\Database\Mapping\Column objects
     * @param string $tableName
     * @param string $dbname
     * @param bool $checkExists if true, query will have 'CREATE TABLE IF NOT EXISTS' clause, otherwise 'CREATE TABLE dbname.tablename...'
     * @return string full query ready to be executed
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
     * @param \App\Lib\Database\Mapping\Column $column object with valid attributes
     * @throws \Exception when @param \App\Lib\Database\Mapping\Column attributes are invalid e.g. 'primary key auto_increment null'
     * @return string string ready to be concatenated with other parts of the SQL query
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
     * @param array $data syntax: ['column_name' => 'value']
     * @param string $tableName
     * @param string $dbname
     * @return string full query ready to be executed
     */
    public static function insert(array $data, string $tableName, string $dbname): string
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $query = "INSERT INTO `$dbname`.`$tableName` ($columns) VALUES ($placeholders);";
        return $query;
    }
    /**
     * @param array $data syntax: ['column_name' => 'value']
     * @param string $tableName
     * @param string $dbName
     * @return string full query ready to be executed
     */
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
    /**
     * 
     * @param string $tableName
     * @param string $dbName 
     * @param string $orderBy syntax: column_name ASC|DESC
     * @param array $columns if empty, return all (*) records.
     * @param array $conditions syntax: ['column_name' => 'value']
     * @param int $limit no limit if null
     * @return string full query ready to be executed
     */
    public static function select(string $tableName, string $dbName, string $orderBy = null, array $columns = null, array $conditions = null, int $limit = null): string
    {
        $columnsStr = '*';
        if ($columns !== null) {
            $columnsStr = implode(', ', $columns);
        }
        $query = "SELECT $columnsStr FROM `$dbName`.`$tableName`";

        if ($conditions !== null && !empty($conditions)) {
            $conditionsStr = self::selectFilter($conditions);
            $query .= " WHERE $conditionsStr";
        }

        if ($orderBy !== null) {
            $query .= " ORDER BY $orderBy";
        }

        if ($limit !== null) {
            $query .= " LIMIT $limit";
        }

        $query .= ";";

        return $query;
    }
    /**
     * @param string $tableName
     * @param string $dbName
     * @param array $conditions syntax: ['column_name' => 'value']
     * @return string full query ready to be executed
     */
    public static function delete(string $tableName, string $dbName, array $conditions): string
    {
        $conditionsStr = self::selectFilter($conditions);

        $query = "DELETE FROM `$dbName`.`$tableName` WHERE $conditionsStr;";

        return $query;
    }
    /**
     * Creates a parametrized SQL filter e.g. column_name = :column_name for sql injection security 
     * @param array $conditions syntax: ['column_name' => 'value'], only column_name will be used
     * @return string string ready to be concatenated with other parts of the SQL query
     */
    private static function selectFilter(array $conditions): string
    {
        $conditionStr = [];
        foreach ($conditions as $key => $value) {
            $conditionStr[] = "$key = :$key";
        }

        return implode(' AND ', $conditionStr);
    }
}