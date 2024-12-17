<div class="col-12">
    <div class="content-section card border-0 shadow-lg p-4 rounded-3">
        <h2 class="text-center fw-bold mb-4" style="font-size: 2rem; color: #333; text-transform: uppercase; letter-spacing: 2px; background: linear-gradient(to right, #8a2be2, #9400d3); -webkit-background-clip: text; color: transparent;">
            Featured Blogposts of the Day
        </h2>

        <!-- Display Posts -->
        <div id="results" class="container d-flex flex-wrap justify-content-center align-items-start gy-4 px-2 border-start">
            <!-- Dynamic Post Items will be injected here -->
        </div>
    </div>
</div>

<!-- Include CSS for styling -->
<style>
    .card {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .card img {
        border-radius: 8px;
    }
    
    .card-body {
        padding: 15px;
    }
    
    .card-body h5 {
        font-size: 1.25rem;
        font-weight: 700;
    }

    .card-body p {
        font-size: 0.9rem;
        color: #666;
    }

    .card-body .card-text small {
        font-size: 0.8rem;
        color: #aaa;
    }

    .card-body .card-title {
        font-size: 1.2rem;
        font-weight: bold;
        margin-bottom: 0.8rem;
    }

    .card-footer {
        background: none;
        border: none;
    }

    .btn-primary {
        background-color: #1877f2; /* Facebook Blue */
        border-color: #1877f2;
        border-radius: 20px;
        font-weight: bold;
        padding: 8px 20px;
        text-align: center;
    }

    .btn-primary:hover {
        background-color: #165eab; /* Darker Blue */
        border-color: #165eab;
    }

    .card-footer a {
        font-size: 0.9rem;
        text-decoration: none;
        color: #1877f2;
    }

    .card-footer a:hover {
        text-decoration: underline;
    }

    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }
</style>

<script>
$(document).ready(function () {
    let categories = []; // Array to store selected categories
    let tags = []; // Array to store selected tags
    let offset = 0;
    let reachedEnd = false;
    let isQuerying = false;
    const today = new Date().toISOString().split('T')[0];

    // Reusable AJAX query function
    function fetchSearchResults() {
        const formDataNull = {
            keyword: "",
            author: "",
            category: null,
            tags: null,
            date_from: today,
            date_to: null,
            sort_by: "like",
            sort_order: "desc",
            offset: offset // Include offset in the form data
        };

        if (!reachedEnd && !isQuerying) {
            isQuerying = true;

            // Add the spinner to the end of the results
            const spinner = `
                <div class="d-flex justify-content-center my-4" id="search-spinner">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            `;
            $('#results').append(spinner); // Append spinner to results

            $.ajax({
                url:"api/get/search-posts", // Form's action URL
                method: 'GET',
                data: formDataNull, // Sending the null values along with offset
                complete: function () {
                    $('#search-spinner').remove();
                    isQuerying = false;
                },
                success: function (response) {
                    const data = response;

                    if (data.length === 0 && !reachedEnd) {
                        const resultCard = `
                                <div class="my-5 py-4 text-center" >
                                    <h3 class="text-muted" style="font-size: 1.75rem; font-weight: 600; color: #6c757d;">
                                        Seems like that's everything!
                                    </h3>
                                    <p class="text-muted" style="font-size: 1.2rem; color: #6c757d;">
                                        There are no more posts to show at the moment. Check back later for new content!
                                    </p>
                                </div>
                            `;

                            $('#results').append(resultCard);


                        reachedEnd = true; // Mark as end reached
                        return;
                    }

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
                        <div class="card shadow-lg rounded-4 overflow-hidden mb-4 col-7" style="border: none; background-color: #f9f9f9;">
                            <div class="row g-0">
                                <!-- Left Column: Post Image (only if there's an image) -->
                                ${imagePath ? `
                                <div class="col-md-12 d-flex justify-content-center align-items-center">
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
                    });
                },
                
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

    // Fetch results on page load
    fetchSearchResults();

    // More results when scrolling reaches bottom of page
    $(window).on('scroll', function () {
        if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
            if (!isQuerying && !reachedEnd) {
                offset += 1;
                fetchSearchResults();
            }
        }
    });
});
</script>
