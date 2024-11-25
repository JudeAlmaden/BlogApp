<div class="container mt-4">
    <!-- Comment Section -->
     
    <h4 class="mb-3">Comments</h4>
    <!-- Comment Form -->
    <div class="mb-3">
        <h5>Leave a Comment</h5>
        <form>
            <div class="d-flex align-items-center mb-3">
                <!-- User Avatar -->
                <img src="https://via.placeholder.com/50" alt="User Avatar" class="rounded-circle me-3" style="width: 50px; height: 50px;">
                <!-- User Name -->
                <div>
                    <h6 class="mb-0">John Doe</h6>
                    <small class="text-muted">You are commenting as John Doe</small>
                </div>
            </div>
            <div class="mb-3">
                <label for="comment" class="form-label">Comment</label>
                <textarea id="comment" class="form-control" rows="3" placeholder="Write your comment here"></textarea>
            </div>
            <button type="submit" class="btn btn-primary mb-5">Submit</button>
        </form>
    </div>

    
    <!-- Single Comment -->
    <div class="comment mb-3 p-3 border rounded shadow-sm">
        <div class="d-flex align-items-center mb-2">
            <!-- User Avatar -->
            <img src="https://via.placeholder.com/50" alt="User Avatar" class="rounded-circle me-3" style="width: 50px; height: 50px;">
            
            <!-- User Info -->
            <div>
                <h6 class="mb-0">John Doe</h6>
                <small class="text-muted">Posted on November 23, 2024</small>
            </div>
        </div>
        <!-- Comment Content -->
        <p class="mb-0">This is a sample comment. You can customize this template to fit your website's design!</p>

        <!-- Reply Button -->
        <button class="btn btn-link text-decoration-none mt-2" type="button" data-bs-toggle="collapse" data-bs-target="#replySection1" aria-expanded="false" aria-controls="replySection1">
            Comments
        </button>
        <button class="btn btn-link text-decoration-none mt-2" type="button" data-bs-toggle="collapse" data-bs-target="#replySection2" aria-expanded="false" aria-controls="replySection2">
            Reply
        </button>

            <!-- Replies -->        
        <div class="collapse mt-3" id="replySection1">
            <div class="replies mt-4 ps-4">
                <div class="comment mb-3 p-3 border rounded shadow-sm">
                    <div class="d-flex align-items-center mb-2">
                        <!-- Reply Avatar -->
                        <img src="https://via.placeholder.com/50" alt="Reply User Avatar" class="rounded-circle me-3" style="width: 40px; height: 40px;">
                        
                        <!-- Reply User Info -->
                        <div>
                            <h6 class="mb-0">Jane Smith</h6>
                            <small class="text-muted">Replied on November 23, 2024</small>
                        </div>
                    </div>
                    <!-- Reply Content -->
                    <p class="mb-0">Thank you for sharing your thoughts, John! I completely agree with your point.</p>
                </div>
            </div>
        </div>

        <!-- Reply Section -->
        <div class="collapse mt-3" id="replySection2">
            <div class="card card-body p-3">
                <form>
                    <div class="mb-3">
                        <label for="reply-name" class="form-label">Name</label>
                        <input type="text" id="reply-name" class="form-control" placeholder="Your name">
                    </div>
                    <div class="mb-3">
                        <label for="reply-comment" class="form-label">Reply</label>
                        <textarea id="reply-comment" class="form-control" rows="2" placeholder="Write your reply here"></textarea>
                    </div>
                    <button type="submit" class="btn btn-secondary btn-sm">Submit Reply</button>
                </form>
            </div>
        </div>
    </div>
</div>
