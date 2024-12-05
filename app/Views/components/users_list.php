<?php if (!empty($users)): ?>
    <!-- Search Results -->
    <div class="container mt-5 px-5">
        <div class="card shadow-lg border-light">
            <!-- Card Header with Background Color -->
            <h3 class="mb-5 px-5 pt-4 text-dark rounded-top" style="font-size: 1.75rem;">
                Search Results for User "<?php echo htmlspecialchars($_GET['query']); ?>"
            </h3>

            <div class="card-body">
                <!-- List of Users -->
                <div class="list-group">
                    <?php foreach ($users as $user): ?>
                        <a href="view-profile?id=<?php echo $user['id']; ?>" class="list-group-item list-group-item-action d-flex align-items-center mb-4 border rounded-3 shadow-sm hover-shadow p-4" style="transition: all 0.3s ease;">
                            <!-- Profile Image or Placeholder -->
                            <div class="profile-image me-5">
                                <?php if ($user['profile_image']): ?>
                                    <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile Image" class="rounded-circle" style="width: 80px; height: 80px;">
                                <?php else: ?>
                                    <div class="rounded-circle d-flex justify-content-center align-items-center" style="width: 80px; height: 80px; background-color: #e0e0e0;">
                                        <span class="text-center text-white" style="font-size: 12px;">No Image</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <!-- User Info -->
                            <div class="flex-grow-1">
                                <strong class="fs-5 text-dark"><?php echo htmlspecialchars($user['name']); ?></strong>
                                <p class="mb-1 text-muted" style="font-size: 0.9rem;"><?php echo htmlspecialchars($user['email']); ?></p>
                                <!-- Bio Section -->
                                <?php if (!empty($user['bio'])): ?>
                                    <p class="text-muted" style="font-size: 0.85rem; color: #555;">
                                        <?php echo substr(htmlspecialchars($user['bio']), 0, 150); ?>
                                        <?php echo strlen($user['bio']) > 150 ? '...' : ''; ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <!-- Privilege Badge -->
                            <?php
                                $badgeClass = '';
                                switch (strtolower($user['privilege'])) {
                                    case 'admin':
                                        $badgeClass = 'bg-danger'; // red for admin
                                        break;
                                    case 'moderator':
                                        $badgeClass = 'bg-info'; // blue for moderator
                                        break;
                                    case 'user':
                                    default:
                                        $badgeClass = 'bg-secondary'; // gray for user
                                        break;
                                }
                            ?>
                            <span class="badge <?php echo $badgeClass; ?> align-self-center py-2 px-3" style="font-size: 0.9rem; color: white;"><?php echo ucfirst($user['privilege']); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- No results found -->
    <div class="container mt-5">
        <div class="alert alert-warning alert-dismissible fade show" role="alert" style="font-size: 1.1rem; padding: 15px 25px;">
            No users found for the search term "<strong><?php echo htmlspecialchars($_GET['query']); ?></strong>".
        </div>
    </div>
<?php endif; ?>
