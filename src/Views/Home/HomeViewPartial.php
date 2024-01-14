<?php
if (!isset($_SESSION['user'])) {
    $message = 'No content here, <a class="basic-link" href="/register">sign up</a> or <a class="basic-link" href="/login">sign in</a> to see anything.';
} else {
    $message = 'There is no content too, awful! Go to <a class="basic-link" href="/blog">blog page</a>.';
}

?>
<div class="server-message-container">
    <h1>Hey!</h1>
    <p>
        <?php echo $message; ?>
    </p>
</div>