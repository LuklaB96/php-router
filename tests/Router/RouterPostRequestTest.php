<?php

use PHPUnit\Framework\TestCase;
use App\Lib\Routing\Router;

final class RouterPostRequestTest extends TestCase
{
    //using this to check if route is valid successfully
    private $validRoute;
    private $router;
    protected function setUp(): void
    {
        $this->validRoute = false;
        $this->router = Router::getInstance('test router');
    }
    protected function tearDown(): void
    {
        $this->router->reset();
    }
    public function testValidPostRoute(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/test';

        $this->router->post('/test', function () {
            $this->validRoute = true;
        });
        $this->router->check();

        $this->assertTrue($this->validRoute);
    }
    public function testInvalidPostRouter(): void
    {
        //Assign
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/test';
        $this->validRoute = false;

        //Act
        $this->router->post('/test', function () {
            $this->validRoute = true;
        });
        $this->router->check();
        $this->router->reset();
        //Assert
        $this->assertFalse($this->validRoute);
    }
}