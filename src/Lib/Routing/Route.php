<?php
/**
 * 1. operation: addRoute -> determine if it is static or dynamic and save it.
 * 2. operation: check what type of route has been requested to execute, static route have priority over dynamic one, find it and move to the next operation
 *   a) operation(static route): invoke handler -> execute
 *   b) operation(dynamic route): validate parameters -> invoke handler with parameters -> execute
 * 3. Routes initalization makes no real performance impact for even 100K array members (took between 8-14 ms) to be pushed in one by one, so no need to cache/serialize this data.
 * check how long it takes to validate and execute route when there are defined 1/100/1000 routes, for now performance need to be good enough for small apps.
 * 
 */
namespace App\Lib\Routing;


class Route
{
    /**
     * 
     * @var string
     */
    protected string $route;
    /**
     * 
     * @var object
     */
    protected object $handler;
    /**
     * 
     * @var bool
     */
    protected bool $static;
    /**
     * 
     * @var string
     */
    protected string $routeError;
    /**
     * 
     * @var string
     */
    protected string $method;
    /**
     * 
     * @param string $route
     * @param object $handler
     * @param bool $static
     */

    public function __construct(string $method, string $route, object $handler, bool $static)
    {
        $this->method = $method;
        $this->route = $route;
        $this->handler = $handler;
        $this->static = $static;
    }
    /**
     * 
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }
    /**
     * 
     * @return object
     */
    public function getHandler(): object
    {
        return $this->handler;
    }
    /**
     * 
     * @return string
     */
    public function getRouteError(): ?string
    {
        return $this->routeError;
    }
    public function isStatic(): bool
    {
        return $this->static;
    }
    public function getMethod(): string
    {
        return $this->method;
    }



}
