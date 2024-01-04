<?php
include 'config/asset_mapper.php';

/**
 * This is main config file, it should be properly configured, otherwise server will work in an unexpected way
 */

return [
    /**
     * PHP_SERVER - Use this if default php server is used, need to start it with valid path to index.php as router e.g. 'php -S localhost:8000 public/index.php'
     * 
     * APACHE - If xampp with apache is main server software, document root must be set to /public directory
     * 
     * AUTO - It will try to automatically check for server software that has been used.
     */
    'SERVER_SOFTWARE' => 'AUTO', //PHP_SERVER,APACHE,AUTO
    //absolute dir paths
    'LOG_PATH' => __DIR__ . '/logs',
    'APP_PATH' => __DIR__ . '/src',
    'MAIN_DIR' => __DIR__,
    //asset mapper
    'ASSETS' => $assets,
    //database credentials
    'DB_USER' => 'root',
    'DB_PASSWORD' => '',
    'DB_NAME' => 'app_db',
    'TEST_DB_NAME' => 'app_db_test',
    'DB_HOST' => '127.0.0.1',
    //if true, all migrations will also be created in testing database
    'TEST_DB_ACTIVE' => true,
    //CSRF Token lifetime, default = 60 minutes
    'CSRF_TOKEN_LIFETIME' => 60 * 60,
    //error and exception routes for the end user
    'EXCEPTION_ROUTE' => '/error',
    'ERROR_ROUTE' => '/error',
];