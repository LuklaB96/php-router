<?php
namespace App\Lib\Routing\Interface;

use App\Lib\Routing\RouteCollection;

/**
 * @property array $instances
 */
interface RouterInterface
{
    /**
     * Add new GET route
     *
     * @param  string $route 
     * @param  mixed  $callback 
     * @return void
     */
    public function get(string $route, $callback);
    /**
     * Add new POST route
     *
     * @param  string $route
     * @param  mixed  $callback
     * @return void
     */
    public function post(string $route, $callback);
    /**
     * Dispatch, validate and execute current route.
     * @param string $requestMethod
     * @param string $route
     * @return void
     */
    public function dispatch(): bool;
    /**
     * Get collection of all avaible routes
     * @return \App\Lib\Routing\RouteCollection
     */
    public function getRouteCollection(): RouteCollection;
    /**
     * Set all Router properties to default/empty state
     * @return void
     */
    public function clear();
}
