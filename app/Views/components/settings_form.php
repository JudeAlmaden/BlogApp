<div class="container mt-5">
    <!-- User Settings Title -->
    <h2>User Settings</h2>
    
    <!-- Profile Settings Section -->
    <div class="card mb-4">
    <div class="card-header">
        <h5>Profile Settings</h5>
    </div>
    <div class="card-body">
        <form action="update-profile" method="POST" enctype="multipart/form-data">
            <!-- Name -->
            <div class="mb-3">
                <label for="user-name" class="form-label">Name</label>
                <input type="text" id="user-name" class="form-control" name="name" value="<?php echo isset($data['user']['name']) ? $data['user']['name'] : ''; ?>" required>
            </div>

            <!-- Bio -->
            <div class="mb-3">
                <label for="user-bio" class="form-label">Bio</label>
                <textarea id="user-bio" class="form-control" rows="3" name="bio" placeholder="Tell us a bit about yourself" max="250" required><?php echo isset($data['user']['bio']) ? $data['user']['bio'] : 'Write here'; ?></textarea>
            </div>

            <!-- Gender -->
            <div class="mb-3">
                <label for="user-gender" class="form-label">Gender</label>
                <select id="user-gender" class="form-select" name="gender" required>
                    <option value="male" <?php echo (isset($data['user']['gender']) && $data['user']['gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
                    <option value="female" <?php echo (isset($data['user']['gender']) && $data['user']['gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
                    <option value="other" <?php echo (isset($data['user']['gender']) && $data['user']['gender'] == 'other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>

            <!-- Avatar -->
            <div class="mb-3">
                  
                <!-- Avatar Preview -->
                <div id="avatar-preview-container" class="d-flex justify-content-center align-items-center flex-column">

                    <?php if (isset($data['user']['profile_image']) && !empty($data['user']['profile_image'])): ?>
                        <!-- Avatar image if already set -->
                        <div class="col-12 text-center">Current Profile Image</div>
                        <img src="<?php echo $data['user']['profile_image']; ?>" alt="Profile Image" class="img-fluid col-12 mt-2 rounded-circle" id="avatar-preview" style="width: 150px; height: 150px; object-fit: cover;">
                    <?php else: ?>
                        <!-- Placeholder if no avatar is set -->
                        <div class="col-12 text-center">No profile image set Currently</div>
                    <?php endif; ?>
                </div>

                <label for="user-avatar" class="form-label">Profile Avatar</label>
                <input type="file" class="form-control" id="user-avatar" name="avatar" onchange="previewImage(event)" value="">
            </div>

            <!-- Save Profile Button -->
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</div>

    <!-- Change Email Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Change Email</h5>
        </div>
        <div class="card-body">
            <form action="update-email" method="POST">
                <!-- New Email -->
                <div class="mb-3">
                    <label for="new-email" class="form-label">New Email</label>
                    <input type="email" id="new-email" class="form-control" name="email" placeholder="Enter new email address" value="<?php echo isset($data['user']['email']) ? $data['user']['email'] : ''; ?>" required>
                </div>

                <!-- Current Password -->
                <div class="mb-3">
                    <label for="current-password-email" class="form-label">Confirm Password to Change Email</label>
                    <input type="password" id="current-password-email" class="form-control" name="password" placeholder="Enter your current password" required>
                </div>

                <!-- Save Email Button -->
                <button type="submit" class="btn btn-warning">Save New Email</button>
            </form>
        </div>
    </div>

    <!-- Change Password Section -->
    <div class="card">
        <div class="card-header">
            <h5>Change Password</h5>
        </div>
        <div class="card-body">
            <form action="update-password" method="POST">
                <!-- Current Password -->
                <div class="mb-3">
                    <label for="current-password" class="form-label">Current Password</label>
                    <input type="password" id="current-password" class="form-control" name="current_password" placeholder="Enter current password" required>
                </div>

                <!-- New Password -->
                <div class="mb-3">
                    <label for="new-password" class="form-label">New Password</label>
                    <input type="password" id="new-password" class="form-control" name="new_password" placeholder="Enter new password" required>
                </div>

                <!-- Confirm New Password -->
                <div class="mb-3">
                    <label for="confirm-password" class="form-label">Confirm New Password</label>
                    <input type="password" id="confirm-password" class="form-control" name="confirm_password" placeholder="Confirm new password" required>
                </div>

                <!-- Save Password Button -->
                <button type="submit" class="btn btn-success">Change Password</button>
            </form>
        </div>
    </div>
</div>

<script>
    // Function to preview image when selected
    function previewImage(event) {
        var reader = new FileReader();

        reader.onload = function() {
            var preview = document.getElementById('avatar-preview');
            preview.src = reader.result;  // Set the image source to the selected file
            preview.style.display = 'block';  // Ensure the preview is shown
            preview.style.objectFit = 'cover';  // Crop the image to fit the circle
        };

        // If no previous preview exists, create one
        if (!document.getElementById('avatar-preview')) {
            var previewContainer = document.getElementById('avatar-preview-container');
            var newImage = document.createElement('img');
            newImage.id = 'avatar-preview';
            newImage.className = 'img-fluid mt-2 rounded-circle';
            newImage.style.width = '150px';
            newImage.style.height = '150px';
            newImage.style.objectFit = 'cover';  // Crop the image to fit the circle

            var div = document.createElement('div');
            div.className = 'col-12 text-center';
            div.textContent = 'New Profile Image';
            previewContainer.appendChild(div);
            previewContainer.appendChild(newImage);
        }

        reader.readAsDataURL(event.target.files[0]);
    }
</script>
