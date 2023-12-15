<?php

namespace App\Lib\Routing;

use App\Lib\Logger\Logger;
use App\Lib\Config;

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
        $validRoute = Router::validateRoute($route);
        if ($validRoute) {
            //create params object from valid route and uri
            $params = Router::createParams($route);

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
     * Returns params object from all parameters passed to $_SERVER['REQUEST_URI'], check if route is valid before using.
     * @param array $paramKeys
     * @param array $paramValues
     * @param array $realParamPositions
     * @return array
     */
    private static function createParams(string $route): array
    {

        $uriParams = $_SERVER['REQUEST_URI'];

        //split all params from route and uri into arrays
        $paramValues = explode('/', $uriParams);
        $routeParams = explode('/', $route);


        //check if route has specified parameters
        preg_match_all('/{(.*?)}/', $route, $matches, PREG_PATTERN_ORDER);


        //get all keys from regex matches
        $paramKeys = [];
        foreach ($matches as $match) {
            $count = 0;
            foreach ($match as $key => $value) {
                $paramKeys[$count] = $value;
                $count++;
            }
        }


        //get position of all true parameters passed by user in array
        $paramPositions = [];
        $count = 0;
        foreach ($routeParams as $key => $value) {
            $matching = preg_match("/{.*?}/", $value);
            if ($matching) {
                $paramPositions[] = $count;
            }
            $count++;
        }

        //create params object with key => value structure, where key is a route parameter name inside curly brackets {} and value is taken directly from end user.
        //validateRoute function is checking if amount of parameters matching route requirements. 
        if (!empty($paramKeys) && !empty($paramPositions) && !empty($paramValues)) {
            $params = [];
            for ($i = 0; $i < count($paramKeys); $i++) {
                $params[$paramKeys[$i]] = $paramValues[$paramPositions[$i]];
            }
            return $params;
        }
        return [];
    }

    /**
     * Validate route, checks if $_SERVER['REQUEST_URI'] is matching any avaible routes.
     * @param string $route
     * @return bool
     */
    private static function validateRoute(string $route): bool
    {
        if ($route) {
            $params = $_SERVER['REQUEST_URI'];
            $routeParams = explode('/', $params);
            $routeParamsBase = explode('/', $route);

            if (count($routeParamsBase) != count($routeParams)) {
                return false;
            }

            $count = 0;
            foreach ($routeParamsBase as $key => $value) {
                $matching = preg_match("/{.*?}/", $value);
                if ($matching == 0) {
                    if ($routeParams[$count] != $routeParamsBase[$count]) {
                        return false;
                    }
                }
                $count++;
            }
            self::$routeExecuted = true;
            return true;
        }
        return false;
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

            $logger = Logger::getInstance();
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