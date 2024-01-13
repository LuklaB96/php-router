<?php
namespace App\Main;

use App\Controller\AccountController;
use App\Controller\BlogController;
use App\Controller\LoginController;
use App\Controller\LogoutController;
use App\Controller\PostApiController;
use App\Controller\RegisterController;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Lib\Routing\Router;
use App\Lib\View\View;

class App
{
    public static function run()
    {
        // Main router instance
        $router = Router::getInstance();

        // ROUTES

        // Authentication
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

        // Base routes avaible for authorized and unauthorized users
        $router->get("/", function () {
            $view = new View('main', 'Multiblog');
            $view->renderPartial();
        });
        $router->get('/error', function () {
            $view = new View('Error500PartialView', '500 - Internal Server Error');
            $view->addStyle('error.css');
            $view->renderPartial();
        });

        // Routes only for authorized users
        // Show main page with all posts including pagination and filters
        $router->get('/blog', function () {
            (new BlogController())->blogGET();
        });
        $router->get('/blog/post/{id}', function ($postId) {
            (new BlogController())->showPostPage($postId);
        });
        // Returns 10 posts with two first comments
        // To fetch more comments use /api/v1/post/{postId} route
        $router->get('/api/v1/posts/page/{page}', function (int $page) {
            $limit = 10;
            (new PostApiController())->apiGetPostsPage($limit, $page);
        });
        // Returns all data for single post
        $router->get('/api/v1/post/{postId}', function ($postId) {
            (new PostApiController())->apiGetPostData($postId);
        });
        $router->post('/api/v1/comment/create', function () {
            (new PostApiController())->apiCreateComment();
        });
        $router->post('/api/v1/post/create', function () {
            (new PostApiController())->apiCreatePost();
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
                // Post data
                $postTitles = ['Interesting Topic', 'My Thoughts', 'A Journey', 'Exploring...', 'Discovering New Things'];
                $postContents = ['Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.', 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.', 'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'];

                for ($j = 0; $j < 5; $j++) {
                    $post = new Post();
                    $post->setTitle($postTitles[$j]);
                    $post->setContent($postContents[$j]);
                    $post->setAuthor($user);
                    $valid = $post->validate();

                    if ($valid) {
                        if ($post->insert()) {
                            $posts++;
                        }
                    }

                    // Comment data
                    $commentContents = ['Great post!', 'Interesting perspective.', 'I totally agree!', 'Keep up the good work.', 'Looking forward to more content.'];

                    for ($k = 0; $k < 5; $k++) {
                        $comment = new Comment();
                        $comment->setContent($commentContents[$k]);
                        $comment->setAuthor($user);
                        $comment->setPost($post);
                        $valid = $comment->validate();

                        if ($valid) {
                            if ($comment->insert()) {
                                $comments++;
                            }
                        }
                    }
                }
            }
            echo 'Created ' . $users . ' records for User entity</br>';
            echo 'Created ' . $posts . ' records for Post entity</br>';
            echo 'Created ' . $comments . ' records for Comment entity';
        });

        //acount route handlers
        $router->get('/account/activate/{code}', function ($code) {
            (new AccountController())->activateGET($code);
        });

        $router->get('/phpinfo', function () {
            phpinfo();
        });

        // Dispatch requested route
        $executed = $router->dispatch();
        if ($executed === false) {
            $view = new View('Error404PartialView', '404 - Page Not Found');
            $view->addStyle('error.css');
            $view->renderPartial();
        }
    }
}
