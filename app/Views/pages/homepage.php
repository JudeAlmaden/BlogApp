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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    
    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- <script src="/IntegrativeProgramming/finals/ToDoMVC/app/Views/components/app.js"></script> -->
</head>

<body>
<div class="container-fluid">
    <!-- Navbar -->
    <?php require_once(__DIR__."/../components/navbar.php"); ?>

    <div class="row my-3" style="height:600px; max-height:50vh">
        <!-- Carousel -->
        <div class="col-12 mb-5">
            <?php require_once(__DIR__."/../components/bootstrap_carousell.php"); ?>
        </div>

        <!-- Card Grid -->
        <div class="col-12 mb-3">
            <?php require_once(__DIR__."/../components/bootstrap_card_grid.php"); ?>
        </div>

        <div class="col-12 px-5"><hr></div>

        <!-- Featured Blogs -->
        <div class="col-12">
            <?php require_once(__DIR__."/../components/featured_blogs.php"); ?>
        </div>

        <!-- Read More Button -->
        <div class="d-flex justify-content-center mt-4">
            <button class="btn btn-primary rounded-pill p-3 fs-3" style="width:300px">Read More</button>
        </div>

        <!-- Footer -->
        <div class="col-12">
            <?php require_once(__DIR__."/../components/footer.php"); ?>
        </div>
    </div>
</div>
</body>
</html>
