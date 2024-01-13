<?php
use App\Entity\Comment;
use App\Entity\Post;

$postId = $data['postId'];
$post = new Post();
$postExists = $post->find($postId);
if ($postExists) {
    $comment = new Comment();
    $commentRepository = $comment->findBy(['post_id', '=', $post->getId()]);
    echo '<div class="post-container" id="post-container-' . $post->getId() . '">';
    echo '<div class="post-author">Author: <span>' . $post->getAuthor()->getLogin() . '</span></div>';
    echo '<div class="post-info">';
    echo '<h1>' . $post->getTitle() . '</h1>';
    echo '</div>';
    echo '<p>' . $post->getContent() . '</p>';
    echo '<div class="comment-section">';
    echo '<h2>Comments</h2>';
    echo '<div class="comments">';
    foreach ($commentRepository as $comment) {
        echo '<div class="comment">';
        echo '<span class="comment-author">By: ' . $comment->getAuthor()->getLogin() . '</span>';
        echo '<p class="comment-content">' . $comment->getContent() . '</p>';
        echo '</div>';
    }
    echo '</div>';
    echo '<div class="add-comment-form">';
    echo '<label for="commentContent">Answer:</label>';
    echo '<span contenteditable role="textbox" rows="5" class="input text-area" id="commentContent-' . $post->getId() . '"';
    echo 'name="commentContent" required></span>';
    echo '<button class="btn btn-primary" onclick="window.postManager.addComment(' . $post->getId() . ')">Add';
    echo 'Comment</button>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}
?>