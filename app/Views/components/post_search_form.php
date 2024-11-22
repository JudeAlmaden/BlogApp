
<!-- Sidebar Column -->
<div class="col-md-3">
    <div class="card p-4 shadow-sm">
        <h4 class="mb-4">Search Blog Posts</h4>
        <form id="search-form" action="search-posts" method="GET">
            
            <!-- Keyword Search Field -->
            <div class="mb-4">
                <label for="keyword" class="form-label">Keyword</label>
                <input type="text" class="form-control" id="keyword" name="keyword" placeholder="Enter keywords" value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>">
            </div>

            <!-- Category Filter -->
            <div class="mb-4">
            <label for="category-search" class="form-label">Category</label>
            <input type="text" class="form-control" id="category-search" placeholder="Search for a category" autocomplete="off">
            <ul id="category-list" class="list-group mt-2"></ul>
            <div class="mt-1" style="font-size:10px">
                <span class="fw-bold">Selected Categories:</span>
                <div id="categories-list" class="mt-2"></div>
            </div>
        </div>

        <!-- Tags Selection -->
        <div class="mb-4">
            <label for="tags-search" class="form-label">Tags</label>
            <input type="text" class="form-control" id="tags-search" placeholder="Search for a tag" autocomplete="off">
            <ul id="tag-list" class="list-group" style="font-size:40"></ul>
            <div class="mt-1" style="font-size:10px">
                <span class="fw-bold">Selected Tags:</span>
                <div id="tags-list" class="mt-2"></div>
            </div>
        </div>

        <div class="col-12 px-5"><hr></div>

        <!-- Date Range Filter -->
        <div class="mb-4">
            <label for="date-from" class="form-label">Date Range</label>
            <div class="d-flex gap-2 flex-wrap">
                <input type="date" class="form-control flex-grow-1 " id="date-from" name="date_from" value="<?php echo isset($_GET['date_from']) ? htmlspecialchars($_GET['date_from']) : ''; ?>">
                <span class="text-center d-flex align-items-center text-center col-xs-12">to</span>
                <input type="date" class="form-control flex-grow-1 " id="date-to" name="date_to" value="<?php echo isset($_GET['date_to']) ? htmlspecialchars($_GET['date_to']) : ''; ?>">
            </div>
        </div>

        <div id="selected-categories-tags">
            <input type="hidden" name="categories[]" id="categories-input">
            <input type="hidden" name="tags[]" id="tags-input">
        </div>

        <!-- Submit Button -->
        <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-primary" id="search-btn">Search</button>
        </div>
        </form>
    </div>
</div>

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
            $('#tags-list').empty();
        }
    });

    function searchTags(query) {
        $('#tags-list').empty();
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


    // Handle the search form submission with AJAX
    $('#search-btn').on('click', function() {
        var formData = $('#search-form').serialize(); // Get the form data
        
        // Send AJAX request
        $.ajax({
            url: $('#search-form').attr('action'), // Form's action URL
            method: 'GET',
            data: formData,
            success: function(response) {
                // Handle the successful response
                console.log(response); // You can display the results in the UI or handle it as needed
                // Example: $('#results-container').html(response); // Update the results container
            },
            error: function(xhr, status, error) {
                // Handle the error response
                alert('An error occurred: ' + error);
            }
        });
    });
});
</script>