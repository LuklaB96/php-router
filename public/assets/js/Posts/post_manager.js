class PostManager {

    async fetchPosts() {
        try {
            const pageNumber = this.getPageNumberFromUrl();
            const response = await fetch('/api/v1/posts/page/' + pageNumber);
            const jsonData = await response.json();
            this.displayPosts(jsonData.data.posts);
        } catch (error) {
            console.error('Error fetching posts:', error);
        }
    }


    async fetchMoreComments(postId) {
        try {
            const response = await fetch(`/api/v1/post/${postId}`);
            const jsonData = await response.json();

            if (jsonData.code === 200 && jsonData.data.post && jsonData.data.post[postId]) {
                const postData = jsonData.data.post[postId];
                const postContainer = document.getElementById(`post-container-${postId}`);
                const commentSection = postContainer.querySelector('.comments');

                // Generate and append additional comments
                commentSection.insertAdjacentHTML('beforeend', this.generateCommentsHTML(postId, postData.comments));

                // Remove the "Load More Comments" button
                const loadMoreButton = postContainer.querySelector('.load-more-comments-btn');
                if (loadMoreButton) {
                    loadMoreButton.remove();
                }
            } else {
                console.error(`Invalid response for post ${postId}:`, jsonData);
            }
        } catch (error) {
            console.error(`Error fetching more comments for post ${postId}:`, error);
        }
    }
    async addPost() {
        try {
            const title = document.getElementById('title');
            const content = document.querySelector('.post-content-input');

            // Validate title and content
            if (!title.value.trim() || !content.innerText.trim()) {
                return;
            }

            // Assuming your server API endpoint for adding posts is /api/v1/posts
            const apiUrl = '/api/v1/post/create';

            // Example data to be sent in the request body
            const postData = {
                title: title.value,
                content: content.innerText,
                token: document.getElementById('token').value
            };

            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(postData),
            });

            const jsonData = await response.json();

            if (jsonData.code === 200 && jsonData.data.post) {
                // Assuming the server responds with the new post data
                const newPost = jsonData.data.post;
                title.value = '';
                content.innerText = '';

                // Update the posts section in the UI
                const postsContainer = document.getElementById('posts-container');
                const postHTML = this.generatePostHTML(newPost.id, newPost);

                // Append the new post to the posts section
                postsContainer.insertAdjacentHTML('afterbegin', postHTML);
            } else {
                console.error('Invalid response for adding post:', jsonData);
            }
        } catch (error) {
            console.error('Error adding post:', error);
        }
    }
    getPageNumberFromUrl() {
        const url = window.location.href;
        const lastSlashIndex = url.lastIndexOf('/');
        const pageNumber = url.substring(lastSlashIndex + 1);

        return parseInt(pageNumber, 10) || 1; // Default to 1 if not a valid number
    }

    addComment(postId) {
        const commentContent = document.getElementById(`commentContent-${postId}`);

        // Validate content
        if (!commentContent.textContent.trim()) {
            return;
        }

        // Assuming your server API endpoint for adding comments is /api/v1/comments
        const apiUrl = '/api/v1/comment/create';

        // Example data to be sent in the request body
        const commentData = {
            postId: postId,
            content: commentContent.textContent,
            token: document.getElementById('token').value
        };

        fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(commentData),
        })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(JSON.stringify(data));
                    });
                }
                return response.json();
            })
            .then(data => {

                const newComment = data.data.comment;

                // Update the comments section in the UI
                commentContent.textContent = '';
                const postContainer = document.getElementById(`post-container-${postId}`);
                const commentSection = postContainer.querySelector('.comments');

                // Append the new comment to the comments section
                commentSection.insertAdjacentHTML('beforeend', `
            <div class="comments">
                <div class="comment">
                    <span class="comment-author">By: <a class="basic-link" href="/profile/${newComment.author}">${newComment.author}</a></span>
                    <p class="comment-content">${newComment.content}</p>
                </div>
            </div>
        `);
            })
            .catch(error => {
                console.log(error);
            });
    }

    generatePostHTML(postId, post, loadAll = false) {

        let loadMoreCommentsBtn = ``;
        if (post.commentsCountDb - 2 > 0 && !loadAll) {
            loadMoreCommentsBtn = `
                <button class="btn btn-primary load-more-comments-btn" onclick="postManager.fetchMoreComments('${postId}')">
                    Load more comments(${post.commentsCountDb - 2})
                </button>
            `
        }
        let commentField = ``;
        let replyButton = ``;
        if (window.location.pathname === '/blog/post/' + postId || post.commentsCountDb === 0 || post.commentsCountDb === undefined) {
            commentField = `
                <div class="add-comment-form">
                    <label for="commentContent">Answer:</label>
                    <span contenteditable role="textbox" rows="5" class="input text-area" id="commentContent-${postId}" name="commentContent" required></span>
                    <button class="btn btn-primary" onclick="window.postManager.addComment('${postId}')">Add Comment</button>
                </div>
            `
        } else {
            replyButton = `<a class="basic-link" href="/blog/post/${postId}">Reply</a>`;
        }
        let postContainer = `
            <div class="post-container" id="post-container-${postId}">
                <div class="post-author">Author: <span><a class="basic-link" href="/profile/${post.author}">${post.author}</a></span></div>
                <div class="post-info">
                    <h1><a class="basic-link" href="/blog/post/${postId}">${post.title}</a></h1>
                </div>
                <p>${post.content}</p>
                <div class="comment-section">
                    ${replyButton}
                    <h2>Comments</h2>
                    <div class="comments">
                        ${this.generateCommentsHTML(postId, post.comments)}
                    </div>
                    ${loadMoreCommentsBtn}
                    ${commentField}
                </div>
            </div>
            `;

        return postContainer;
    }

    generateCommentsHTML(postId, comments) {
        let commentsHTML = '';
        if (comments) {

            const startFromIndex = Object.keys(comments).length > 2 ? 2 : 0;

            let index = 0;
            for (const commentId in comments) {
                if (index < startFromIndex) {
                    // Skip comments until reaching the starting index
                    index++;
                    continue;
                }

                const comment = comments[commentId];
                commentsHTML += `
                <div class="comment">
                    <span class="comment-author">By: <a class="basic-link" href="/profile/${comment.author}">${comment.author}<a/></span>
                    <p class="comment-content">${comment.content}</p>
                </div>
        `;
            }
        }
        return commentsHTML;
    }


    displayPosts(posts) {
        const postsContainer = document.getElementById('posts-container');
        for (const postId in posts) {
            const post = posts[postId];
            const postHTML = this.generatePostHTML(postId, post);
            postsContainer.insertAdjacentHTML('beforeend', postHTML)
        }
    }
}
export default PostManager;