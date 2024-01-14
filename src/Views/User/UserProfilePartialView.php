<?php
use App\Entity\Comment;
use App\Entity\Post;

$user = $data['user'];
$post = new Post();
$postRepository = $post->findby(['user_id', '=', $user->getId()]);
if (isset($postRepository) && is_array($postRepository) && count($postRepository) > 0)
    foreach ($postRepository as $p) {
        if (isset($p)) {
            $comment = new Comment();
            $commentsAmount = $comment->count(['post_id', '=', $p->getId()]);
            $commentRepository = $comment->findBy(['post_id', '=', $p->getId()], limit: 2, orderBy: 'id ASC');
            echo '<div class="post-container" id="post-container-' . $p->getId() . '">';
            echo '<div class="post-author">Author: <span><a class="basic-link" href="/profile/' . $p->getAuthor()->getLogin() . '">' . $p->getAuthor()->getLogin() . '</a></span></div>';
            echo '<div class="post-info">';
            echo '<h1>' . $p->getTitle() . '</h1>';
            echo '</div>';
            echo '<p>' . $p->getContent() . '</p>';
            echo '<div class="comment-section">';
            echo '<h2>Comments</h2>';
            echo '<div class="comments">';
            foreach ($commentRepository as $comment) {
                echo '<div class="comment">';
                echo '<span class="comment-author">By: <a class="basic-link" href="/profile/' . $comment->getAuthor()->getLogin() . '">' . $comment->getAuthor()->getLogin() . '</a></span>';
                echo '<p class="comment-content">' . $comment->getContent() . '</p>';
                echo '</div>';
            }
            echo '</div>';
            echo '</div>';
            if ($commentsAmount > 2) {
                echo '<a class="basic-link" href="/blog/post/' . $p->getId() . '">More comments: ' . $commentsAmount - 2 . '</a>';
            }
            echo '</div>';
        }
    }
?>