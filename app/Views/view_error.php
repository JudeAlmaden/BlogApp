<?php
if (!empty($errors)): ?>
    <div class='alert alert-danger' role='alert'>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
