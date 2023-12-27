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
        $startTime = microtime(true);
        //router instance, multiple instances are possible.
        $router = Router::getInstance();

        $router->get("/", function (Request $request, Response $response) {
            $person1 = new Person();
            $personRepository = $person1->findAll();
            foreach ($personRepository as $p) {
                echo $p->getImie() . '</br>';
            }

            View::render(
                'ExampleView', [
                    'helloWorld' => 'Hello World!',
                ]
            );
        });
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
            '/arrayinit', function () {
                $startTime = microtime(true);
                $arr = [];
                for ($i = 0; $i < 100000; $i++) {
                    //$arr[] = '/abs/{' . $i . '}'; //array with auto increment key | ~6 ms
                    $arr['key_' . $i] = '/abs/{' . $i . '}'; //array with custom key | ~14 ms
                    //array_push($arr, '/abs/{' . $i . '}'); //function call | ~12 ms
    
                }
                $executionTime = microtime(true) - $startTime;
                print_r($executionTime);
                print_r(count($arr));
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



        //check if any route has been set as valid, display error like 'page not found' or render specific view for this type of event.
        $executed = $router->dispatch();
        if ($executed === false) {
            View::render('Error404');
        }
        $endTime = microtime(true) - $startTime;
        $time = intval($endTime * ($p = pow(10, 3))) / $p;
        $routeCollection = $router->getRouteCollection();
        echo '</br>Route executed in: ' . $time . 's, amount of routes: ' . $routeCollection->countRoutes();
    }
}
