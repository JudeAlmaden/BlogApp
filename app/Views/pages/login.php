<?php
    if (isset($_SESSION['id'])) {
        header('Location: homepage');
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Generic Website</title>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="/IntegrativeProgramming/finals/BlogWebApp/public/css/style.css">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-lg" style="width: 400px;">
            <div class="text-center">
                <div class="logo mb-3">
                    <img src="https://www.freepnglogos.com/uploads/logo-chatgpt-png/chatgpt-brand-logo-transparent.png" alt="LogoLangz" class="img-fluid" style="width: 100px;">
                </div>
                <h3 class="mb-3">Generic Website</h3>
            </div>
            <form action="login" method="POST" class="mt-3">
                <!-- Email Field -->
                <div class="form-group mb-3">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="far fa-user"></i></span>
                        <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email" required>
                    </div>
                </div>
                <!-- Password Field -->
                <div class="form-group mb-4">
                    <label for="pwd" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                        <input type="password" class="form-control" name="password" id="pwd" placeholder="Enter your password" required>
                    </div>
                </div>
                <!-- Login Button -->
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            <div class="text-center mt-3">
                <p>Don't have an account? <a href="<?= $base_url ?>/register" class="text-decoration-none">Sign up here!</a></p>
            </div>
            <!-- Error and Success Messages -->
            <?php require_once(__DIR__ . "/../view_error.php"); ?>
            <?php require_once(__DIR__ . "/../view_success.php"); ?>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ku1NK5OK7UhpqlInW2PvoCQeEcxMT7ftDXc5Yc2DQNRdLuSWV5M6xIlQTuk77LKP" crossorigin="anonymous"></script>
</body>
</html>
