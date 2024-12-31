
<!-- Sidebar Column -->
<div class="col-12 mb-5 row ">
<div class="col-md-3 mb-5 px-4">
    <div class="card p-4 shadow-lg border-0 rounded-3">
        <h4 class="mb-4 fw-bold text-center">Filters</h4>

        <form id="search-form" action="api/get/my-posts/search" method="GET">

            <!-- Keyword Search -->
            <div class="mb-4">
                <label for="keyword" class="form-label fw-semibold">Search by Keyword</label>
                <input 
                    type="text" 
                    class="form-control shadow-sm" 
                    id="keyword" 
                    name="keyword" 
                    placeholder="Type keywords here" 
                    value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>">
            </div>

            <!-- Category Filter -->
            <div class="mb-4">
                <label for="category-search" class="form-label fw-semibold">Filter by Category</label>
                <input 
                    type="text" 
                    class="form-control shadow-sm" 
                    id="category-search" 
                    placeholder="Search for categories" 
                    autocomplete="off">
                <ul id="category-list" class="list-group mt-2 shadow-sm"></ul>
                <div class="mt-2">
                    <strong>Selected Categories:</strong>
                    <div id="categories-list" class="badge-container mt-2"></div>
                </div>
            </div>

            <!-- Tag Filter -->
            <div class="mb-4">
                <label for="tags-search" class="form-label fw-semibold">Filter by Tags</label>
                <input 
                    type="text" 
                    class="form-control shadow-sm" 
                    id="tags-search" 
                    placeholder="Search for tags" 
                    autocomplete="off">
                <ul id="tag-list" class="list-group mt-2 shadow-sm"></ul>
                <div class="mt-2">
                    <strong>Selected Tags:</strong>
                    <div id="tags-list" class="badge-container mt-2"></div>
                </div>
            </div>

            <!-- Date Range Filter -->
            <div class="mb-4">
                <label for="date-from" class="form-label fw-semibold">Filter by Date Range</label>
                <div class="d-flex gap-2 flex-wrap">
                    <input 
                        type="date" 
                        class="form-control shadow-sm flex-grow-1" 
                        id="date-from" 
                        name="date_from" 
                        value="<?php echo isset($_GET['date_from']) ? htmlspecialchars($_GET['date_from']) : ''; ?>">
                    <span class="align-self-center">to</span>
                    <input 
                        type="date" 
                        class="form-control shadow-sm flex-grow-1" 
                        id="date-to" 
                        name="date_to" 
                        value="<?php echo isset($_GET['date_to']) ? htmlspecialchars($_GET['date_to']) : ''; ?>">
                </div>
            </div>

            <!-- Sort Options -->
            <div class="mb-4">
                <label for="sort-by" class="form-label fw-semibold">Sort By</label>
                <select 
                    id="sort-by" 
                    name="sort_by" 
                    class="form-control shadow-sm">
                    <option value="date">Date</option>
                    <option value="likes">Likes</option>
                </select>
            </div>

            <!-- Sort Order -->
            <div class="mb-4">
                <label for="sort-order" class="form-label fw-semibold">Order</label>
                <select 
                    id="sort-order" 
                    name="sort_order" 
                    class="form-control shadow-sm">
                    <option value="desc">Descending</option>
                    <option value="asc">Ascending</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div class="mb-4">
                <label for="status" class="form-label fw-semibold">Post Status</label>
                <select 
                    id="status" 
                    name="status" 
                    class="form-control shadow-sm">
                    <option value="">Any</option>
                    <option value="Draft">Draft</option>
                    <option value="Published">Published</option>
                </select>
            </div>

            <!-- Hidden Fields -->
            <div id="selected-categories-tags" class="d-none">
                <input type="hidden" name="categories[]" id="categories-input">
                <input type="hidden" name="tags[]" id="tags-input">
            </div>

            <!-- Search Button -->
            <div class="d-grid">
                <button type="submit" class="btn btn-primary shadow-sm">Apply Filters</button>
            </div>
        </form>
    </div>
</div>
<div class="col-md-8">
    <div class="content-section card border-0 shadow-lg p-4 rounded-3">
        <h2 class="text-center fw-bold mb-4">Your Blog Posts</h2>

        <!-- Display Posts -->
        <div id="results" class="row gy-4 px-2">
            <!-- Dynamic Post Items will be injected here -->
        </div>
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
                                <div class="card mb-3 d-flex flex-align-center mb-5 p-0">
                                    <div class="row g-0">
                                        <div class="col-md-4">
                                            <img src="${imagePath}" class="img-fluid rounded-start" alt="Thumbnail" style="width: 600px; height: 250px; object-fit: cover;">
                                        </div>
                                        <div class="col-md-8"  style="height: 250px; ">
                                            <div class="card-body d-flex flex-column justify-content-between" style="height: 100%;">
                                                <div>
                                                    <h5 class="card-title">
                                                        ${item.title.length > 50 ? item.title.substring(0, 50) + '...' : item.title}
                                                    </h5>
                                                    <p class="card-text">
                                                        ${item.content.length > 50 ? item.content.substring(0, 150) + '...' : item.content}
                                                    </p>
                                                    <p class="card-text m-0">
                                                        <small class="text-muted">Last updated: ${item.updated_at}</small>
                                                    </p>
                                                    <p class="card-text m-0">
                                                        <small>Likes: ${item.likes}</small>
                                                    </p>
                                                    <small>
                                                        Status:
                                                        <span class="badge ${item.status === 'published' ? 'bg-success' : 'bg-warning text-dark'}">
                                                            ${item.status.charAt(0).toUpperCase() + item.status.slice(1)}
                                                        </span>
                                                    </small>
                                                </div>
                                                <div class="d-flex justify-content-end align-items-center gap-2 mt-3">
                                                    <a href="view?id=${item.id}" class="btn btn-primary btn-m d-flex align-items-center justify-content-center" target="_blank" r>
                                                        Read
                                                    </a>
                                                    <a href="delete-post?id=${item.id}" class="btn btn-danger btn-m d-flex align-items-center justify-content-center" target="_blank">
                                                        <i class="fas fa-trash me-1"></i> Delete
                                                    </a>
                                                    <a href="edit-post?id=${item.id}" class="btn btn-success btn-m d-flex align-items-center justify-content-center" target="_blank" >
                                                        <i class="fas fa-edit me-1"></i> Edit
                                                    </a>
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