<?php
namespace App\Lib\Routing\Interface;

/**
 * @property array $instances
 */
interface RouterInterface
{
    /**
     * Return existing or new instance, multiple instances can be created and managed at the same time.
     * @param string $router name of the instance
     * @return \App\Lib\Routing\Router Router instance
     */
    public static function getInstance(string $router): RouterInterface;

    /**
     * Creates GET route
     * @param string $route 
     * @param mixed $callback 
     * @return void
     */
    public function get(string $route, $callback);
    /**
     * Creates POST route
     * @param string $route
     * @param mixed $callback
     * @return void
     */
    public function post(string $route, $callback);
}