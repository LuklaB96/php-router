<?php
namespace App\Lib\Database\Interface;

/**
 * 
 */
interface QueryBuilderInterface
{
    /**
     * Returns a create table query string
     *
     * @param  array  $columns     array with valid \App\Lib\Database\Mapping\Column objects
     * @param  string $tableName
     * @param  string $dbname
     * @param  bool   $checkExists if true, query will have 'CREATE TABLE IF NOT EXISTS' clause, otherwise 'CREATE TABLE dbname.tablename...'
     * @return string full query ready to be executed
     */
    public static function createTable(array $columns, string $tableName, string $dbname, bool $checkExists = false): string;
    /**
     * Creates a column definition from avaible data in Column object
     *
     * @param  \App\Lib\Database\Mapping\Column $column object with valid attributes
     * @throws \Exception when @param \App\Lib\Database\Mapping\Column attributes are invalid e.g. 'primary key auto_increment null'
     * @return string string ready to be concatenated with other parts of the SQL query
     */
    private static function createColumnDefinition(Column $column): string;
    /**
     * @param  array  $data      syntax: ['column_name' => 'value']
     * @param  string $tableName
     * @param  string $dbname
     * @return string full query ready to be executed
     */
    public static function insert(array $data, string $tableName, string $dbname): string;
    /**
     * @param  array  $data      syntax: ['column_name' => 'value']
     * @param  string $tableName
     * @param  string $dbName
     * @return string full query ready to be executed
     */
    public static function update(array $data, string $tableName, string $dbName): string;
    /**
     * 
     * @param  string $tableName
     * @param  string $dbName 
     * @param  string $orderBy    syntax: column_name ASC|DESC
     * @param  array  $columns    if empty, return all (*) records.
     * @param  array  $conditions syntax: ['column_name' => 'value']
     * @param  int    $limit      no limit if null
     * @return string full query ready to be executed
     */
    public static function select(string $tableName, string $dbName, string $orderBy = null, array $columns = null, array $conditions = null, int $limit = null): string;
    /**
     * @param  string $tableName
     * @param  string $dbName
     * @param  array  $conditions syntax: ['column_name' => 'value']
     * @return string full query ready to be executed
     */
    public static function delete(string $tableName, string $dbName, array $conditions): string;
}
