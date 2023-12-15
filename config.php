<?php
include 'config/asset_mapper.php';

return [
    //absolute dir paths
    'LOG_PATH' => __DIR__ . '/logs',
    'APP_PATH' => __DIR__ . '/src/Views',
    'MAIN_DIR' => __DIR__,
    //asset mapper
    'assets' => $assets,
    //database credentials
    'DB_USER' => 'dbuser',
    'DB_PASSWORD' => 'dbpassword',
    'DB_NAME' => 'dbname',
    'DB_HOST' => 'dbhost',
];