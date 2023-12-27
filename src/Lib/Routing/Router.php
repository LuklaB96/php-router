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

use App\Lib\Routing\Interface\RouterInterface;
use App\Lib\Routing\Uri\RouteParser;

class Router implements RouterInterface
{

    /**
     * Router instances, default = 'main'
     * @var array
     */
    protected static $instances = [];
    /**
     * Collection with all avaible routes
     * @var RouteCollection
     */
    public RouteCollection $routeCollection;
    public function __construct()
    {
        $this->routeCollection = new RouteCollection();
    }
    /**
     * Get new or existing Router instance
     * @param string $router
     * @return \App\Lib\Routing\Interface\RouterInterface
     */
    public static function getInstance(string $router = 'main'): RouterInterface
    {
        if (empty(self::$instances[$router])) {
            return self::$instances[$router] = new Router();
        }

        return self::$instances[$router];
    }
    public function get($route, $callback)
    {
        $static = $this->isRouteStatic($route);
        $route = new Route('GET', $route, $callback, $static);
        $this->routeCollection->add($route);
    }
    public function post(string $route, $callback)
    {
        $route = new Route('POST', $route, $callback, false);
        $this->routeCollection->add($route);
    }
    public function dispatch(): bool
    {
        $requestMethod = trim($_SERVER['REQUEST_METHOD']);
        $route = trim($_SERVER['REQUEST_URI']);
        $routeObject = $this->routeCollection->find($requestMethod, $route);
        if (!empty($routeObject)) {
            $params = RouteParser::getRouteParams($routeObject->getRoute());
            $callback = $routeObject->getHandler();
            if (!empty($params)) {
                $callback(...$params);
                return true;
            } else {
                $callback();
                return true;
            }
        }
        return false;
    }
    /**
     * Checking if route has a custom {var} pattern, otherwise mark as static.
     * @param string $route
     * @return bool
     */
    public function isRouteStatic(string $route): bool
    {
        $matching = preg_match("/{.*?}/", $route);
        if ($matching == 0) {
            return true;
        }
        return false;
    }
    public function getRouteCollection(): RouteCollection
    {
        return $this->routeCollection;
    }
    public function clear(): void
    {
        $this->routeCollection = new RouteCollection();
    }
}
