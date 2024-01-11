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
        $router->get('/przyklad', function () {
            View::render('jakiesview', ['nazwa2' => 'jakis text']);
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
        $router->get('/find-post-data', function () {
            $post = new Post();
            $post->find(19);
            $comment = new Comment();
            $commentsRepository = $comment->findBy(['post_id', '=', $post->getId()]);
            $data[0] = [
                'id' => $post->getId(),
                'title' => $post->getTitle(),
                'content' => $post->getContent(),
                'author' => [
                    'id' => $post->getAuthorId(),
                    'nickname' => $post->getAuthor()->getLogin(),
                    'email' => $post->getAuthor()->getEmail(),
                ]
            ];
            foreach ($commentsRepository as $c) {
                $data[0]['comments'][$c->getId()] = [
                    'content' => $c->getContent(),
                    'author' => [
                        'id' => $c->getAuthor()->getId(),
                        'nickname' => $c->getAuthor()->getNickname(),
                        'email' => $c->getAuthor()->getEmail(),
                    ]
                ];
            }
            echo (new Response())->toJSON($data);
        });

        $router->get('/find-one', function () {
            $user = new User();
            $user->find(1);
            echo $user->getLogin() . '<br/><br/><br/>===================================<br/>';
        });
        $router->get('/find-one-by', function () {
            $comment = new Comment();
            $comment->findOneBy(['id', '=', 1]);

            $author = $comment->getAuthor();
            echo $author->getLogin() . '';
        });
        $router->get('/find-all', function () {
            $example = new User();
            $repository = $example->findAll();
            foreach ($repository as $item) {
                echo 'User ID: ' . $item->getId() . '<br/>';
                echo $item->getEmail() . '<br/>';
                echo $item->getLogin() . '<br/>';
            }
        });
        $router->get('/find-by', function () {
            $comment = new Comment();
            // findBy should be extended to read more conditions
            $repository = $comment->findBy([
                ['id', '>=', 1],
                ['id', '<=', 3]
            ]);

            foreach ($repository as $item) {
                echo 'ID: ' . $item->getId() . '<br/>';
            }
        });

        $router->get('/find-by-with-offset', function () {
            $user = new User();
            $page = 1;
            $offest = 10;
            $finalOffset = ($page - 1) * $offest;
            $repository = $user->findBy(['id', '>=', 10], limit: 10, offset: $finalOffset);

            foreach ($repository as $item) {
                echo 'ID: ' . $item->getId() . '<br/>';
            }
        });

        $router->get('/get-post-author-data', function () {
            $post = new Post();
            $post->find(1);
            echo $post->getContent() . '</br>';
            echo $post->getAuthor()->getId() . '';
        });

        $router->get('/create-data', function () {
            $users = 0;
            $posts = 0;
            $comments = 0;
            for ($i = 0; $i < 5; $i++) {
                $user = new User();
                $user->setLogin('Login' . $i);
                $user->setPassword('Password' . $i);
                $user->setEmail('Email' . $i);
                $valid = $user->validate();

                if ($valid) {
                    if ($user->insert()) {
                        $users++;
                    } else {
                        $user->find($i);
                    }

                }
                for ($j = 0; $j < 5; $j++) {
                    $post = new Post();
                    $post->setTitle('Title' . $j);
                    $post->setContent('Content' . $j);
                    $post->setAuthor($user);
                    $valid = $post->validate();
                    if ($valid) {
                        if ($post->insert())
                            $posts++;
                    }
                    for ($k = 0; $k < 5; $k++) {
                        $comment = new Comment();
                        $comment->setContent('Content' . $k);
                        $comment->setAuthor($user);
                        $comment->setPost($post);
                        $valid = $comment->validate();
                        if ($valid) {
                            if ($comment->insert())
                                $comments++;
                        }

                    }
                }
            }
            echo 'Created ' . $users . ' records for User entity</br>';
            echo 'Created ' . $posts . ' records for Post entity</br>';
            echo 'Created ' . $comments . ' records for Comment entity';
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
            $user = new User();
            $user->setEmail("email");
            $user->setLogin("login");
            $user->setPassword("password");

            //check if all required properties are set - should return true
            $valid = $user->validate();
            echo 'Entity is valid and ready to be sent to db: ';
            echo $valid ? 'true' : 'false';
        });

        //invalid entity example
        $router->get('/invalid-entity', function () {
            $user = new User();
            $user->setEmail("email");

            //check if all required properties are set, we did not set login property which is required - should return false.
            $valid = $user->validate();
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
