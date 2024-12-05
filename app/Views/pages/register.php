<?php
    if(!isset($_SESSION['id'])){
        header('location:login');
        exit;

    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Generic Website</title>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="/IntegrativeProgramming/finals/BlogWebApp/public/css/style.css">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-lg p-4" style="width: 400px;">
            <div class="text-center">
                <img src="https://www.freepnglogos.com/uploads/logo-chatgpt-png/chatgpt-brand-logo-transparent.png" alt="LogoLangz" class="img-fluid mb-3" style="width: 100px;">
                <h3 class="mb-3">Generic Website</h3>
            </div>
            <form action="register" method="POST" autocomplete="off">
                <!-- Name Field -->
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="far fa-user"></i></span>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter your name" required>
                    </div>
                </div>
                <!-- Email Field -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="far fa-envelope"></i></span>
                        <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email" required>
                    </div>
                </div>
                <!-- Password Field -->
                <div class="mb-3">
                    <label for="pwd" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                        <input type="password" class="form-control" name="password" id="pwd" placeholder="Enter your password" required>
                    </div>
                </div>
                <!-- Confirm Password Field -->
                <div class="mb-3">
                    <label for="pwd-confirm" class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                        <input type="password" class="form-control" name="confirm_password" id="pwd-confirm" placeholder="Confirm your password" required>
                    </div>
                </div>
                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary w-100" id="register-btn" disabled>Register</button>
            </form>
            <div class="text-center mt-3">
                <p>Already have an account? <a href="<?= $base_url ?>/login" class="text-decoration-none">Login here!</a></p>
            </div>
            <!-- Error and Success Messages -->
            <?php require_once(__DIR__ . "/../view_error.php"); ?>
            <?php require_once(__DIR__ . "/../view_success.php"); ?>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ku1NK5OK7UhpqlInW2PvoCQeEcxMT7ftDXc5Yc2DQNRdLuSWV5M6xIlQTuk77LKP" crossorigin="anonymous"></script>
    <script>
        // JavaScript for enabling/disabling the Register button
        const inputFields = document.querySelectorAll('input');
        const submitButton = document.getElementById('register-btn');

        // Event listener for all input fields
        inputFields.forEach(input => {
            input.addEventListener('input', checkInputs);
        });

        // Function to check if all inputs are valid and passwords match
        function checkInputs() {
            const allFilled = [...inputFields].every(input => input.value.trim() !== '');
            const passwordsMatch = validatePasswords();

            submitButton.disabled = !(allFilled && passwordsMatch);
        }

        // Function to validate if passwords match
        function validatePasswords() {
            const password = document.getElementById("pwd").value.trim();
            const passwordConfirm = document.getElementById("pwd-confirm").value.trim();
            return password && passwordConfirm && password === passwordConfirm;
        }
    </script>
</body>
</html>
