<div class="add-post-container">
    <h3>Add Post</h3>
    <div class="add-post-form">
        <label for="title"></label>
        <input class="input post-title-input" type="text" id="title" name="title" value="">
        <label for="content"></label>
        <span contenteditable role="textbox" rows="5" class="input text-area post-content-input" name="content"
            required></span>
        <button class="btn btn-primary" onclick="window.postManager.addPost()">Add Post</button>
    </div>
</div>
<div id="posts-container">

</div>