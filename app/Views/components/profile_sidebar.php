<div class="col-md-3 mt-5 p-5">
    <?php if ($user): ?>
        <div class="user-profile sticky-profile">
            <!-- Profile Avatar Image -->
            <div class="profile-avatar">
                <?php if ($user['profile_image']): ?>
                    <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile Image" class="avatar-img">
                <?php else: ?>
                    <div class="avatar-placeholder">No Image</div>
                <?php endif; ?>
            </div>

            <!-- Profile Info -->
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($user['name']); ?></h2>

                <!-- Email with Icon -->
                <div class="profile-item">
                    <i class="fas fa-envelope"></i>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                </div>

                <!-- Gender with Icon -->
                <div class="profile-item">
                    <i class="fas fa-venus-mars"></i>
                    <p><strong>Gender:</strong> <?php echo htmlspecialchars($user['gender']); ?></p>
                </div>

                <!-- Bio with Icon -->
                <div class="profile-item">
                    <i class="fas fa-user-edit"></i>
                    <p><strong>Bio:</strong> <?php echo ($user['bio']); ?></p>
                </div>

                <?php if ($_SESSION['privilege'] == 'admin' && $user['id'] != $_SESSION['id']): ?>
                    <!-- Privilege Management Form -->
                    <div class="profile-item mt-4">
                        <form action="set-user" method="POST" class="d-flex align-items-center justify-content-between">
                            <!-- Hidden user ID -->
                            <input type="number" name="user_id" id="user_id" class="form-control" value="<?= $user['id'] ?>" hidden>

                            <!-- Privilege Select -->
                            <select name="privilege" id="privilege" class="form-select form-select-sm" required style="max-width: 160px;">
                                <option value="user" <?php echo ($user['privilege'] == 'user') ? 'selected' : ''; ?>>User</option>
                                <option value="moderator" <?php echo ($user['privilege'] == 'moderator') ? 'selected' : ''; ?>>Moderator</option>
                            </select>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary btn-sm ms-3">Set</button>
                        </form>
                    </div>
                <?php else: ?>
                    <!-- Display User Privilege Badge for Non-admin Users or Same Session -->
                    <div class="profile-item mt-4">
                        <strong>Privilege:</strong> 
                        <span class="badge 
                            <?php 
                                if ($user['privilege'] == 'admin') {
                                    echo 'bg-danger';  // Red for Admin
                                } elseif ($user['privilege'] == 'moderator') {
                                    echo 'bg-warning';  // Yellow for Moderator
                                } else {
                                    echo 'bg-secondary';  // Grey for User
                                }
                            ?>">
                            <?= ucfirst($user['privilege']) ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <p>User not found.</p>
    <?php endif; ?>
</div>

<!-- Include Font Awesome CDN for icons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

<style>
    /* Make the profile section sticky */
    .sticky-profile {
        position: sticky;
        top: 20px; /* Adjust the distance from the top */
        background-color: #fff; /* Background color for the profile section */
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1); /* Enhanced shadow for a more elevated look */
        height: auto;
        max-height: 90vh; /* Limit the height to the screen height */
        overflow-y: auto;
    }

    /* Profile Avatar */
    .profile-avatar {
        text-align: center;
        margin-bottom: 20px;
    }

    .avatar-img {
        width: 140px; /* Slightly larger avatar size */
        height: 140px;
        border-radius: 50%;
        object-fit: cover; /* Ensures the image covers the avatar circle */
        border: 4px solid #ccc; /* Slightly thicker border */
    }

    .avatar-placeholder {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #f0f0f0;
        color: #999;
        font-weight: bold;
        border: 4px solid #ccc;
    }

    /* Profile Info Styling */
    .profile-info {
        text-align: left;
        padding-left: 15px;
    }

    .profile-info h2 {
        font-size: 26px; /* Larger name font size */
        margin-bottom: 15px;
        color: #333;
    }

    .profile-info p {
        font-size: 18px;
        line-height: 1.6;
        margin: 8px 0;
        color: #444;
    }

    .profile-info strong {
        font-weight: bold;
        color: #333;
    }

    .profile-item {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    .profile-item i {
        font-size: 20px;
        color: #007bff; /* Blue color for the icons */
        margin-right: 10px;
    }

    .profile-item p {
        margin: 0;
        font-size: 16px;
        color: #555;
    }
</style>
