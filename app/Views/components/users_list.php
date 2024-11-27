<?php if (!empty($users)): ?>
    <!-- Search Results -->
    <div class="container mt-4 px-5">
        <div class="card shadow-sm border-light">
            <!-- Card Header with Background Color -->
            <h3 class="mb-4 px-5 pt-5 text-dark rounded-top">
                Search Results for User "<?php echo htmlspecialchars($_GET['query']); ?>"
            </h3>

            <div class="card-body">
                <!-- List of Users -->
                <div class="list-group mb-4">
                    <?php foreach ($users as $user): ?>
                        <a href="view-profile?id=<?php echo $user['id']; ?>" class="list-group-item list-group-item-action d-flex align-items-center mb-3 border rounded-3 shadow-sm hover-shadow">
                            <!-- Profile Image or Placeholder -->
                            <div class="profile-image me-3">
                                <?php if ($user['profile_image']): ?>
                                    <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile Image" class="rounded-circle" style="width: 60px; height: 60px;">
                                <?php else: ?>
                                    <div class="rounded-circle d-flex justify-content-center align-items-center" style="width: 60px; height: 60px; background-color: #ddd;">
                                        <span class="text-center text-white">No Image</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <!-- User Info -->
                            <div class="flex-grow-1">
                                <strong class="fs-5"><?php echo htmlspecialchars($user['name']); ?></strong>
                                <p class="mb-1 text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                            </div>
                            <!-- Privilege Badge -->
                            <span class="badge bg-primary align-self-center"><?php echo ucfirst($user['privilege']); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- No results found -->
    <div class="container mt-4">
        <div class="alert alert-warning" role="alert">
            No users found for the search term "<?php echo htmlspecialchars($_GET['query']); ?>".
        </div>
    </div>
<?php endif; ?>
