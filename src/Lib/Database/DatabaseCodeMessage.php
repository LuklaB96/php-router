<?php
namespace App\Lib\Database;


/**
 * To do, maybe will be used in future.
 */
class DatabaseCodeMessage
{
    public static function get($code): string
    {
        if ($code == 1050) {
            return 'Table already exists';
        }
        return 'Unhandled code';
    }
}
