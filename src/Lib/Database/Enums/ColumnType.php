<?php
namespace App\Lib\Database\Enums;

enum ColumnType: string
{
    case DECIMAL = 'decimal';
    case INT = 'int';
    case TINYINT = 'tinyint';
    case SMALLINT = 'smallint';
    case MEDIUMINT = 'mediumint';
    case BIGINT = 'bigint';
    case FLOAT = 'float';
    case DOUBLE = 'double';
    case DATE = 'date';
    case DATETIME = 'datetime';
    case TIMESTAMP = 'timestamp';
    case TIME = 'time';
    case YEAR = 'year';
    case CHAR = 'char';
    case VARCHAR = 'varchar';
    case BINARY = 'binary';
    case VARBINARY = 'varbinary';
    case TINYBLOB = 'tinyblob';
    case BLOB = 'blob';
    case MEDIUMBLOB = 'mediumblob';
    case LONGBLOB = 'longblob';
    case TINYTEXT = 'tinytext';
    case TEXT = 'text';
    case MEDIUMTEXT = 'mediumtext';
    case LONGTEXT = 'longtext';
    case ENUM = 'enum';
    case SET = 'set';
    //Add more if needed
}
