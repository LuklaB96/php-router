<?php
namespace App\Main;

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
        //router instance, multiple instances are possible.
        $router = Router::getInstance('default');
        //every route is unique, if we make two identical endpoints, only first one will be executed.

        //example routes
        //This is very slow approach to match the correct route, because every get and post request made below will check if it is a valid route
        //Better approach is to make an $router->addRoute('route','request_type','handler') function, route validator should check if valid route exist, execute its handler and return correct response. 
        //Example situation: when it is the first route in the list, it is an optimistic situation, when it is the last - pessimistic. 
        //We want the router to perform no further checking or even any interaction with subsequent cases if the route has been found and executed properly.
        $startTime = microtime(true);
        $router->get(
            '/', function () {
                $person1 = new Person();
                $person2 = new Person();
                $personRepository = $person1->findAll();
                foreach ($personRepository as $p) {
                    echo $p->getImie() . '</br>';
                }

                View::render(
                    'ExampleView', [
                        'helloWorld' => 'Hello World!',
                    ]
                );
            }
        );

        //problem with those two is that both are valid at the same time, our validator cant tell the difference between them. 
        //When we call /pers/2 it will return only this one, but when we call /pers/all we are getting both responses which is BAD
        $router->get(
            '/pers/all', function () {
                echo 'first route should handle and return all data';
            }
        );
        $router->get(
            '/pers/{id}', function () {
                echo 'second route should handle and return data searched by id';
            }
        );



        $router->get(
            '/phpinfo', function (Request $req) {
                echo apache_get_version();
            }
        );

        $router->get(
            '/blog', function () {
                $data = [
                    'helloWorld' => 'Hello World!',
                ];
                View::render('ExampleView', $data);
            }
        );

        $router->get(
            '/post/{id}', function (Request $req, Response $res) {
                $res->toJSON(
                    [
                        'post' => [
                            'id' => $req->params->id,
                        ],
                        'status' => 'ok'
                    ]
                );
            }
        );
        $router->get(
            '/person/{id}/{firstName}/{lastName}', function (Request $req, Response $res) {
                $res->toJSON(
                    [
                        'post' => [
                            'id' => $req->params->id,
                            'imie' => $req->params->firstName,
                            'nazwisko' => $req->params['lastName'],
                        ],
                        'status' => 'ok'
                    ]
                );
            }
        );

        $router->get(
            '/person/{id}', function (Request $req, Response $res) {
                $person = new Person();
                $person->find($req->params->id);
                if ($person->getId() == null) {
                    $res->toJSON(
                        [
                            'status' => 'not found'
                        ]
                    );
                } else {
                    $res->toJSON(
                        [
                            'person' => [
                                'id' => $person->getId(),
                                'imie' => $person->getImie(),
                                'nazwisko' => $person->getNazwisko()
                            ],
                            'status' => 'ok'
                        ]
                    );
                }
            }
        );

        //will only work when properly sent POST request.
        $router->post(
            '/test', function (Request $req, Response $res) {
                $result = $req->getData();
                $person = new Person();

                $person->setImie($result['imie']);
                $person->setNazwisko($result['nazwisko']);
                $message = $person->insert();
                $res->toJSON($message);
            }
        );

        $executionTime = microtime(true) - $startTime;
        print_r($executionTime);

        //check if any route has been set as valid, display error like 'page not found' or render specific view for this type of event.
        $executed = $router->check();
        if ($executed === false) {
            View::render('Error404');
        }
    }
}
