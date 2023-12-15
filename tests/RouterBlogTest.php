<?php

use PHPUnit\Framework\TestCase;
use App\Lib\Routing\Router;

final class RouterBlogTest extends TestCase
{
    //using this to check if route is valid successfully
    private $validRoute;
    public function testValidBlogRouterRequest(): void
    {
        //Assign
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/blog';
        $this->validRoute = false;

        //Act
        Router::get('/blog', function () {
            $this->validRoute = true;
        });
        Router::check();
        Router::reset();
        //Assert
        $this->assertTrue($this->validRoute);
    }
    public function testInvalidBlogRouterRequest(): void
    {
        //Assign
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/blog';
        $this->validRoute = false;

        //Act
        Router::get('/blog', function () {
            $this->validRoute = true;
        });
        Router::check();
        Router::reset();
        //Assert
        $this->assertFalse($this->validRoute);
    }
}