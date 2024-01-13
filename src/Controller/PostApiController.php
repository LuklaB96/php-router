<?php
namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Lib\Controller\BaseController;
use App\Lib\Database\Entity\Entity;
use App\Lib\View\View;

class PostApiController extends BaseController
{
    private array $error = [];
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * [GET] Route /api/v1/posts/page/{page}
     * @param int $limit
     * @param int $page
     * @return void
     */
    public function apiGetPostsPage(int $limit, int $page)
    {
        $offset = $limit * ($page - 1);
        $post = new Post();
        $postRepository = $post->findBy(limit: $limit, offset: $offset);

        $data = [];
        foreach ($postRepository as $post) {
            $data['posts'][$post->getId()]['title'] = $post->getTitle();
            $data['posts'][$post->getId()]['content'] = $post->getContent();
            $data['posts'][$post->getId()]['author'] = $post->getAuthor()->getLogin();
            $comment = new Comment();
            $commentRepository = $comment->findBy(['post_id', '=', $post->getId()], limit: 2);
            $commentCount = $comment->count(['post_id', '=', $post->getId()]);
            $data['posts'][$post->getId()]['commentsCountDb'] = $commentCount;
            foreach ($commentRepository as $comment) {

                $data['posts'][$post->getId()]['comments'][$comment->getId()]['author'] = $comment->getAuthor()->getLogin();
                $data['posts'][$post->getId()]['comments'][$comment->getId()]['content'] = $comment->getContent();
            }
        }
        $this->postResponse(200, $data);
    }
    /**
     * [GET] /api/v1/post/{postId}
     * @param int $postId
     * @return void
     */
    public function apiGetPostData($postId)
    {
        $post = new Post();
        $post->find($postId);
        $data = [];
        if ($post->exists()) {
            $data['post'][$post->getId()]['title'] = $post->getTitle();
            $data['post'][$post->getId()]['content'] = $post->getContent();
            $data['post'][$post->getId()]['createdAt'] = $post->getCreatedAt();
            $data['post'][$post->getId()]['author'] = $post->getAuthor()->getLogin();
            $comment = new Comment();
            $commentRepository = $comment->findBy(['post_id', '=', $post->getId()]);
            foreach ($commentRepository as $comment) {
                $data['post'][$post->getId()]['comments'][$comment->getId()]['author'] = $comment->getAuthor()->getLogin();
                $data['post'][$post->getId()]['comments'][$comment->getId()]['content'] = $comment->getContent();
                $data['post'][$post->getId()]['comments'][$comment->getId()]['createdAt'] = $comment->getCreatedAt();
            }
            $this->postResponse(200, $data);
            return;
        }
        $this->postResponse(404, 'Post not found');
        return;
    }
    /**
     * [POST] /api/v1/post/create
     * @return void
     */
    public function apiCreatePost()
    {
        //csrf validation
        if (!$this->csrfAuth()) {
            $this->postResponse(401, 'Unauthorized');
            return;
        }
        //validate data
        $data = $this->request->getData();
        if (empty($data)) {
            $this->error['message'] = 'empty data';
            $this->postResponse(400, $this->error);
            return;
        }
        $content = $data['content'];
        if (empty($content)) {
            $this->error['message'] = 'empty data';
            $this->postResponse(400, $this->error);
            return;
        }
        $postContent = $data['content'];
        $postTitle = $data['title'];


        $userId = $_SESSION['user'];

        $post = new Post();
        $user = new User();
        $user->find($userId);
        $post->setAuthor($user);
        $post->setTitle($postTitle);
        $post->setContent($postContent);
        $inserted = $post->insert();
        if ($inserted) {
            $resData['post']['author'] = $post->getAuthor()->getLogin();
            $resData['post']['content'] = $post->getContent();
            $resData['post']['title'] = $post->getTitle();
            $resData['post']['id'] = $post->getId();
            $this->postResponse(200, $resData);
        } else {
            $this->postResponse(400, 'Error');
        }
    }
    /**
     * [POST] /api/v1/comment/create
     * @return void
     */
    public function apiCreateComment()
    {
        //csrf validation
        if (!$this->csrfAuth()) {
            $this->postResponse(401, 'Unauthorized');
            return;
        }

        //validate data
        $data = $this->request->getData();
        if (empty($data)) {
            $this->error['message'] = 'empty data';
            $this->postResponse(400, $this->error);
            return;
        }
        $content = $data['content'];
        if (empty($content)) {
            $this->error['message'] = 'empty data';
            $this->postResponse(400, $this->error);
            return;
        }
        $postId = $data['postId'];
        $userId = $_SESSION['user'];

        $post = new Post();
        $post->find($postId);
        $user = new User();
        $user->find($userId);

        $comment = new Comment();
        $comment->setContent($content);
        $comment->setPost($post);
        $comment->setAuthor($user);
        $inserted = $comment->insert();
        if ($inserted) {
            $resData['comment']['author'] = $user->getLogin();
            $resData['comment']['content'] = $comment->getContent();
            $this->postResponse(200, $resData);
        } else {
            $this->postResponse(400, 'Error');
        }

    }
}