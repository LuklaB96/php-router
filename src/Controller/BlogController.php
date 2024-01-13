<?php
namespace App\Controller;

use App\Entity\Post;
use App\Lib\Controller\BaseController;
use App\Lib\View\View;

class BlogController extends BaseController
{
    /**
     * [GET] Main /blog/page/{page}
     * @return void
     */
    public function showAllPosts($page, $limit = 10)
    {
        //$this->sendMail();
        //csrf validation
        if (!$this->authUser()) {
            $this->redirectToRoute('/login');
            return;
        }
        $post = new Post();
        $postCount = $post->count();
        $lastPage = $postCount / 10;
        $data['page'] = $page;
        $data['lastPage'] = $lastPage;

        $view = new View('Blog/BlogMainViewPartial', 'Blog');
        $view->addStyle('blog/post.css');
        $view->addScript('app_posts.js', 'module');
        $view->renderPartial($data);
    }
    /**
     * [GET] Render page with single post
     * @param int $postId
     * @return void
     */
    public function showSinglePost($postId)
    {
        //csrf validation
        if (!$this->authUser()) {
            $this->redirectToRoute('/login');
            return;
        }
        $view = new View('Blog/BlogPostViewPartial', 'Blog');
        $view->addStyle('blog/post.css');
        $view->addScript('app_posts.js', 'module');
        $view->renderPartial(['postId' => $postId]);
    }
}