<div class="container mt-4">
    <!-- Comment Section -->
     
    <h4 class="mb-3">Comments</h4>
    <!-- Comment Form -->
    <div class="mb-3 p-4 border rounded shadow-sm bg-light">
        <h5 class="mb-4">Leave a Comment</h5>
        <form id="commentForm" action="#" method="POST">
            <div class="d-flex align-items-center mb-3">
                <!-- User Avatar -->
                <img src="<?=$user['profile_image']?>" alt="User Avatar" class="rounded-circle me-3" style="width: 50px; height: 50px;">
                
                <!-- User Info -->
                <div>
                    <h6 class="mb-0"><?=$user['name']?></h6>
                    <small class="text-muted">You are commenting as <?=$user['name']?></small>
                </div>
            </div>

            <!-- Comment Input Section -->
            <div class="mb-3">
                <label for="comment" class="form-label">Comment</label>
                <textarea id="comment" class="form-control" rows="3" placeholder="Write your comment here" name="comment" required></textarea>
            </div>

            <input type="hidden" name="id" value="<?= htmlspecialchars($post['id'], ENT_QUOTES) ?>">
            
            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary mb-3">Submit</button>
        </form>

        <!-- Response Message Section -->
        <div id="responseMessage"></div>


        <!-- Optional: For better styling or effects, consider adding success/failure alert styles -->
        <style>
            .p-4 {
                background-color: #f9f9f9;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }

            .btn-primary {
                width: 100%;
            }

            #responseMessage {
                margin-top: 20px;
            }

            .alert {
                font-size: 14px;
            }


        .comment {
            background-color: #f9f9f9;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .btn-link {
            color: #007bff;
            font-size: 14px;
        }

        .btn-link:hover {
            text-decoration: underline;
        }

        .replies {
            margin-left: 20px;
        }

        .replyForm {
            display: flex;
            flex-direction: column;
        }

        .card-body {
            padding: 15px;
            border-radius: 8px;
            background-color: #f1f1f1;
        }

        .replyForm textarea {
            resize: none;
            margin-bottom: 10px;
        }

        .btn-secondary {
            align-self: flex-start;
        }
        </style>

        <div id="commentsContainer" class="row">    <!-- Comments Here  --></div>
    </div>
</div>

<script>
$(document).ready(function() {
    let index = 0;
    canLoadMore=true;

    function fetchSearchResults() {
        $.ajax({
            url: "api/get/comment",  // API URL to fetch comments
            method: 'GET',
            data: { 
                id: <?= $post['id']; ?>, // Send the current post ID
                index: index, // Send the current index for pagination
            },
            success: function(response) {
                console.log(response);
                if (response.status === 'success') {
                    // Add the fetched comments to the page (you can adjust this as needed)
                    response.data.forEach(function(comment) {
                        // Append each comment in the provided format
                        $('#commentsContainer').append(
                            `<div class="comment mb-4 p-4 border rounded shadow-sm col-12">
                                <div class="d-flex align-items-center mb-3">
                                    <!-- User Avatar -->
                                    <img src="${comment.profile_image || 'https://via.placeholder.com/50'}" alt="User Avatar" class="rounded-circle me-3" style="width: 50px; height: 50px;">
                                    
                                    <!-- User Info -->
                                    <div>
                                        <h6 class="mb-0">${comment.name}</h6>
                                        <small class="text-muted">Posted on ${comment.created_at}</small>
                                    </div>
                                </div>

                                <!-- Comment Content -->
                                <p class="mb-0">${comment.content}</p>

                                <!-- Reply Button -->
                                <div class="d-flex mt-2">
                                    <button 
                                        class="btn btn-link text-decoration-none me-2 fetch-replies-btn" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#replySection${comment.id}" 
                                        aria-expanded="false" 
                                        aria-controls="replySection${comment.id}" 
                                        data-comment-id="${comment.id}">
                                        Replies (${comment.responses})
                                    </button>
                                    <button class="btn btn-link text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#replySection${comment.id}-reply" aria-expanded="false" aria-controls="replySection${comment.id}-reply">
                                        Reply
                                    </button>
                                </div>
                                
                                <hr>

                                <!-- Replies Section -->
                                <div class="collapse mt-3" id="replySection${comment.id}">
                                    <div class="replies mt-4 ps-4 border-start border-dark border-3" id="replies">
                                        <!-- Here, the replies will be dynamically inserted -->
                                    </div>
                                </div>


                                <!-- Reply Form Section -->
                                <div class="collapse mt-3" id="replySection${comment.id}-reply">
                                    <div class="card card-body p-3">
                                        <form data-comment-id="${comment.id}" class="replyForm">
                                            <div class="d-flex align-items-center mb-3">
                                                <img src="<?=$user['profile_image']?>" alt="User Avatar" class="rounded-circle me-3" style="width: 50px; height: 50px;">
                                                    <!-- User Info -->
                                                    <div>
                                                        <h6 class="mb-0"><?=$user['name']?></h6>
                                                        <small class="text-muted">You are replying as <?=$user['name']?></small>
                                                    </div>
                                                </div>
                                                <textarea id="reply-comment" class="form-control" rows="2" placeholder="Write your reply here"></textarea>
                                                <button type="submit" class="btn btn-secondary btn-sm">Submit Reply</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>`
                        );

                        if (!response.has_more) {
                            canLoadMore=false;
                        }else{
                            const resultCard = `
                            <div class="my-3 mb-2 fs-4 text-center">
                                Seems like that's everything
                            </div>`;
                        $('#results').append(resultCard);
                            index+=1;
                        }
                    });
                } else {
                    console.log('No more comments to load.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching comments:', error);
            }
        });
    }
    fetchSearchResults();
    $(window).scroll(function() {
        // Check if we are at the bottom of the page
        if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
            if(canLoadMore){
                fetchSearchResults();
            }
        }
    });
    
    //For sumitting a comment
    $('#commentForm').on('submit', function(event) {
        event.preventDefault();  // Prevent the default form submission
        event.stopPropagation();  // Stop event from propagating further

        // Get form data
        const comment = $('#comment').val();
        const post_id = $('input[name="id"]').val();

        // Prepare the URL with query parameters
        const url = 'api/create/comment?id=' + encodeURIComponent(post_id) + '&comment=' + encodeURIComponent(comment);

        // Send the data via AJAX GET request
        $.ajax({
            url: url,  // The URL with query parameters
            method: 'GET',  // Use GET method
            success: function(response) {
              
                if (response.status == "success") {
                    // Handle success: display a message, clear the form
                    $('#responseMessage').html(`<div class="alert alert-success">${response.message}</div>`);
                    $('#comment').val('');  // Clear the comment textarea
                    $('#commentForm')[0].reset();  // Reset the entire form

                    // Optional: Display a success alert or message
                    alert("Your comment has been submitted successfully!");
                } else {
                    // Handle error
                    $('#responseMessage').html(`<div class="alert alert-danger">${response.message}</div>`);
                }
            },
            error: function(xhr, status, error) {
                // Handle AJAX error
                $('#responseMessage').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
            }
        });
    });

    // For Replies
    $(document).on('submit', '.replyForm', function(event) {
        event.preventDefault();  // Prevent default form submission

        // Get form data
        const commentId = $(this).data('comment-id');  // Get the comment ID
        const replyContent = $(this).find('textarea').val();  // Get the reply content

        // Check if reply content is not empty
        if (!replyContent.trim()) {
            alert('Please enter a reply.');
            return;
        }

        // Send the data via AJAX POST request
        $.ajax({
            url: 'api/create/reply',  
            method: 'GET', 
            data: {
                comment_id: commentId,  
                reply_content: replyContent  
            },
            
            success: function(response) {
                // If reply is successfully submitted, handle the response
                if (response.status === 'success') {
                    // Optionally: Append the new reply to the comment's reply list
                    // For example, you can append the reply here

                    // Clear the reply textarea after submission
                    $(`form[data-comment-id="${commentId}"]`).find('textarea').val('');

                    // Optionally: Close the reply form (collapse it)
                    $(`#replySection${commentId}-reply`).collapse('hide');

                   //Update the
                    button = $('.fetch-replies-btn[data-comment-id="' + commentId + '"]')
                    let currentText = button.text().trim(); // e.g., "Replies (5)"
                        let matches = currentText.match(/\((\d+)\)/); // Extract the number inside parentheses

                        if (matches) {
                            let currentNum = parseInt(matches[1]); // Extract the numeric value
                            let newNum = currentNum + 1; // Increment by 1

                            // Update the button text
                            button.text(`Replies (${newNum})`);
                        }

                    alert('Your reply has been submitted!');
                } else {
                    alert('Failed to submit your reply. Please try again.');
                }

            },
            error: function(xhr, status, error) {
                // Handle error if the AJAX request fails
                alert('An error occurred while submitting your reply. Please try again.');
            }
        });
    });

    $(document).on('click', '.fetch-replies-btn', function () {
    const commentId = $(this).data('comment-id'); 
    const repliesContainer = `#replySection${commentId} .replies`; 
    let index = 0; // Start from the first set of replies

    // AJAX request to fetch replies
    $.ajax({
        url: 'api/get/reply', 
        method: 'GET',
        data: {
            comment_id: commentId, 
            index: index
        },
        success: function (response) {
            if (response.status === 'success') {
                // Clear existing replies before appending new ones
                $(repliesContainer).empty();

                // Loop through the replies and append them
                response.data.forEach(reply => {
                    $(repliesContainer).prepend(`
                        <div class="reply mb-3 p-3 border rounded shadow-sm">
                            <div class="d-flex align-items-center mb-2">
                                <img src="${reply.profile_image || 'https://via.placeholder.com/40'}" 
                                    alt="Reply User Avatar" 
                                    class="rounded-circle me-3" 
                                    style="width: 40px; height: 40px;">
                                <div>
                                    <h6 class="mb-0">${reply.name}</h6>
                                    <small class="text-muted">Replied on ${reply.created_at}</small>
                                </div>
                            </div>
                            <p class="mb-0">${reply.content}</p>
                        </div>
                    `);
                });

                // If no replies are found, show a message
                if (response.data.length === 0) {
                    $(repliesContainer).append('<p class="text-muted">No replies yet.</p>');
                }

                // If there are more replies to load, show the "Get more replies" button
                if (response.has_more) {
                    $(repliesContainer).prepend(`
                        <button class="btn btn-link text-decoration-none load-more-replies" 
                            data-comment-id="${commentId}">
                            Get more replies
                        </button>
                    `);
                } else {
                    // If no more replies, indicate that there are no more to load
                    $(repliesContainer).append('<p class="text-muted">No more replies.</p>');
                }

                // Append the "Collapse" button
                $(repliesContainer).append(`
                    <button class="btn btn-link text-decoration-none collapse-replies" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#replySection${commentId}" 
                        aria-expanded="true" 
                        aria-controls="replySection${commentId}">
                        Collapse
                    </button>
                `);
            } else {
                console.error('Error fetching replies:', response.message);
            }
        },
        error: function (xhr, status, error) {
            console.error('AJAX error:', error);
        }
    });
});

    // Event handler for "Get more replies" button click
    $(document).on('click', '.load-more-replies', function () {
        const commentId = $(this).data('comment-id');
        const repliesContainer = `#replySection${commentId} .replies`; 
        const currentReplyCount = ($(repliesContainer).children('.reply').length/20); // Get number of current replies
        // AJAX request to fetch the next set of replies
        $.ajax({
            url: 'api/get/reply', 
            method: 'GET',
            data: {
                comment_id: commentId, 
                index: currentReplyCount // Start from the current number of replies
            },

            success: function (response) {
                if (response.status === 'success') {
                    // Loop through the replies and append them
                    response.data.forEach(reply => {
                        $(repliesContainer).append(`
                            <div class="reply mb-3 p-3 border rounded shadow-sm">
                                <div class="d-flex align-items-center mb-2">
                                    <img src="${reply.profile_image || 'https://via.placeholder.com/40'}" 
                                        alt="Reply User Avatar" 
                                        class="rounded-circle me-3" 
                                        style="width: 40px; height: 40px;">
                                    <div>
                                        <h6 class="mb-0">${reply.name}</h6>
                                        <small class="text-muted">Replied on ${reply.created_at}</small>
                                    </div>
                                </div>
                                <p class="mb-0">${reply.content}</p>
                            </div>
                        `);
                    });

                    // If there are more replies to load, keep the "Get more replies" button
                    if (response.has_more) {
                        $(repliesContainer).prepend(`
                            <button class="btn btn-link text-decoration-none load-more-replies" 
                                data-comment-id="${commentId}">
                                Get more replies
                            </button>
                        `);
                    } else {
                        // If no more replies, remove the "Get more replies" button
                        $(repliesContainer).find('.load-more-replies').remove();
                        $(repliesContainer).prepend('<p class="text-muted">No more replies.</p>');
                    }
                } else {
                    console.error('Error fetching more replies:', response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX error:', error);
            }
        });
    });

});

</script>
