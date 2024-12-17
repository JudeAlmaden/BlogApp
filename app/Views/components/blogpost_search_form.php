
<!-- Sidebar Column -->    
<div class="col-12 mb-5 row">
<div class="col-md-3 mb-5 px-5">
<div class="card p-4 shadow-sm border-0 rounded-3">
    <h4 class="mb-4 fw-bold text-primary">Search Blog Posts</h4>
    <form id="search-form" action="#" method="GET">
        
        <!-- Keyword Search Field -->
        <div class="mb-4">
            <label for="keyword" class="form-label fw-semibold">Keyword</label>
            <input 
                type="text" 
                class="form-control" 
                id="keyword" 
                name="keyword" 
                placeholder="Enter keywords"
                value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>"
            >
        </div>

        <!-- Author Search -->
        <div class="mb-4">
            <label for="author" class="form-label fw-semibold">Author</label>
            <input 
                type="text" 
                class="form-control" 
                id="author" 
                name="author" 
                placeholder="Enter author's name"
                value="<?php echo isset($_GET['author']) ? htmlspecialchars($_GET['author']) : ''; ?>"
            >
        </div>

        <!-- Category Filter -->
        <div class="mb-4">
            <label for="category-search" class="form-label fw-semibold">Category</label>
            <input 
                type="text" 
                class="form-control" 
                id="category-search" 
                placeholder="Search for a category" 
                autocomplete="off"
            >
            <ul id="category-list" class="list-group mt-2"></ul>
            <div class="mt-2 small">
                <span class="fw-bold">Selected Categories:</span>
                <div id="categories-list"></div>
            </div>
        </div>

        <!-- Tags Selection -->
        <div class="mb-4">
            <label for="tags-search" class="form-label fw-semibold">Tags</label>
            <input 
                type="text" 
                class="form-control" 
                id="tags-search" 
                placeholder="Search for a tag" 
                autocomplete="off"
            >
            <ul id="tag-list" class="list-group mt-2"></ul>
            <div class="mt-2 small">
                <span class="fw-bold">Selected Tags:</span>
                <div id="tags-list"></div>
            </div>
        </div>

        <hr class="my-4">

        <!-- Date Range Filter -->
        <div class="mb-4">
            <label class="form-label fw-semibold">Date Range</label>
            <div class="d-flex gap-2 align-items-center">
                <input 
                    type="date" 
                    class="form-control form-control-sm w-50" 
                    id="date-from" 
                    name="date_from"
                    value="<?php echo isset($_GET['date_from']) ? htmlspecialchars($_GET['date_from']) : ''; ?>"
                >
                <span class="small">to</span>
                <input 
                    type="date" 
                    class="form-control form-control-sm w-50" 
                    id="date-to" 
                    name="date_to"
                    value="<?php echo isset($_GET['date_to']) ? htmlspecialchars($_GET['date_to']) : ''; ?>"
                >
            </div>
        </div>

        <hr class="my-4">

        <!-- Sorting Options -->
        <div class="mb-4">
            <label for="sort-by" class="form-label fw-semibold">Sort By</label>
            <select id="sort-by" name="sort_by" class="form-select">
                <option value="date">Date</option>
                <option value="likes">Likes</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="sort-order" class="form-label fw-semibold">Order</label>
            <select id="sort-order" name="sort_order" class="form-select">
                <option value="desc">Descending</option>
                <option value="asc">Ascending</option>
            </select>
        </div>

        <!-- Search Button -->
        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>
</div>

</div>
<div class="col-md-8 border-start">
    <div class="content-section p-4">
        <h2 class="text-center fw-bold mb-4">Your Blog Posts</h2>

        <!-- Display Posts -->
        <div id="results" class="row gy-4 px-2">
            <!-- Dynamic Post Items will be injected here -->
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
            url: 'http://localhost/IntegrativeProgramming/finals/BlogWebApp/api/get/categories/search?search=' + query,
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
                url: "api/get/search-posts", // Form's action URL
                method: 'GET',
                data: formData,
                complete: function () {
                    $('#search-spinner').remove();
                    isQuerying = false;
                },
                success: function (response) {
                    const data = response;
                    
                    if ((data.length === 0) && !reachedEnd) {
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
                            : ""; // Removed placeholder image for posts without images

                        // Profile image with fallback
                        const profileImage = item.profile 
                            ? item.profile 
                            : "https://i.pinimg.com/originals/c0/27/be/c027bec07c2dc08b9df60921dfd539bd.webp";  // Placeholder image for profile

                        // Build the result card dynamically
                        const resultCard = `
                        <div class="card shadow-lg rounded-4 overflow-hidden mb-4" style="border: none; background-color: #f9f9f9;">
                            <div class="row g-0">
                                <!-- Left Column: Post Image (only if there's an image) -->
                                ${imagePath ? `
                                <div class="col-md-4 d-flex justify-content-center align-items-center">
                                    <img src="${imagePath}" class="img-fluid rounded-start" alt="Post Thumbnail" 
                                        style="width: 100%; height: 250px; object-fit: cover;">
                                </div>
                                ` : ''}

                                <!-- Right Column: Post Details -->
                                <div class=" d-flex flex-column p-3">
                                    <div class="card-body d-flex flex-column justify-content-between" style="height: 100%;">

                                        <!-- Post Title -->
                                        <h5 class="card-title mb-3" style="font-size: 1.75rem; font-weight: bold; color: #333;">
                                            ${item.title.length > 60 ? item.title.substring(0, 60) + '...' : item.title}
                                        </h5>

                                        <!-- Post Content Preview -->
                                        <p class="card-text mb-3" style="color: #555; font-size: 1rem; line-height: 1.6;">
                                            ${item.content.length > 250 ? item.content.substring(0, 250) + '...' : item.content}
                                        </p>

                                        <!-- Author Info & Date -->
                                        <div class="d-flex justify-content-between align-items-center mb-3" style="font-size: 0.9rem; color: #888;">
                                            <!-- Author -->
                                            <div class="d-flex align-items-center">
                                                <img src="${profileImage}" class="rounded-circle" alt="Profile" 
                                                    style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;">
                                                <a href="view-profile?id=${item.user_id}" 
                                                style="text-decoration: none; font-weight: bold; color: #007bff;">
                                                    ${item.name}
                                                </a>
                                            </div>
                                            
                                            <!-- Post Date -->
                                            <small>${item.published_at}</small>
                                        </div>
                                        
                                        <!-- Post Stats (Likes, Tags, Categories) -->
                                        <div class="d-flex justify-content-between align-items-center mb-3" style="font-size: 0.9rem; color: #888;">
                                            <div class="d-flex align-items-center">
                                                <span class="badge badge-light text-muted" style="font-size: 0.9rem; margin-right: 8px;">
                                                    <i class="fas fa-thumbs-up"></i> ${item.likes}
                                                </span>
                                                <span class="badge badge-light text-muted" style="font-size: 0.9rem; margin-right: 8px;">
                                                    <i class="fas fa-tag"></i> ${item.all_tags}
                                                </span>
                                                <span class="badge badge-light text-muted" style="font-size: 0.9rem;">
                                                    <i class="fas fa-folder"></i> ${item.all_categories}
                                                </span>
                                            </div>
                                            
                                            <!-- Read More Button -->
                                            <a href="view?id=${item.id}" class="btn btn-outline-primary btn-sm" 
                                            target="_blank" 
                                            style="font-weight: bold; padding: 5px 15px; border-radius: 20px; transition: all 0.3s;">
                                                Read More
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        `;

                        // Append the result card to the results container
                        $('#results').append(resultCard);
                    });                },
                error: function (xhr, status, error) {
                    console.error('Error fetching results:', error);
                    const errorCard = `
                        <div class="alert alert-danger">
                            Unable to load results. Please try again later.
                        </div>`;
                    $('#results').append(errorCard);
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
        isQuerying=true;
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