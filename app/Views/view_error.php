<?php if (!empty($errors)): ?>
    <div class="alert alert-danger position-fixed bottom-0 start-0 mb-3 ms-3" id="errorAlert" role="alert">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <script>
        // Fade out the alert after 5 seconds
        setTimeout(function() {
            let alert = document.getElementById('errorAlert');
            alert.classList.add('fade');  // Add fade class to trigger Bootstrap fade-out
            setTimeout(function() {
                alert.style.display = 'none';  // Hide the alert completely after fading out
            }, 150);  // Wait for the fade-out transition to finish
        }, 5000);  // 5 seconds
    </script>

<?php endif; ?>
