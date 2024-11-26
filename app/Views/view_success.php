<?php if (isset($_SESSION['success']) && !empty($_SESSION['success'])): ?>
    <div 
        class="alert alert-success position-fixed bottom-0 start-0 w-100 mb-0 fade-out" 
        id="successAlert" 
        role="alert" 
        style="z-index: 20;">
        <ul>
            <?php foreach ($_SESSION['success'] as $succ): ?>
                <li><?php echo htmlspecialchars($succ); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <script>
        // Automatically fade out the alert after 5 seconds
        document.addEventListener("DOMContentLoaded", () => {
            const alert = document.getElementById('successAlert');
            setTimeout(() => {
                alert.style.opacity = '0'; // Start fading out
                setTimeout(() => {
                    alert.style.display = 'none'; // Hide completely after transition
                }, 1000); // Match the CSS transition duration
            }, 5000); // Delay before fade-out starts (5 seconds)
        });
    </script>
    <?php unset($_SESSION["success"]); ?>
<?php endif; ?>
