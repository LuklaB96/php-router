<?php

namespace App\Lib\Routing;

use App\Lib\Logger\Logger;
use App\Lib\Config;
use App\Lib\Logger\Types\FileLogger;
use App\Lib\Routing\Uri\UriParser;
use App\Lib\Routing\Validator\RouteValidator;

class Router
{

    //this will hold information if exactly one valid route was successfully used.
    private static $routeExecuted = false;
    private static $validRouteName = '';
    private static $lastRoute = '';
    private static $checked = false;
    public static function get($route, $callback)
    {
        self::$lastRoute = $_SERVER['REQUEST_URI'];
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'GET') !== 0) {
            return;
        }
        //if route with same name has been executed already, do nothing.
        if (self::$validRouteName == $route) {
            return throw new \Exception('Duplicated route: ' . $route);
        }

        self::on($route, $callback);
    }

    public static function post($route, $callback)
    {
        self::$lastRoute = $_SERVER['REQUEST_URI'];
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') !== 0) {
            return;
        }
        //if route with same name has been executed already, do nothing.
        if (self::$validRouteName == $route) {
            return throw new \Exception('Duplicated route: ' . $route);
        }
        self::on($route, $callback);
    }

    public static function on($route, $callback)
    {
        $validRoute = RouteValidator::validate($route);
        if ($validRoute) {
            self::$routeExecuted = true;
            //create params object from valid route and uri
            $params = UriParser::getRouteParams($route);

            //check if any parameters have been created and send them back to route handler
            if (!empty($params)) {
                $params = (object) $params;
                $callback(new Request($params), new Response());
            } else {
                $callback(new Request(), new Response());
            }
            self::$validRouteName = $route;
        }
    }
    /**
     * Reset router to default settings.
     * @return void
     */
    public static function reset()
    {
        self::clear();
        self::$checked = false;
    }
    /**
     * Check if any valid route was used, if not, this should display errors for end user.
     * Never call it more than once.
     * @return void
     */
    public static function check()
    {
        if (!self::$checked) {
            self::$checked = true;
        } else {
            return throw new \Exception("Route::check() function was called twice on the same route.");
        }
        if (!self::$routeExecuted) {
            //code for redirect / error display if page was not found
            echo 'Page not found';

            $logger = Logger::getInstance(new FileLogger());
            $logger->message('Trying to access invalid route: ' . self::$lastRoute);
        }
        self::clear();
    }
    /**
     * Clears all information about current and previous routes
     * @return void
     */
    public static function clear()
    {
        self::$routeExecuted = false;
        self::$lastRoute = '';
        self::$validRouteName = '';
    }
}