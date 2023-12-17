<?php
namespace App\Lib\Logger\Utility;

use App\Lib\Logger\LoggerConfig;

/**
 * Helper class for creating and removing log directories.
 */
class LoggerDirectory
{
    /**
     * Create logs directory (default or custom) if does not exist
     * @param \App\Lib\Logger\LoggerConfig $config
     * @return bool
     */
    public static function create(LoggerConfig $config): bool
    {
        if (!file_exists($config->getLogDir() . '/' . $config->getName())) {
            // create directory if does not exists
            mkdir($config->getLogDir() . '/' . $config->getName(), 0777, true);
            return true;
        }
        return false;
    }
    /**
     * Trying to delete directory, not working on default directory.
     * @param \App\Lib\Logger\LoggerConfig $config
     * @return bool
     */
    public static function delete(LoggerConfig $config): bool
    {
        if (file_exists($config->getLogDir() . '/' . $config->getName()) && !$config->isDefault()) {
            $dir = $config->getLogDir();
            $it = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new \RecursiveIteratorIterator($it,
                \RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($files as $file) {
                if ($file->isDir()) {
                    rmdir($file->getPathname());
                } else {
                    unlink($file->getPathname());
                }
            }
            rmdir($dir);
            return true;
        }
        return false;
    }
}
