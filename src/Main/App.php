<?php
namespace App\Main;

use App\Controller\LoginController;
use App\Controller\LogoutController;
use App\Controller\RegisterController;
use App\Controller\TestController;
use App\Entity\Comment;
use App\Entity\ExampleEntity;
use App\Entity\Person;
use App\Entity\Post;
use App\Entity\User;
use App\Lib\Database\Mapping\AttributeReader;
use App\Lib\Routing\Router;
use App\Lib\Routing\Response;
use App\Lib\View\View;

class App
{
    public static function run()
    {
        //router instance, multiple instances are possible
        $router = Router::getInstance();
        $router->get('/register', function () {
            (new RegisterController())->registerGET();
        });
        $router->post('/register', function () {
            (new RegisterController())->registerPOST();
        });
        $router->get("/login", function () {
            (new LoginController())->loginGET();
        });
        $router->post("/login", function () {
            (new LoginController())->loginPOST();
        });
        $router->post('/logout', function () {
            (new LogoutController())->logout();
        });

        //basic GET request route thats renders view as a response.
        $router->get("/", function () {
            View::render(
                'ExampleView',
                [
                    'helloWorld' => 'Hello World!',
                ]
            );
        });



        //dispatch current route provided by user. 
        $executed = $router->dispatch();
        if ($executed === false) {
            // View::render('Error404');
        }
    }
}
