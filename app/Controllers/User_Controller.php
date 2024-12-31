<?php

require_once("Controller.php");
require_once(__DIR__."/../Models/UserModel.php");

class UserController extends Controller{

    function login() {
        $userModel = new UserModel();
        $errors = [];
        
        // POST Request Handling
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = sanitizeOutput($_POST["email"]);
            $password = sanitizeOutput($_POST["password"]);
            
            // Check for empty fields
            if ($email == "" || $password == "") {
                array_push($errors, "Email or password cannot be empty");
            }
    
            // Validate email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                array_push($errors, "Invalid Email");
            }
    
            // Check if user exists
            $user = $userModel->getUserLogin($email, $password);
    
            // If there are no errors, set session and redirect to homepage
            if (empty($errors)) {
                $this->setSession($user['id'], $user['name'],$user['privilege']);
                header('Location: homepage');
                exit; 
            } else {
                // Pass errors back to login view
                $this->view("pages/login", ['errors' => $errors]);
                exit; // Stop further execution
            }
        }

        $this->view("pages/login", ['errors' => $errors]);
    }
    

    function register(){

        $userModel = new UserModel();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = sanitizeOutput($_POST["name"]);
            $email = sanitizeOutput($_POST["email"]);
            $password = sanitizeOutput($_POST["password"]);
            $confirm_password = sanitizeOutput($_POST["confirm_password"]);
            $exists = $userModel->isEmailValid( $email );

            if($name == "" || $email == ""  || $password == "" || $confirm_password== ""){
                array_push($errors,"Cannot have empty fields");

            }
            if($confirm_password != $password){
                array_push($errors,"Passwords do not match");

            }
            if (empty($password)) {
                $errors[] = "Password cannot be empty.";
            } 
            
            if (!preg_match('/^(?=.*\d)(?=.*[@#\-_$%^&+=§!?.])[A-Za-z0-9@#\-_$%^&+=§!?.]{8,20}$/', $password)) {
                $errors[] = "Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one digit, and one special character.";
            }
            
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                array_push($errors,"Invalid email");

            }
            if($exists){
                array_push($errors,"Email already used");

            }
            if(!$errors){
                $userModel->insertUser($name, $email, $password);
                $_SESSION['success'] = ["Registration successful! Please log in."];
                               
                // Redirect to login page
                header('Location: login');
                exit;
            }

        }
        $this->view("pages/register", ['errors' => $errors]);
        exit;
    }

    function setSession($id, $name, $privilege){
        session_start();

        $_SESSION['id'] = $id;
        $_SESSION['name'] =$name;
        $_SESSION['privilege'] = $privilege;
        
        return;
    }

    function endSession(){
        session_destroy();

        header('location:login');
        echo("Hello");
        exit;
    }

    function updateProfile(){    
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Initialize an array to store potential errors
            $id = isset($_SESSION['id']) ? htmlspecialchars(trim($_SESSION['id'])) : '';
            if (empty($id)) {
                $errors[] = 'You are not logged in';
            }
        

            // Sanitize and validate the name (text input)
            $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
            if (empty($name)) {
                $errors[] = 'Name is required.';
            }
        
            
            // Sanitize and validate the bio (text input)
            $bio = isset($_POST['bio']) ? htmlspecialchars(trim($_POST['bio'])) : '';
            if (empty($bio)) {
                $errors[] = 'Bio is required.';
            }
        
            // Sanitize and validate the gender (dropdown)
            $gender = isset($_POST['gender']) ? htmlspecialchars(trim($_POST['gender'])) : '';
            if (!in_array($gender, ['male', 'female', 'other'])) {
                $errors[] = 'Invalid gender selection.';
            }
        
            // Sanitize and validate the avatar (file upload)
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
                $fileTmpPath = $_FILES['avatar']['tmp_name'];
                $fileName = $_FILES['avatar']['name'];
                $fileSize = $_FILES['avatar']['size'];
                $fileType = $_FILES['avatar']['type'];
                
                // Define allowed file types
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        
                // Check if file is an allowed image type
                if (!in_array($fileType, $allowedTypes)) {
                    $errors[] = 'Invalid file type for avatar. Only JPEG, PNG, or GIF allowed.';
                }
        
                // Check file size (max 2MB for example)
                if ($fileSize > 5 * 1024 * 1024) { // 2MB
                    $errors[] = 'Avatar image size exceeds the limit of 5MB.';
                }
        
                // If no errors, move the uploaded file to the desired directory
                if (empty($errors)) {
                    // Ensure that the uploads directory exists
                    $uploadDir = 'public/uploads/';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
        
                    // Generate a unique file name for the avatar
                    $avatarName = uniqid('avatar_') . '_' . basename($fileName);
                    $uploadPath = $uploadDir . $avatarName;
        
                    // Move the uploaded file to the directory
                    if (move_uploaded_file($fileTmpPath, $uploadPath)) {
                        $avatar = $uploadPath;
                    } else {
                        $errors[] = 'There was an error uploading your avatar.';
                    }
                }
            }

            $userModel = new UserModel();
            $_SESSION['errors'] = $errors;
            
            // Check if there are any errors
            if (empty($errors) &&   $userModel->updateProfile($id, $name, $bio, $gender, $avatar)) {
                $_SESSION['success'] = ["Profile updated Sucessfully"];

                return header("location:settings");
            } else {
                return header("location:settings");
            }
        }
    }

    function updateEmail(){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userModel = new UserModel();
            $email = sanitizeOutput($_POST["email"]);
            $password = sanitizeOutput($_POST["password"]);
            $errors= [];

            if($email == "" ){
                array_push($errors,"Email Cannot be empty");

            }elseif( $userModel->isEmailValid( $email )){
                array_push($errors,"Email already in use use");

            }elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                array_push($errors,"Email must be valid");
                
            }elseif($password == ""){
                array_push($errors,"Password not be empty");

            }

            if(empty($errors)){
                $id = isset($_SESSION['id']) ? htmlspecialchars(trim($_SESSION['id'])) : '';

                if(($userModel->updateEmail($id,$email,$password))){
                    $_SESSION['success'] = ["Profile updated Sucessfully"];
                    return header("location:settings?");

                }else{
                    array_push($errors,"Password does not match");
                }
            }

            $_SESSION['errors'] = $errors;
            return header("location:settings");
        }

        return header("location:settings");
    }

    function updatePassword(){
        $current_password = sanitizeOutput($_POST["current_password"]);
        $new_password = sanitizeOutput($_POST["new_password"]);
        $confirm_password = sanitizeOutput($_POST["confirm_password"]);
        $errors = [];
        $id = isset($_SESSION['id']) ? htmlspecialchars(trim($_SESSION['id'])) : '';
        
        $userModel = new UserModel();
    
        // Check if passwords match
        if ($confirm_password != $new_password) {
            $errors[] = "Passwords do not match";
        }
    
        // Validate new password
        if (empty($new_password)) {
            $errors[] = "Password cannot be empty.";
        } 
        
        //Check if password is strong
        if (!preg_match('/^(?=.*\d)(?=.*[@#\-_$%^&+=§!\?])(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z@#\-_$%^&+=§!\?]{8,20}$/', $new_password)) {
            $errors[] = "Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one digit, and one special character. ".$new_password;
        }
    
        // Proceed if there are no errors
        if (empty($errors)) {
            if ($userModel->updatePassword($id, $current_password, $new_password)) {
                $_SESSION['success'] = ["Password updated successfully"];
                return header("location:settings?");
            } else {
                $errors[] = "Unable to change password. Check if the current password is correct.";
            }
        }
    
        $_SESSION['errors'] = $errors;
        return header("location:settings");
    }
    
    function setUserPrivilege() {
        // Initialize an array to hold errors
        $errors = [];
    
        // Sanitize the inputs to avoid malicious code
        $privilege = sanitizeOutput($_POST["privilege"]);
        $id = sanitizeOutput($_POST["user_id"]);
    
        // Validate that the privilege is either 'user' or 'moderator'
        if (!in_array($privilege, ['user', 'moderator'])) {
            // Add error to the errors array if the privilege is not valid
            $errors[] = "Error: Invalid privilege value. It must be either 'user' or 'moderator'.";
        }
    
        // Ensure user ID is a valid number and greater than 0
        if (!is_numeric($id) || $id <= 0) {
            // Add error if user ID is not valid
            $errors[] = "Error: Invalid user ID.";
        }
    
        // If there are any errors, redirect back with the errors
        if (!empty($errors)) {
            // Store errors in the session so they can be displayed on the previous page
            $_SESSION['errors'] = $errors;
    
            // Redirect back to the form page (you can replace this with the desired URL)
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit; // Stop further execution
        }
    
        // If no errors, proceed to set the user privilege using the UserModel
        try {
            $userModel = new UserModel();
            $userModel->setUserPrivilege($privilege, $id);
            
            // Optionally, add a success message if needed
            $_SESSION['success'] = "User privilege updated successfully.";
    
            // Redirect to a success page or back to the profile page
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        } catch (Exception $e) {
            // Handle any exceptions that may occur
            $_SESSION['errors'] = ["Error: Could not update privilege. Please try again later."];
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }
    
}