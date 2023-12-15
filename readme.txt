## PHP SERVER CONFIGURATION INFO ##
if you are using php server, use this command, it will point to index.php as your router.

php -S localhost:8000 public/index.php

## END OF PHP SERVER CONFIGURATION INFO ##



## APACHE CONFIGURATION INFO ##

You need to set /public folder as root directory
If we are using apache, .htaccess file in /public folder has basic configuration, you can edit this file to meet your requirements.

## END OF APACHE CONFIGURATION INFO ##

## GENERATING ASSETS INFO ##

All assets are avaible in /public/assets/ without restrictions.
If asset is correctly mapped, it can be accessed in browser: host.example/public/assets/styles/app.css 
Later you can just use path in your views like /public/assets/styles/app.css or use function that will get path from asset name asset('app.css');
Check /src/Views/ExampleView.php if you need more information.

## END OF GENERATING ASSETS INFO ##