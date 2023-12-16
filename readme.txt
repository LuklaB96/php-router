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


## DATABASE INFO ##

First go to config.php in main directory and set up your database.
Run command:
php src/Database/database_create.php

Now you can create your first entity that will represent table in database.
Create new file in src/Entity/ e.g. Person.php, create class that extends Entity class.
Remember if you don't set a $name variable in your entity class, it will be set as class name, in this case 'person' will be our table name.

If your entity class is properly configured, go to /src/Database/migrations.php and create all entity objects you want to be represented as tables:

$person = new Person(); //your entity class
$migrations->create($person); //creating table in database from entity properties and name information

Run command:
php src/Database/migrations.php

Check database if all is ok.

## END OF DATABASE INFO ##

