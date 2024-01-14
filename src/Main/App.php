<?php
namespace App\Main;

use App\Controller\AccountSettingsController;
use App\Controller\BlogController;
use App\Controller\LoginController;
use App\Controller\LogoutController;
use App\Controller\PostApiController;
use App\Controller\RegisterController;
use App\Controller\UserController;
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
            $view = new View('Home/HomeViewPartial', 'Multiblog');
            $view->addStyle('error.css');
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
            header("Location: /blog/page/1");
        });
        $router->get('/blog/page/{page}', function ($page) {
            (new BlogController())->showAllPosts($page);
        });
        $router->get('/blog/post/{id}', function ($postId) {
            (new BlogController())->showSinglePost($postId);
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

        //acount route handlers
        $router->get('/account/activate/{code}', function ($code) {
            (new UserController())->activateGET($code);
        });
        $router->get('/profile/{login}', function ($login) {
            (new UserController())->showProfile($login);
        });
        $router->get('/account/recovery', function () {
            (new UserController())->forgotPasswordGET();
        });
        $router->post('/account/recovery', function () {
            (new UserController())->forgotPasswordPOST();
        });
        $router->get('/account/recovery/{code}', function ($code) {
            (new UserController())->passwordRecoveryFormGET($code);
        });
        $router->post('/account/recovery/submit', function () {
            (new UserController())->passwordRecoveryFormPOST();
        });

        $router->get('/account/settings', function () {
            (new AccountSettingsController())->profileSettingsGET();
        });
        $router->post('/account/password/change', function () {
            (new AccountSettingsController())->changePasswordPOST();
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
