<div class="add-post-container">
    <h3>Add Post</h3>
    <div class="add-post-form">
        <label for="title"></label>
        <input class="input post-title-input" type="text" id="title" name="title" value="" placeholder="Title..">
        <label for="content"></label>
        <span contenteditable role="textbox" rows="5" class="input text-area post-content-input" name="content"
            placeholder="What is on your mind?" required></span>
        <button class="btn btn-primary" onclick="window.postManager.addPost()">Add Post</button>
    </div>
</div>
<div id="posts-container">

</div>
<div class="pagination-container">
    <?php
    if ($data['page'] > 1) {
        echo '<a class="page-btn border-right basic-link" href="/blog/page/1">First Page </a>';
    } else {
        echo '<a class="page-btn border-right basic-link""></a>';

    }
    if ($data['page'] > 1) {
        echo '<a class="page-btn border-right basic-link" href="/blog/page/' . $data['page'] - 1 . '">Previous Page </a>';
    }
    if ($data['page'] < $data['lastPage']) {
        echo '<a class="page-btn border-left basic-link" style="margin-left: auto;" href="/blog/page/' . $data['page'] + 1 . '">Next Page </a>';
    } else {
        echo '<a class="page-btn border-left basic-link" style="margin-left: auto;"></a>';
    }
    ?>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        window.postManager.fetchPosts();
    });

</script>