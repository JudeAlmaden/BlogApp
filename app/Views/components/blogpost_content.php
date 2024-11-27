<?php if (isset($results['error'])): ?>
    <div class="alert alert-danger">
        <strong>Error:</strong> <?= $results['error']; ?>
    </div>
<?php else: ?>


    
    <div class="container mt-5">
        <div class="blog-post border rounded p-4 shadow-lg">
            <?php if ($isAdmin): ?>
                <div class="d-flex justify-content-end">
                    <a href="delete-post?id=<?php echo $post_id; ?>" class="text-secondary small" style="margin-right:0px;" onclick="return confirm('Are you sure you want to delete this post?');">
                        <i class="fas fa-trash me-1"></i>Delete
                    </a>
                </div>
            <?php endif; ?>

            <!-- Blog Title -->
            <h1 class="post-title mb-4"><?= htmlspecialchars($post['title']); ?></h1>

            <!-- Post Meta (Author and Date) -->
            <p class="post-meta text-muted mb-4">
                <small>Published on <?= date('F j, Y', strtotime($post['created_at'])); ?> by <a href="view-profile?id=<?=$post['user_id']?>"><strong><?= htmlspecialchars($post['author']); ?></strong></a></small>
            </p>


            <!-- Tags -->
            <?php if (!empty($post['all_tags'])): ?>
                <div class="post-tags">
                    <strong>Tags:</strong>
                    <?php
                        $tags = explode(',', $post['all_tags']);
                        echo implode(', ', array_map('htmlspecialchars', $tags));
                    ?>
                </div>
            <?php endif; ?>

            <!-- Categories -->
            <?php if (!empty($post['all_categories'])): ?>
                <div class="post-categories mb-3">
                    <strong>Categories:</strong>
                    <?php
                        $categories = explode(',', $post['all_categories']);
                        echo implode(', ', array_map('htmlspecialchars', $categories));
                    ?>
                </div>
            <?php endif; ?>

            <!-- Post Content -->
            <div class="content-wrapper ">
                <div class="post-content mb-4 border-start ps-5 border-dark pl-3">
                    <p><?= nl2br(htmlspecialchars($post['content'])); ?></p>
                </div>
            </div>

            <!-- Images -->
            <?php if (!empty($post['media_url'])): ?>
            <!-- Carousel Container -->
            <div id="imageCarousel" class="carousel carousel-dark slide mb-4" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php
                        $images = explode(',', $post['media_url']);
                        $first = true; // To handle the first image active class
                        foreach ($images as $image): 
                    ?>
                        <div class="carousel-item <?php echo $first ? 'active' : ''; ?>">
                            <!-- Image Thumbnail with height limit of 400px and centered -->
                            <img src="<?php echo htmlspecialchars($image); ?>" 
                                class="d-block w-100 img-fluid rounded mb-3 thumbnail-image" 
                                alt="Image"
                                data-bs-toggle="modal" 
                                data-bs-target="#imageModal"
                                style="max-height: 400px; object-fit: contain; cursor: pointer;">
                        </div>
                        <?php 
                            $first = false;
                        endforeach; 
                    ?>
                </div>
                <!-- Carousel Controls -->
                <button class="carousel-control-prev" type="button" data-bs-target="#imageCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#imageCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>

            <!-- Modal for Displaying Larger Image -->
            <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="imageModalLabel">Image View</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <img id="modal-image" src="" alt="Large Image" class="img-fluid">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                // JavaScript to update the modal image source when a thumbnail is clicked
                const thumbnailImages = document.querySelectorAll('.thumbnail-image');
                thumbnailImages.forEach(image => {
                    image.addEventListener('click', function () {
                        const imageSrc = this.src; // Get the clicked image source
                        document.getElementById('modal-image').src = imageSrc; // Set the modal image source
                    });
                });
            </script>
            <?php endif; ?>

            <!-- Like Button and Like Count -->
            <div class="like-section col-12 d-flex justify-content-end align-items-center mb-4">
                <!-- Like Count -->
                <span id="like-count" class="mx-2"><?= $post['likes']; ?> Likes</span>

                <!-- Like Button -->
                <button class="btn btn-outline-primary" id="like-button" onclick="toggleLike(<?= $post['id']; ?>)" style="width: 100px;">
                    <i class="fa fa-thumbs-up"></i> Like
                </button>
            </div>
        </div>
    </div>

<?php endif; ?>

<script>
    // Initial state of the like status (Assuming $isLiked is passed from the server-side)
    let isLiked = <?=$isLiked?>;

    
    document.addEventListener('DOMContentLoaded', function() {
        const likeButton = document.getElementById("like-button");

        // Set the initial state of the like button based on isLiked
        if (isLiked) {
            likeButton.classList.add("btn-primary");
            likeButton.classList.remove("btn-outline-primary");
            likeButton.innerHTML = '<i class="fa fa-thumbs-up"></i> Liked';

        } else {
            likeButton.classList.remove("btn-primary");
            likeButton.classList.add("btn-outline-primary");
            likeButton.innerHTML = '<i class="fa fa-thumbs-up"></i> Like';
        }
    });
    
    function toggleLike(postId) {
        // Toggle the like status
        const likeButton = document.getElementById("like-button");
        const likeCountElement = document.getElementById("like-count");
        let currentLikes = parseInt(likeCountElement.textContent);

        // Check the current like status based on the button's class
        const buttonIsLiked = likeButton.classList.contains("btn-primary");

        // Update the UI immediately to reflect the new like status
        if (buttonIsLiked) {
            likeButton.classList.remove("btn-primary");
            likeButton.classList.add("btn-outline-primary");
            likeButton.innerHTML = '<i class="fa fa-thumbs-up"></i> Like';
            likeCountElement.textContent = (currentLikes - 1) + " Likes";
        } else {
            likeButton.classList.add("btn-primary");
            likeButton.classList.remove("btn-outline-primary");
            likeButton.innerHTML = '<i class="fa fa-thumbs-up"></i> Liked';
            likeCountElement.textContent = (currentLikes + 1) + " Likes";
        }

        // Make the AJAX request to update the like status in the backend using GET (pass data via query params)
        const url = `api/like/post?post_id=${postId}`;
        
        fetch(url, {
            method: 'GET',  // Use GET for fetching/updating data
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // If the like status was updated successfully on the server
                console.log('Post like status updated:', data.message);
                alert("You " + (buttonIsLiked ? "unliked" : "liked") + " post #" + postId);
            } else {
                // If the request fails, revert the UI changes
                alert('Failed to update like status. Please try again.');
                if (buttonIsLiked) {
                    likeButton.classList.add("btn-primary");
                    likeButton.classList.remove("btn-outline-primary");
                    likeButton.innerHTML = '<i class="fa fa-thumbs-up"></i> Liked';
                    likeCountElement.textContent = (currentLikes + 1) + " Likes";
                } else {
                    likeButton.classList.remove("btn-primary");
                    likeButton.classList.add("btn-outline-primary");
                    likeButton.innerHTML = '<i class="fa fa-thumbs-up"></i> Like';
                    likeCountElement.textContent = (currentLikes - 1) + " Likes";
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error occurred while updating the like status.');
        });
    }
</script>


<!-- Include Font Awesome for the like icon (if you haven't already) -->
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
