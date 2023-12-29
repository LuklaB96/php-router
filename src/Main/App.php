<?php
namespace App\Main;

use App\Controller\TestController;
use App\Entity\Person;
use App\Lib\Routing\Router;
use App\Lib\Routing\Response;
use App\Lib\View\View;

class App
{
    public static function run()
    {
        $startTime = microtime(true);
        //router instance, multiple instances are possible
        $router = Router::getInstance();

        //basic GET request with route and view
        $router->get("/", function () {
            View::render(
                'ExampleView', [
                    'helloWorld' => 'Hello World!',
                ]
            );
        });

        //basic Entity usage to get data from db
        $router->get('/person/{id}', function ($id) {
            $res = new Response();
            $person = new Person();
            $found = $person->find($id);
            if ($found) {
                $data = ['post' => [
                    'id' => $person->getId(),
                    'firstName' => $person->getFirstName(),
                    'lastName' => $person->getLastName(),
                ],
                    'status' => '200'
                ];
                $res->toJSON();
            }
        });
        //route with parameters
        $router->get('/person/{id}/{firstName}/{lastName}', function ($id, $firstName, $lastName) {
            $res = new Response();
            echo 'first param(id): ' . $id . ', second param(first name): ' . $firstName . ', third param(last name): ' . $lastName;
        });

        $router->get('/person/{id}', function ($id) {
            (new TestController())->getPersonById($id);
        });

        //will only work when properly sent POST request with payload
        $router->post('/test', function () {
            (new TestController())->csrfValidationExample();
        });
        $router->get('/attr', function () {
            $person = new Person();

            $person->setFirstName('test');
            $person->setLastName('test');
            $person->setLogin('testLogin');
            $valid = $person->validate();
            var_dump($valid);
        });



        //dispatch current route provided by user. 
        $executed = $router->dispatch();
        if ($executed === false) {
            View::render('Error404');
        }
        $endTime = microtime(true) - $startTime;
        $time = intval($endTime * ($p = pow(10, 3))) / $p;
        $routeCollection = $router->getRouteCollection();
        echo '</br>Route executed in: ' . $time . 's, avaible routes: ' . $routeCollection->countRoutes();
    }
}
