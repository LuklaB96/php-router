<?php

use PHPUnit\Framework\TestCase;
use App\Lib\Routing\Router;

final class RouterDispatchTest extends TestCase
{
    private $router;
    protected function setUp(): void
    {
        $this->router = Router::getInstance('test router');
    }
    protected function tearDown(): void
    {
        $this->router->clear();
    }
    public function testRouterValidDispatch()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/test';

        $this->router->post('/test', function () {
            //route with empty response
        });

        //above route is valid, should return true
        $valid = $this->router->dispatch();

        $this->assertTrue($valid);
    }
    public function testRouterInvalidDispatch()
    {
        $_SERVER['REQUEST_METHOD'] = '';
        $_SERVER['REQUEST_URI'] = '';
        //dispatch should return false when there are no routes matching requested uri
        $valid = $this->router->dispatch();

        $this->assertFalse($valid);
    }
}