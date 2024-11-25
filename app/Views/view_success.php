<?php if (isset($_SESSION['success']) && !empty($_SESSION['success'])): ?>
    <div class="alert alert-success position-fixed bottom-0 start-0 w-100 mb-0" id="successAlert" role="alert">
        <ul>
            <?php foreach ($_SESSION['success'] as $succ): ?>
                <li><?php echo htmlspecialchars($succ); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php unset($_SESSION["success"]); ?>
<?php endif; ?>