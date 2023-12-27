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

        $router->get("/", function () {
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
            '/pers/{id}', function ($id) {
                echo 'id: ' . $id;
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
            '/phpinfo', function () {
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
            '/post/{id}', function ($id) {
                $res = new Response();
                $res->toJSON(
                    [
                        'post' => [
                            'id' => $id,
                        ],
                        'status' => 'ok'
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

        $router->get(
            '/person/{id}', function ($id) {
                $person = new Person();
                $res = new Response();
                $person->find($id);
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

        //will only work when properly sent POST request
        $router->post(
            '/test', function () {
                $req = new Request();
                $res = new Response();


                $result = $req->getData();
                $person = new Person();

                $person->setImie($result['imie']);
                $person->setNazwisko($result['nazwisko']);
                $message = $person->insert();
                $res->toJSON($message);
            }
        );



        //dispatch current route provided by user. 
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
