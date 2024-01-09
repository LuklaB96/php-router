<?php
namespace App\Main;

use App\Controller\TestController;
use App\Entity\ExampleEntity;
use App\Entity\Person;
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

        //basic GET request route thats renders view as a response.
        $router->get("/", function () {
            View::render(
                'ExampleView',
                [
                    'helloWorld' => 'Hello World!',
                ]
            );
        });
        $router->get('/attr', function () {
            $person = new Person();
            if ($person->find(7)) {
                $example = new ExampleEntity();
                $example->setPerson($person);
                $example->insert();

                var_dump($example->delete());
            }


            $example = new ExampleEntity();
            $example->setPerson($person);
            //$example->insert();
            $res = new Response();
            $res->toJSON(AttributeReader::getAttributes(new ExampleEntity()));
        });

        $router->get('/find-one', function () {
            $person = new Person();
            $person->find(5);
            echo $person->getFirstName() . '<br/><br/><br/>===================================<br/>';
        });
        $router->get('/find-one-by', function () {
            $person = new Person();
            $person->findOneBy(['id', '=', 10]);
            echo 'ID: ' . $person->getId() . '<br/>';
            echo $person->getFirstName() . '<br/>';
            echo $person->getLastName() . '<br/>';
        });
        $router->get('/find-all', function () {
            $example = new ExampleEntity();
            $repository = $example->findAll();
            foreach ($repository as $item) {
                echo 'Person ID: ' . $item->getId() . '<br/>';
                echo $item->getTitle() . '<br/>';
                echo $item->getDescription() . '<br/>';
            }
        });
        $router->get('/find-by', function () {
            $person = new Person();
            $repository = $person->findBy(['id', '>=', 10]);

            foreach ($repository as $item) {
                echo 'ID: ' . $item->getId() . '<br/>';
            }
        });

        $router->get('/find-by-with-offset', function () {
            $person = new Person();
            $repository = $person->findBy(['id', '>=', 10], limit: 10, offset: 10);

            foreach ($repository as $item) {
                echo 'ID: ' . $item->getId() . '<br/>';
            }
        });

        $router->get('/create-users', function () {
            for ($i = 1; $i <= 100; $i++) {
                $user = new User();
                $user->setLogin('Login' . $i);
                $user->setPassword('Password' . $i);
                $user->setEmail('Email' . $i);
                $user->setNickname('Nickname' . $i);
                $valid = $user->validate();
                if ($valid) {
                    $user->insert();
                }
            }
            echo 'Created ' . $i - 1 . ' records for User entity';
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
        $router->get('/db-execute-example', function () {
            (new TestController())->standardQueryExample();
        });

        //POST request route handled by controller with csrf token validation
        //Check example usage in src/Views/ExampleView.php
        //Modify HiddenCSRF() function in public/index.php so it will meet your needs
        $router->post('/csrf-test', function () {
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
