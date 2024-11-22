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
                $this->setSession($user['id'], $user['name']);
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

    function setSession($id, $name){
        session_start();

        $_SESSION['id'] = $id;
        $_SESSION['name'] =$name;

        return;
    }

    function endSession(){
        session_unset();
        
        header('Location: index.php');
        return;
    }
}