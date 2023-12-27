<?php
namespace App\Lib\Routing;

use App\Lib\Routing\Validator\RouteValidator;


class RouteCollection
{
    /**
     * Collection of Route objects
     * @var array
     */
    protected $staticGetRoutes = [];
    protected $dynamicGetRoutes = [];
    protected $dynamicPostRoutes = [];
    public function add(Route $route)
    {
        if ($route->getMethod() === "GET") {
            $static = $route->isStatic();
            if ($static) {
                $this->staticGetRoutes[$route->getRoute()] = $route;
            } else {
                $this->dynamicGetRoutes[] = $route;
            }
        }
        if ($route->getMethod() === "POST") {
            $this->dynamicPostRoutes[] = $route;
        }
    }
    public function find(string $requestMethod, string $route): ?Route
    {
        if ($requestMethod === 'GET') {
            $staticRouteObject = $this->staticGetRoutes[$route] ?? null;
            if (!empty($staticRouteObject)) {
                return $staticRouteObject;
            }
            $dynamicRouteObject = $this->getRouteFromCollection($this->dynamicGetRoutes);
            return $dynamicRouteObject;
        } elseif ($requestMethod === 'POST') {
            $dynamicRouteObject = $this->getRouteFromCollection($this->dynamicPostRoutes);
            return $dynamicRouteObject;
        }
        return null;
    }
    /**
     * Check if route exists in a given collection 
     * @param Route[] $routes
     * @return mixed
     */
    private function getRouteFromCollection(array $routes): ?Route
    {
        foreach ($routes as $dynamicRouteObject) {
            $valid = RouteValidator::validate($dynamicRouteObject->getRoute());
            if ($valid) {
                return $dynamicRouteObject;
            }
        }
        return null;
    }
    public function countRoutes(): int
    {
        $sget = count($this->staticGetRoutes);
        $dget = count($this->dynamicGetRoutes);
        $dpost = count($this->dynamicPostRoutes);
        return $sget + $dget + $dpost;
    }
}