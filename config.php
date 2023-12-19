<?php
include 'config/asset_mapper.php';

/**
 * This is main config file, it should be properly configured, otherwise exceptions can be thrown.
 */

return [
    //absolute dir paths
    'LOG_PATH' => __DIR__ . '/logs',
    'APP_PATH' => __DIR__ . '/src',
    'MAIN_DIR' => __DIR__,
    //asset mapper
    'assets' => $assets,
    //TO DO: use path specified here as public asset directory, need to be implemented in AssetMapper::isPublicAsset() function.
    'PUBLIC_ASSET_DIR' => '/public/assets',
    //database credentials
    'DB_USER' => 'root',
    'DB_PASSWORD' => '',
    'DB_NAME' => 'main_db',
    'TEST_DB_NAME' => 'test_db',
    'DB_HOST' => '127.0.0.1',
    //if true, all migrations will also be created in testing database
    'TEST_DB_ACTIVE' => true,
];