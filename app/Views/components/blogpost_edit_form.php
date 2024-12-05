<?php if (isset($post)): ?>

<div class="container py-5">
    <h1 class="text-center mb-4">Edit Blog Post</h1>
    <form id="category-form" action="edit-post" method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">
        <!-- Title Field -->
        <div class="mb-4">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="Enter post title" value="<?=$post['title']?>" required>
            <input type="text" class="form-control" id="post_id" name="post_id" value="<?=$post["id"]?>" required hidden>
        </div>

        <!-- Content Field -->
        <div class="mb-4">
            <label for="content" class="form-label">Content</label>
            <textarea class="form-control" id="content" name="content" rows="6" placeholder="Write your content here..."  required><?=$post['content']?></textarea>
        </div>

        <!-- Category Selection -->
        <div class="mb-4">
            <label for="category-search" class="form-label">Category</label>
            <input type="text" class="form-control" id="category-search" placeholder="Search for a category" autocomplete="off">
            <ul id="category-list" class="list-group mt-2"></ul>
            <div class="mt-1" style="font-size:10px">
                <span class="fw-bold">Previously Selected Categories: <span id="all_categories"><?=$post['all_categories']?></span></span>
                <div id="categories-list" class="mt-2">
                </div>
            </div>
        </div>

        <!-- Tags Selection -->
        <div class="mb-4">
            <label for="tags-search" class="form-label">Tags</label>
            <input type="text" class="form-control" id="tags-search" placeholder="Search for a tag" autocomplete="off">
            <ul id="tag-list" class="list-group mt-2"></ul>
            <div class="mt-1" style="font-size:10px"></div>
                <span class="fw-bold" style="font-size:10px">Previously Selected Tags: <span id="all_tags"><?=$post['all_tags']?></span></span>
            <div id="tags-list" class="mt-2"></div>
        </div>

        <!-- Media Upload -->
        <div class="mb-4">
            <label for="media" class="form-label">Upload Media (Reupload on next edit)</label>
            <input type="file" class="form-control" id="media" name="media[]" accept="image/*,video/*,audio/*" multiple>
            <small class="text-muted">You can upload images, videos, or audio files.</small>
            <div id="media-preview" class="row mt-3 gy-3">
                <?php if (!empty($post['media_url'])): ?>
                <!-- Carousel Container -->
                    <div id="imageCarouselContainer">
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

                    // JavaScript to remove the carousel when files are selected
                    document.getElementById('media').addEventListener('change', function () {
                        const carouselContainer = document.getElementById('imageCarouselContainer');
                        if (carouselContainer) {
                            carouselContainer.remove(); // Remove the existing carousel
                        }
                    });
                </script>
                <?php endif; ?>
            </div>
        </div>

        <!-- Scheduled Date for Publication -->
        <div class="mb-4">
            <label for="scheduled_at" class="form-label">Schedule for Publication</label>
            <input type="datetime-local" class="form-control" id="scheduled_at" name="scheduled_at" value="<?=$post['scheduled_at']?>" >
        </div>

        
        <!-- Hidden Fields -->
        <div id="selected-categories-tags">
            <input type="hidden" name="categories[]" id="categories-input" >
            <input type="hidden" name="tags[]" id="tags-input" >
        </div>

        <!-- Submit Buttons -->
        <div class="d-flex justify-content-end gap-2">
            <button type="submit" class="btn btn-primary" name="action" value="save">Save Post</button>
            <button type="submit" class="btn btn-success" name="action" value="publish">Publish Post</button>
        </div>
    </form>
</div>

<!-- jQuery and FontAwesome for "X" icon -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<script>
$(document).ready(function() {
    let categories = [];
    let tags = [];

    // Listen for input events to search for categories
    $('#category-search').on('input', function() {
        const query = $(this).val();
        if (query.length >= 1) {
            searchCategories(query);
            
        } else {
            $('#category-list').empty();
        }
    });

    function searchCategories(query) {
        $('#category-list').empty();
        $.ajax({
            url: 'http://localhost/IntegrativeProgramming/finals/BlogWebApp/api/get/categories/search?search=' + query,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                data.forEach(function(category) {
                    const listItem = $('<li>')
                        .addClass('list-group-item list-group-item-action')
                        .text(category.name)
                        .data('id', category.id)
                        .on('click', function() {
                            $('#category-list').empty();
                            addCategoryToList(category);
                            $('#category-search').val('');
                        });
                    $('#category-list').append(listItem);
                });
            },
            error: function(xhr, status, error) {
                console.error('Error fetching categories:', error);
            }
        });
    }

    function addCategoryToList(category) {
        if (!categories.some(cat => cat.id === category.id)) {
            categories.push({ id: category.id, name: category.name });
        }
        updateSelectedCategories();
    }
  
    function updateSelectedCategories() {
        $('#categories-list').empty();

        categories.forEach(function(category) {
            const box = $('<span>')
                .addClass('badge bg-light p-2 m-1 text-dark rounded-pill border border-secondary')
                .text(category.name)
                .append(
                    $('<i>')
                        .addClass('ms-1 fa-solid fa-x')
                        .on('click', function() {
                            removeCategoryFromList(category);
                        })
                );
            $('#categories-list').append(box);
        });
        $('#categories-input').val(JSON.stringify(categories)); 
    }

    function removeCategoryFromList(category) {
        categories = categories.filter(cat => cat.id !== category.id);
        updateSelectedCategories();
    }

    //Tags
    $('#tags-search').on('input', function() {
        const query = $(this).val();
        if (query.length >= 1) {
            searchTags(query);
        } else {
            $('#tag-list').empty();
        }
    });

    function searchTags(query) {
        $('#tag-list').empty();
        $.ajax({
            url: 'http://localhost/IntegrativeProgramming/finals/BlogWebApp/api/get/tags/search?search=' + query,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                data.forEach(function(tag) {
                    const listItem = $('<li>')
                        .addClass('list-group-item list-group-item-action')
                        .text(tag.name)
                        .data('id', tag.id)
                        .on('click', function() {
                            $('#tag-list').empty();
                            addTagToList(tag);
                            $('#tags-search').val('');
                        });
                    $('#tag-list').append(listItem);
                });
            },
            error: function(xhr, status, error) {
                console.error('Error fetching tags:', error);
            }
        });
    }

    function addTagToList(tag) {
        if (!tags.some(t => t.id === tag.id)) {
            tags.push({ id: tag.id, name: tag.name });
        }

        updateSelectedTags();        
    }

    function updateSelectedTags() {
        $('#tags-list').empty();

        tags.forEach(function(tag) {
            const box = $('<span>')
                .addClass('badge bg-light p-2 m-1 text-dark rounded-pill border border-secondary')
                .text(tag.name)
                .append(
                    $('<i>')
                        .addClass('ms-1 fa-solid fa-x')
                        .on('click', function() {
                            removeTagFromList(tag);
                        })
                );
            $('#tags-list').append(box);
        });
        $('#tags-input').val(JSON.stringify(tags)); // Update the hidden input with selected tags
    }

    function removeTagFromList(tag) {
        tags = tags.filter(t => t.id !== tag.id);
        updateSelectedTags();
        console.log(tags)
    }

});
</script>

<script>
    document.getElementById('media').addEventListener('change', function () {
        const previewContainer = document.getElementById('media-preview');
        previewContainer.innerHTML = ''; // Clear existing previews

        const files = Array.from(this.files);
        files.forEach(file => {
            const reader = new FileReader();
            const fileType = file.type.split('/')[0]; // Check if it's image, video, or audio

            reader.onload = function (event) {
                const colDiv = document.createElement('div');
                colDiv.classList.add('col-4', 'text-center');

                if (fileType === 'image') {
                    const img = document.createElement('img');
                    img.src = event.target.result;
                    img.alt = file.name;
                    img.classList.add('img-fluid', 'rounded', 'shadow');
                    colDiv.appendChild(img);
                } else if (fileType === 'video') {
                    const video = document.createElement('video');
                    video.src = event.target.result;
                    video.controls = true;
                    video.classList.add('w-100', 'rounded', 'shadow');
                    colDiv.appendChild(video);
                } else if (fileType === 'audio') {
                    const audio = document.createElement('audio');
                    audio.src = event.target.result;
                    audio.controls = true;
                    audio.classList.add('w-100', 'rounded', 'shadow');
                    colDiv.appendChild(audio);
                } else {
                    const fileName = document.createElement('p');
                    fileName.textContent = `Unsupported file: ${file.name}`;
                    colDiv.appendChild(fileName);
                }

                previewContainer.appendChild(colDiv);
            };

            reader.readAsDataURL(file); // Read file as Data URL for preview
        });
    });
</script>

<?php endif; ?>