<div class="container py-5">

    <form id="category-form" action="create-post" method="POST" enctype="multipart/form-data" class="card p-5 shadow-lg border-0 rounded-4">
    <h1 class="text-center mb-5" style="font-size: 2.5rem; font-weight: bold; color: #333;">Create Blog Post</h1>        
        <!-- Title Field -->
        <div class="mb-4">
            <label for="title" class="form-label fw-semibold" style="font-size: 1.1rem;">Post Title</label>
            <input 
                type="text" 
                class="form-control shadow-sm" 
                id="title" 
                name="title" 
                placeholder="Enter your post title here" 
                required 
                maxlength="100" 
                style="font-size: 1rem; border-radius: 10px;">
        </div>

        <!-- Content Field -->
        <div class="mb-4">
            <label for="content" class="form-label fw-semibold" style="font-size: 1.1rem;">Post Content</label>
            <textarea 
                class="form-control shadow-sm" 
                id="content" 
                name="content" 
                rows="8" 
                placeholder="Write your content here..." 
                required
                style="font-size: 1rem; border-radius: 10px;"></textarea>
        </div>

        <!-- Category Selection -->
        <div class="mb-4">
            <label for="category-search" class="form-label fw-semibold" style="font-size: 1.1rem;">Select Categories</label>
            <input 
                type="text" 
                class="form-control shadow-sm" 
                id="category-search" 
                placeholder="Search and add categories" 
                autocomplete="off"
                style="font-size: 1rem; border-radius: 10px;">
            <ul id="category-list" class="list-group mt-2 shadow-sm"></ul>
            <div class="mt-3">
                <strong>Selected Categories:</strong>
                <div id="categories-list" class="mt-2 badge-container"></div>
            </div>
        </div>

        <!-- Tags Selection -->
        <div class="mb-4">
            <label for="tags-search" class="form-label fw-semibold" style="font-size: 1.1rem;">Add Tags</label>
            <input 
                type="text" 
                class="form-control shadow-sm" 
                id="tags-search" 
                placeholder="Search and add tags" 
                autocomplete="off"
                style="font-size: 1rem; border-radius: 10px;">
            <ul id="tag-list" class="list-group mt-2 shadow-sm"></ul>
            <div class="mt-3">
                <strong>Selected Tags:</strong>
                <div id="tags-list" class="mt-2 badge-container"></div>
            </div>
        </div>

        <!-- Media Upload -->
        <div class="mb-4">
            <label for="media" class="form-label fw-semibold" style="font-size: 1.1rem;">Upload Media</label>
            <input 
                type="file" 
                class="form-control shadow-sm" 
                id="media" 
                name="media[]" 
                accept="image/*,video/*,audio/*" 
                multiple
                style="font-size: 1rem; border-radius: 10px;">
            <small class="text-muted">You can upload images, videos, or audio files. These will need to be reuploaded if editing.</small>
            <div id="media-preview" class="row mt-3 gy-3">
                <!-- Media previews will appear here -->
            </div>
        </div>

        <!-- Scheduled Date for Publication -->
        <div class="mb-4">
            <label for="scheduled_at" class="form-label fw-semibold" style="font-size: 1.1rem;">Schedule Post</label>
            <input 
                type="datetime-local" 
                class="form-control shadow-sm" 
                id="scheduled_at" 
                name="scheduled_at"
                style="font-size: 1rem; border-radius: 10px;">
        </div>

        <!-- Hidden Fields for Categories and Tags -->
        <div id="selected-categories-tags" class="d-none">
            <input type="hidden" name="categories[]" id="categories-input">
            <input type="hidden" name="tags[]" id="tags-input">
        </div>

        <!-- Submit Buttons -->
        <div class="d-flex justify-content-end gap-3 mt-4">
            <button type="submit" class="btn btn-outline-primary shadow-sm px-4 py-2" name="action" value="save" style="border-radius: 20px; font-weight: bold; transition: all 0.3s;">Save as Draft</button>
            <button type="submit" class="btn btn-success shadow-sm px-4 py-2" name="action" value="publish" style="border-radius: 20px; font-weight: bold; transition: all 0.3s;">Publish</button>
        </div>

        <!-- Disclaimer -->
        <div class="mt-3 text-muted text-end">
            <small><em>Note: Categories, tags, and uploads will not be saved until the post is published or saved as a draft.</em></small>
        </div>
    </form>
</div>

<!-- jQuery and FontAwesome for "X" icon -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<script>
$(document).ready(function() {
    let categories = [];  // Array to store selected categories
    let tags = [];  // Array to store selected tags

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

    // Remove tag from the list
    function removeTagFromList(tag) {
        tags = tags.filter(t => t.id !== tag.id);
        updateSelectedTags();
        console.log(tags)
    }

    $('form').on('submit', function(event) {
        let categories = $('#categories-input').val(); // Get the value of the categories input
        let tags = $('#tags-input').val(); // Get the value of the tags input

        // If either categories or tags are empty, prevent the form submission
        if (!categories || !tags || categories === "[]" || tags === "[]") {
            event.preventDefault();  // Prevent form submission

            // Display a message to the user (optional)
            alert('Please select at least one category and one tag.');
        }
    });
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