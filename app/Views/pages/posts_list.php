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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- <script src="/IntegrativeProgramming/finals/ToDoMVC/app/Views/components/app.js"></script> -->
</head>

<body>
<div class="container-fluid">
    <!-- Navbar -->
    <?php require_once(__DIR__."/../components/navbar.php"); ?>

    <div class="row my-3" style="height:600px; max-height:50vh">

    <div class="col-12 mb-5 row">
        <?php require_once(__DIR__."/../components/post_search_form.php"); ?>
    </div>

    <!-- Footer -->
    <div class="col-12 ">
        <?php require_once(__DIR__."/../components/footer.php"); ?>
    </div>
    </div>
</div>
</body>
</html>
