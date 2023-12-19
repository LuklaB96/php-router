<?php

use App\Lib\Routing\Exception\RouterCheckException;
use PHPUnit\Framework\TestCase;
use App\Lib\Routing\Router;

final class RouterCheckTest extends TestCase
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
    public function testRouterValidCheck()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/test';

        $this->router->post('/test', function () {
            //route with empty response
        });

        //valid route executed above, should return true
        $valid = $this->router->check();

        $this->assertTrue($valid);
    }
    public function testRouterInvalidCheck()
    {
        //if router did not executed any route, check should return false
        $valid = $this->router->check();

        $this->assertFalse($valid);
    }
    public function testRouterCheckException()
    {
        $this->expectException(RouterCheckException::class);
        //calling check twice without using $this->router->reset is not a valid operation
        $this->router->check();
        $this->router->check();
    }
    public function testResetRouter_InvalidPostRoute()
    {
        //should not throw any exceptions when router is cleared before second check call
        $this->router->check();
        $this->router->reset();

        //execute invalid route
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/test';

        $this->router->post('/test', function () {
            //route with empty response
        });
        //should return false
        $validCheck = $this->router->check();

        $this->assertFalse($validCheck);
    }
    public function testResetRouter_ValidPostRoute()
    {
        //should not throw any exceptions when router is cleared before second check call
        $this->router->check();
        $this->router->reset();

        //execute valid route
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/test';

        $this->router->post('/test', function () {
            //route with empty response
        });

        //should return true
        $validCheck = $this->router->check();

        $this->assertTrue($validCheck);
    }
    public function testResetRouter_InvalidGetRoute()
    {
        //should not throw any exceptions when router is cleared before second check call
        $this->router->check();
        $this->router->reset();

        //execute invalid route
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/test';

        $this->router->get('/test', function () {
            //route with empty response
        });
        //should return false
        $validCheck = $this->router->check();

        $this->assertFalse($validCheck);
    }
    public function testResetRouter_ValidGetRoute()
    {
        //should not throw any exceptions when router is cleared before second check call
        $this->router->check();
        $this->router->reset();

        //execute valid route
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/test';

        $this->router->get('/test', function () {
            //route with empty response
        });

        //should return true
        $validCheck = $this->router->check();

        $this->assertTrue($validCheck);
    }
}