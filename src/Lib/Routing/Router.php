<?php
/**
 * Create redirect function like redirect('routeName');
 * 
 * Problem: we have two routes, one is using id as var /person/{id}, other one is to return all: /person/all
 * Our validator cant find the difference between /person/1 and /person/all, so it will execute first that has been found instead of matching one.
 * 
 * Solution: Static routes should be separated from parametrized ones.
 * We have two groups: group 1 should contain all routes that are not parametrized e.g. /person/all, second group should have remaining ones with parameters /person/{id}
 * But how to tell which one was called?
 * 
 */
namespace App\Lib\Routing;

use App\Lib\Logger\Logger;
use App\Lib\Logger\Types\FileLogger;
use App\Lib\Routing\Exception\RouterCheckException;
use App\Lib\Routing\Interface\RouterInterface;
use App\Lib\Routing\Uri\RouteParser;
use App\Lib\Routing\Validator\RouteValidator;

class Router implements RouterInterface
{

    private static $instances = [];
    /**
     * This will hold information if exactly one valid route was successfully executed
     *
     * @var 
     */
    private $routeExecuted = false;
    /**
     * structure: ['route' => ['handler' => ['varName1','varName2',...]]]
     * e.g. [/person/{id} => ['personHandler' => ['id']]]
     * 
     * @var array
     */
    private $routes = [];
    /**
     * Route name that is valid and is executed properly
     *
     * @var string
     */
    private $validRouteName = '';
    /**
     * Last route that router tried to execute, can be valid/invalid
     *
     * @var string
     */
    private $lastRoute = '';
    /**
     * true if check() function has been used, otherwise false
     *
     * @var 
     */
    private $checked = false;
    public static function getInstance(string $router): RouterInterface
    {
        if (empty(self::$instances[$router])) {
            return self::$instances[$router] = new Router();
        }

        return self::$instances[$router];
    }
    public function get($route, $callback)
    {
        $this->lastRoute = $_SERVER['REQUEST_URI'];
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'GET') !== 0) {
            return;
        }
        //if route with same name has been executed already, do nothing.
        if ($this->validRouteName == $route) {
            return throw new \Exception('Duplicated route: ' . $route);
        }

        $this->on($route, $callback);
    }
    public function post($route, $callback)
    {
        $this->lastRoute = $_SERVER['REQUEST_URI'];
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') !== 0) {
            return;
        }
        //if route with same name has been executed already, do nothing.
        if ($this->validRouteName == $route) {
            return throw new \Exception('Duplicated route: ' . $route);
        }
        $this->on($route, $callback);
    }

    private function on($route, $callback)
    {
        $validRoute = RouteValidator::validate($route);
        if ($validRoute) {
            $this->routeExecuted = true;
            //create params object from valid route and uri
            $params = RouteParser::getRouteParams($route);

            //check if any parameters have been created and send them back to route handler
            if (!empty($params)) {
                $params = (object) $params;
                $callback(new Request($params), new Response());
            } else {
                $callback(new Request(), new Response());
            }
            $this->validRouteName = $route;
        }
    }
    /**
     * Reset router properties to default values, check function can be called again.
     *
     * @return void
     */
    public function reset()
    {
        $this->clear();
        $this->checked = false;
    }
    /**
     * @return bool true if route was executed correctly, otherwise false
     */
    public function check(): bool
    {
        if (!$this->checked) {
            $this->checked = true;
        } else {
            return throw new RouterCheckException(message: "Route::check() function was called twice on the same route.");
        }
        if (!$this->routeExecuted) {
            $logger = Logger::getInstance(new FileLogger());
            $logger->log('Trying to access invalid route: ' . $this->lastRoute);
            $this->clear();
            return false;
        }
        $this->clear();
        return true;
    }
    /**
     * Clears all information about current and previous routes
     *
     * @return void
     */
    public function clear()
    {
        $this->routeExecuted = false;
        $this->lastRoute = '';
        $this->validRouteName = '';
    }
}
