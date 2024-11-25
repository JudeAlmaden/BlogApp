<?php if (isset($results['error'])): ?>
    <div class="alert alert-danger">
        <strong>Error:</strong> <?= $results['error']; ?>
    </div>
<?php else: ?>

    <div class="container mt-5">
        <div class="blog-post border rounded p-4 shadow-lg">
            <!-- Blog Title -->
            <h1 class="post-title mb-4"><?= htmlspecialchars($results['title']); ?></h1>

            <!-- Post Meta (Author and Date) -->
            <p class="post-meta text-muted mb-4">
                <small>Published on <?= date('F j, Y', strtotime($results['created_at'])); ?> by <strong><?= htmlspecialchars($results['author']); ?></strong></small>
            </p>


            <!-- Tags -->
            <?php if (!empty($results['all_tags'])): ?>
                <div class="post-tags">
                    <strong>Tags:</strong>
                    <?php
                        $tags = explode(',', $results['all_tags']);
                        echo implode(', ', array_map('htmlspecialchars', $tags));
                    ?>
                </div>
            <?php endif; ?>

            <!-- Categories -->
            <?php if (!empty($results['all_categories'])): ?>
                <div class="post-categories mb-3">
                    <strong>Categories:</strong>
                    <?php
                        $categories = explode(',', $results['all_categories']);
                        echo implode(', ', array_map('htmlspecialchars', $categories));
                    ?>
                </div>
            <?php endif; ?>

            <!-- Post Content -->
            <div class="content-wrapper ">
                <div class="post-content mb-4 border-start ps-5 border-dark pl-3">
                    <p><?= nl2br(htmlspecialchars($results['content'])); ?></p>
                </div>
            </div>

            <!-- Images -->
            <?php if (!empty($results['media_url'])): ?>
            <!-- Carousel Container -->
            <div id="imageCarousel" class="carousel carousel-dark slide mb-4" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php
                        $images = explode(',', $results['media_url']);
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
                <span id="like-count" class="mx-2"><?= $results['likes']; ?> Likes</span>

                <!-- Like Button -->
                <button class="btn btn-outline-primary" id="like-button" onclick="toggleLike(<?= $results['id']; ?>)" style="width: 100px;">
                    <i class="fa fa-thumbs-up"></i> Like
                </button>
            </div>


        </div>
    </div>

<?php endif; ?>

<script>
    let isLiked = false; // Track the like status (initially not liked)

    function toggleLike(postId) {
        // Toggle the like status
        isLiked = !isLiked;

        // Update the button and like count based on the new like status
        const likeButton = document.getElementById("like-button");
        const likeCountElement = document.getElementById("like-count");
        let currentLikes = parseInt(likeCountElement.textContent);

        if (isLiked) {
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

        // Simulate AJAX call to update the like status in the database
        // Make an AJAX request to update the like status in the backend
        // e.g., using fetch or XMLHttpRequest
        alert("You " + (isLiked ? "liked" : "unliked") + " post #" + postId);
    }
</script>

<!-- Include Font Awesome for the like icon (if you haven't already) -->
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
