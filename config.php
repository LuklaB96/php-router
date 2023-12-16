<?php
include 'config/asset_mapper.php';

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
    'DB_PASSWORD' => 'testdbpass1',
    'DB_NAME' => 'dbtest',
    'DB_HOST' => '127.0.0.1',
];