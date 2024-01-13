<?php
include 'asset_config/asset_mapper.php';

/**
 * This is main config file, it should be properly configured, otherwise server will work in an unexpected way
 */

return [
    /**
     * PHP_SERVER - Use this if default php server is used, need to start it with valid path to index.php as router e.g. 'php -S localhost:8000 public/index.php'
     * 
     * APACHE - If apache is main server software, document root must be set to /public directory
     * 
     * AUTO - It will try to automatically check for server software that has been used
     * 
     * NGINX - Not yet supported
     */
    'SERVER_SOFTWARE' => 'AUTO', //PHP_SERVER,APACHE,AUTO
    //absolute dir paths
    'LOG_PATH' => __DIR__ . '/logs',
    'APP_PATH' => __DIR__ . '/src',
    'MAIN_DIR' => __DIR__,
    //asset mapper
    'ASSETS' => $assets,
    //database credentials
    'DB_USER' => getenv('MYSQL_USER'),
    'DB_PASSWORD' => getenv('MYSQL_PASSWORD'),
    'DB_NAME' => getenv('MYSQL_DATABASE'),
    'DB_HOST' => getenv('MYSQL_HOST'),
    //CSRF Token lifetime, default = 60 minutes
    'CSRF_TOKEN_LIFETIME' => 60 * 60,
    //error and exception routes for the end user
    'EXCEPTION_ROUTE' => '/error',
    'ERROR_ROUTE' => '/error',

    // email sender config
    // same as email configured in php.ini sendemail_from
    'EMAIL_SENDER' => 'php.lukasz.bulicz@gmail.com',
];