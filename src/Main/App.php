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
        //router instance, multiple instances are possible
        $router = Router::getInstance();

        //basic GET request route thats renders view as a response.
        $router->get("/", function () {
            View::render(
                'ExampleView',
                [
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
                $res->status(200);
                $data = [
                    'post' => [
                        'id' => $person->getId(),
                        'firstName' => $person->getFirstName(),
                        'lastName' => $person->getLastName(),
                    ],
                    'status' => '200'
                ];
                $res->toJSON($data);
            } else {
                $res->status(404);
                $data = [
                    'status' => '404'
                ];
                $res->toJSON($data);
            }
        });

        //GET request route with parameters
        $router->get('/person/{id}/{firstName}/{lastName}', function ($id, $firstName, $lastName) {
            $res = new Response();
            echo 'first param(id): ' . $id . ', second param(first name): ' . $firstName . ', third param(last name): ' . $lastName;
        });

        //GET route that is handled by controller
        $router->get('/person/{id}', function ($id) {
            (new TestController())->getPersonById($id);
        });

        //POST request route handled by controller with csrf token validation
        //Check example usage in src/Views/ExampleView.php
        //Modify HiddenCSRF() function in public/index.php so it will meet your needs
        $router->post('/test', function () {
            (new TestController())->csrfValidationExample();
        });

        //valid entity example
        $router->get('/valid-entity', function () {
            $person = new Person();
            $person->setFirstName('test');
            $person->setLastName('test');
            $person->setLogin('testLogin');

            //check if all required properties are set - should return true
            $valid = $person->validate();
            echo 'Entity is valid and ready to be sent to db: ';
            echo $valid ? 'true' : 'false';
        });

        //invalid entity example
        $router->get('/invalid-entity', function () {
            $person = new Person();
            $person->setFirstName('test');
            $person->setLastName('test');
            $person->setLogin('');

            //check if all required properties are set, we did not set login property which is required - should return false.
            $valid = $person->validate();
            echo 'Entity is valid and ready to be sent to db: ';
            echo $valid ? 'true' : 'false';
        });


        $router->get('/error', function () {
            View::render('ExceptionView');
        });



        //dispatch current route provided by user. 
        $executed = $router->dispatch();
        if ($executed === false) {
            View::render('Error404');
        }
    }
}
