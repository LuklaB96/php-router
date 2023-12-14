<?php
namespace App\Main;

use App\Lib\Logger\Logger;
use App\Lib\Routing\Router;

class App
{
    public static function run()
    {
        //Run any necessary services here

        //check if any route has been set as valid, display error if not.
        Router::check();
    }
}