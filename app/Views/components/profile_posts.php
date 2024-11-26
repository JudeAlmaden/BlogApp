<div class="col-md-9 mb-5 p-5">
    <div class="text-center fs-1"><?php echo htmlspecialchars($user['name']); ?>'s Posts</div>
    <div id="results"></div>
</div>


<script>
$(document).ready(function () {
    let offset = 0;
    let reachedEnd = false;
    let isQuerying = false;
 
    // Reusable AJAX query function
    function fetchSearchResults() {
       
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
                url: 'api/get/search-posts', // Form's action URL
                method: 'GET',
                data: {
                        user_id: <?=$user['id']?>,
                        offset:offset
                    },
                complete: function () {
                    $('#search-spinner').remove();
                    isQuerying = false;
                },
                success: function (response) {
                    const data = response;
                    console.log(data.length);
                    if ((data.length === 0) && !reachedEnd) {
                        const resultCard = `
                            <div class="my-3 mb-2 fs-4 text-center">
                                Seems like that's everything
                            </div>`;
                        $('#results').append(resultCard);

                        reachedEnd = true; // Mark as end reached
                        return;
                    }
                    offset++;
                    // Populate results
                    data.forEach(function (item) {
                        const imagePath = item.file_path
                            ? item.file_path
                            : "https://via.placeholder.com/600x250"
                        
                            const resultCard = `
                                <div class="card mb-3 d-flex flex-align-center mb-2">
                                    <div class="row g-0">
                                        <div class="col-md-4">
                                            <img src="${imagePath}" class="img-fluid rounded-start" alt="Thumbnail" style="width: 600px; height: 250px; object-fit: cover;">
                                        </div>
                                        <div class="col-md-8"  style="height: 250px; ">
                                            <div class="card-body">
                                                <h5 class="card-title">${item.content.length > 30 ? item.content.substring(0, 20) + '...' : item.title}</h5>
                                                <p class="card-text">${item.content.length > 50 ? item.content.substring(0, 150) + '...' : item.content}</p>
                                                <p class="card-text m-0"><small class="text-muted">Author: ${item.name}</small></p>
                                                <p class="card-text m-0"><small class="text-muted">Last updated: ${item.updated_at}</small></p>
                                                <p class="card-text m-0"><small>Likes: ${item.likes}</small></p>
                                                <p class="card-text my-0"><small>Tags: ${item.all_tags}</small></p>
                                                <p class="card-text my-0"><small>Categories: ${item.all_categories}</small></p>
                                                <div class="d-flex justify-content-end">
                                                    <a href="view?id=${item.id}" class="btn btn-primary col-3" target="_blank" rel="noopener noreferrer">Read</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>`;
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
    }

    // Fetch results on page load
    fetchSearchResults();

    //More when document reaches end
    $(window).on('scroll', function () {
    if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
        offset += 1;
        fetchSearchResults();
        }
    });
});


</script>