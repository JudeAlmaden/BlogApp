
<!-- Sidebar Column -->
<div class="col-12 mb-5 row">
<div class="col-md-3 mb-5">
    <div class="card p-4 shadow-sm" style="z-index: 10; ">
        <h4 class="mb-4"> Filters</h4>
        <form id="search-form" action="api/get/my-posts/search" method="GET">
            
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

        <div class="col-12 px-5"><hr></div>

        <div class="mb-3">
            <div class="form-group">
                <label for="sort-by" class="form-label">Sort By:</label>
                <select id="sort-by" name="sort_by" class="form-control">
                    <option value="date">Date</option>
                    <option value="likes">Likes</option>
                </select>
            </div>

            <div class="form-group mt-2">
                <label for="sort-order" class="form-label">Order:</label>
                <select id="sort-order" name="sort_order" class="form-control">
                    <option value="desc">Descending</option>
                    <option value="asc">Ascending</option>
                </select>
            </div>

            
            <div class="form-group mt-2">
                <label for="sort-order" class="form-label">Status:</label>
                <select id="sort-order" name="status" class="form-control">
                    <option value="">Any</option>
                    <option value="Draft">Draft</option>
                    <option value="Published">Published</option>
                </select>
            </div>
        </div>
        
        <!-- Submit Button -->
        <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-primary" id="search-btn">Search</button>
        </div>
        </form>
    </div>
</div>
    <div class="col-8">
        <h2 class="text-center">Here are your posts</h2>
        <div class="col-12"  id="results">
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    let categories = []; // Array to store selected categories
    let tags = []; // Array to store selected tags
    let offset = 0;
    let reachedEnd = false;
    let isQuerying = false;

    // Listen for input events to search for categories
    $('#category-search').on('input', function () {
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
            url: 'http://localhost/IntegrativeProgramming/finals/BlogWebApp/api/get/categories?=' + query,
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                data.forEach(function (category) {
                    const listItem = $('<li>')
                        .addClass('list-group-item list-group-item-action')
                        .text(category.name)
                        .on('click', function () {
                            $('#category-list').empty();
                            addCategoryToList(category.name);
                            $('#category-search').val('');
                        });
                    $('#category-list').append(listItem);
                });
            },
            error: function (xhr, status, error) {
                console.error('Error fetching categories:', error);
            }
        });
    }

    function addCategoryToList(categoryName) {
        if (!categories.includes(categoryName)) {
            categories.push(categoryName);
        }
        updateSelectedCategories();
    }

    function updateSelectedCategories() {
        $('#categories-list').empty();
        categories.forEach(function (category) {
            const box = $('<span>')
                .addClass('badge bg-light p-2 m-1 text-dark rounded-pill border border-secondary')
                .text(category)
                .append(
                    $('<i>')
                        .addClass('ms-1 fa-solid fa-x')
                        .on('click', function () {
                            removeCategoryFromList(category);
                        })
                );
            $('#categories-list').append(box);
        });
        $('#categories-input').val(JSON.stringify(categories)); // Update the hidden input with selected categories
    }

    function removeCategoryFromList(categoryName) {
        categories = categories.filter(cat => cat !== categoryName);
        updateSelectedCategories();
    }

    // Tags
    $('#tags-search').on('input', function () {
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
            success: function (data) {
                data.forEach(function (tag) {
                    const listItem = $('<li>')
                        .addClass('list-group-item list-group-item-action')
                        .text(tag.name)
                        .on('click', function () {
                            $('#tag-list').empty();
                            addTagToList(tag.name);
                            $('#tags-search').val('');
                        });
                    $('#tag-list').append(listItem);
                });
            },
            error: function (xhr, status, error) {
                console.error('Error fetching tags:', error);
            }
        });
    }

    function addTagToList(tagName) {
        if (!tags.includes(tagName)) {
            tags.push(tagName);
        }
        updateSelectedTags();
    }

    function updateSelectedTags() {
        $('#tags-list').empty();
        tags.forEach(function (tag) {
            const box = $('<span>')
                .addClass('badge bg-light p-2 m-1 text-dark rounded-pill border border-secondary')
                .text(tag)
                .append(
                    $('<i>')
                        .addClass('ms-1 fa-solid fa-x')
                        .on('click', function () {
                            removeTagFromList(tag);
                        })
                );
            $('#tags-list').append(box);
        });
        $('#tags-input').val(JSON.stringify(tags)); // Update the hidden input with selected tags
    }

    function removeTagFromList(tagName) {
        tags = tags.filter(t => t !== tagName);
        updateSelectedTags();
    }

    // Reusable AJAX query function
    function fetchSearchResults() {
        const formData = $('#search-form').serialize() + `&offset=${offset}`; // Include offset in the form data

        if (!reachedEnd) {
        // Add the spinner to the end of the results
        const spinner = `
            <div class="d-flex justify-content-center my-4" id="search-spinner">
                <div class="spinner-border" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        `;

        $('#results').append(spinner); // Append spinner to results

        if(!isQuerying){
            $.ajax({
                url: $('#search-form').attr('action'), // Form's action URL
                method: 'GET',
                data: formData,
                complete: function () {
                    // Remove spinner when AJAX completes, regardless of success or error
                    $('#search-spinner').remove();
                    isQuerying=false;
                },
                success: function (response) {
                    const data = response;

                    if (data.length === 0) {
                        const resultCard = `
                            <div class="my-3 mb-2 fs-4 text-center">
                                Seems like that's everything
                            </div>`;
                        $('#results').append(resultCard);

                        reachedEnd = true; // Mark as end reached
                        return;
                    }

                    // Populate results
                    data.forEach(function (item) {
                        const imagePath = item.file_path
                            ? item.file_path
                            : "https://via.placeholder.com/600x250"; // Default image if file_path is empty
                        
                            const resultCard = `
                                <div class="card mb-3 d-flex flex-align-center mb-5">
                                    <div class="row g-0">
                                        <div class="col-md-4">
                                            <img src="${imagePath}" class="img-fluid rounded-start" alt="Thumbnail" style="width: 600px; height: 250px; object-fit: cover;">
                                        </div>
                                        <div class="col-md-8"  style="height: 250px; ">
                                            <div class="card-body">
                                                <h5 class="card-title">${item.content.length > 30 ? item.content.substring(0, 20) + '...' : item.title}</h5>
                                                <p class="card-text">${item.content.length > 50 ? item.content.substring(0, 150) + '...' : item.content}</p>
                                                <p class="card-text m-0"><small class="text-muted"> Last updated: ${item.updated_at}</small></p>
                                                <p class="card-text m-0"><small>Likes: ${item.likes}</small></p>
                                                <small>
                                                    Status: 
                                                    <span class="badge ${item.status === 'published' ? 'bg-success' : 'bg-warning text-dark'}">
                                                        ${item.status.charAt(0).toUpperCase() + item.status.slice(1)}
                                                    </span>
                                                </small>
                                                <div class="d-flex justify-content-end">
                                                    <a href="delete-post?id=${item.id}" class="btn btn-danger col-3 me-1" target="_blank" ><i class="fas fa-trash me-2"></i>Delete</a>
                                                    <a href="edit-post?id=${item.id}" class="btn btn-success col-3" target="_blank" rel="noopener noreferrer"><i class="fas fa-edit me-2"></i>Edit</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>`;
                        $('#results').append(resultCard);
                    });
                },
                error: function (xhr, status, error) {
                    alert('An error occurred: ' + error);
                }
            });
            }
        }
    }

    // Fetch results on page load
    fetchSearchResults();

    // Fetch results when search button is clicked
    $('#search-btn').on('click', function () {
        reachedEnd = false;
        offset = 0

        $('#results').empty();
        fetchSearchResults();
    });

    //More when document reaches end
    $(window).on('scroll', function () {
    if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
        offset += 1;
        fetchSearchResults();
        }
    });
});


</script>