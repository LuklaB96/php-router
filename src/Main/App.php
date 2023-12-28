<?php
namespace App\Main;

use App\Controller\PersonController;
use App\Lib\Database\Entity\EntityValidator;
use App\Entity\Person;
use App\Lib\Assets\AssetMapper;
use App\Lib\Routing\Router;
use App\Lib\Routing\Response;
use App\Lib\Routing\Request;
use App\Lib\View\View;

class App
{
    public static function run()
    {
        $startTime = microtime(true);
        //router instance, multiple instances are possible.
        $router = Router::getInstance();

        $router->get("/", function () {
            $person1 = new Person();
            $personRepository = $person1->findAll();
            foreach ($personRepository as $p) {
                echo $p->getFirstName() . '</br>';
            }

            View::render(
                'ExampleView', [
                    'helloWorld' => 'Hello World!',
                ]
            );
        });
        $router->get('/post/{id}', function ($id) {
            $res = new Response();
            $res->toJSON(
                [
                    'post' => [
                        'id' => $id,
                    ],
                    'status' => '200'
                ]
            );
        }
        );
        $router->get(
            '/person/{id}/{firstName}/{lastName}', function ($id, $firstName, $lastName) {
                $res = new Response();
                $res->toJSON(
                    [
                        'post' => [
                            'id' => $id,
                            'imie' => $firstName,
                            'nazwisko' => $lastName,
                        ],
                        'status' => 'ok'
                    ]
                );
            }
        );

        $router->get('/person/{id}', function ($id) {
            (new PersonController())->getPersonById($id);
        });

        //will only work when properly sent POST request with payload
        $router->post('/test', function () {
            $req = new Request();
            $res = new Response();
            $person = new Person();
            $result = $req->getData();
            $person->setFirstName($result['imie']);
            $person->setLastName($result['nazwisko']);
            $message = $person->insert();
            $res->toJSON($message);
            $result = $req->getData();
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
