<?php
// Check if user is logged in, otherwise redirect to login
if (!isset($_SESSION['id'])) {
    header('Location: login');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog App</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    
    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</head>

<body>
<div class="container-fluid">
    <!-- Navbar -->
    <?php require_once(__DIR__."/../components/navbar.php"); ?>

    <div class="row my-3" style="height:700px; max-height:70vh">
    <?php require_once(__DIR__ . "/../components/users_list.php"); ?>
    </div>
    <?php require_once(__DIR__ . "/../components/footer.php"); ?>
    <?php require_once(__DIR__ . "/../view_error.php"); ?>
    <?php require_once(__DIR__ . "/../view_success.php"); ?>
    </div>
</div>
</body>
</html>
