<?php

if (isset($_SESSION['success']) && !empty($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?php foreach ($_SESSION['success'] as $succ): ?>
            <li><?php echo htmlspecialchars($succ); ?></li>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php unset($_SESSION["success"]);?>