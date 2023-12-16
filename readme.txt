## PROJECT CONFIGURATION ##

Run command to install all required packages:
composer install

## END OF PROJECT CONFIGURATION ##


## PHP SERVER CONFIGURATION INFO ##
if using php server, use this command, it will point to index.php as your router.

php -S localhost:8000 public/index.php

## END OF PHP SERVER CONFIGURATION INFO ##


## APACHE CONFIGURATION INFO ##

You need to set /public folder as root directory
If using apache, .htaccess file in /public folder has basic configuration, you can edit this file to meet your requirements.

## END OF APACHE CONFIGURATION INFO ##


## GENERATING ASSETS INFO ##

All public assets can be accessed directly in /public/assets/

Use AssetMapper::isPublicFile() to check if file is accessible outside /public/assets/ 
It can be configured in /config/asset_mapper.php 
Later you can just use path in your views like /public/assets/styles/app.css or use function that will get path from asset_mapper.php eg. asset('app.css');
Check /src/Views/ExampleView.php if you need more information.

## END OF GENERATING ASSETS INFO ##

