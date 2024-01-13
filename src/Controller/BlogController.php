<?php
namespace App\Controller;

use App\Lib\Controller\BaseController;
use App\Lib\View\View;

class BlogController extends BaseController
{
    /**
     * [GET] Main blog page
     * @return void
     */
    public function blogGET()
    {
        //$this->sendMail();
        //csrf validation
        if (!$this->authUser()) {
            $this->redirectToRoute('/login');
            return;
        }
        $view = new View('Blog/MultiblogViewPartial', 'Blog');
        $view->addStyle('blog/post.css');
        $view->addScript('app_posts.js', 'module');
        $view->renderPartial();
    }
    public function sendMail()
    {
        $email = 'matex12312@gmail.com';
        // set email subject & body
        $subject = 'Please activate your account';
        $message = <<<MESSAGE
            Hi,
            Please click the following link to activate your account:
            MESSAGE;
        // email header
        $header = "From:" . 'test@localhost';

        // send the email
        $sent = mail($email, $subject, nl2br($message), $header);
        error_log(print_r($sent, true));
    }
    /**
     * [GET] Render page with single post
     * @param int $postId
     * @return void
     */
    public function showPostPage(int $postId)
    {
        //csrf validation
        if (!$this->authUser()) {
            $this->redirectToRoute('/login');
            return;
        }
        $view = new View('Blog/MultiblogViewPartial', 'Blog');
        $view->addStyle('blog/post.css');
        $view->addScript('app_posts.js', 'module');
        $view->renderPartial();
    }
}