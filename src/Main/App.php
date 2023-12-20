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
        $router->get('/', function () {
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
        $executed = $router->check();
        if ($executed === false) {
            View::render('Error404');
        }
    }
}
