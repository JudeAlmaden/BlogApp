<?php

require_once("Controller.php");
require_once(__DIR__."/../Models/UserModel.php");
class HomeController extends Controller{

    public function index(){
        $this->view('pages/homepage');
    }

    public function createBlogFrom(){
        $this->view('pages/create_post');
    }

    public function getPostList(){
        $this->view('pages/posts_list');
    }

    public function settings() {
        $userModel = new UserModel();
        $errors = [];
        $userID = $_SESSION['id'];
        $data = []; // Initialize the data array
    
        // Check if user is logged in, redirect if not
        if (!isset($userID)) {
            header('Location: /login');  // Redirect to login page if not logged in
            exit();
        }
    
        // Get user data by userID
        $user =  $userModel->getUserByID($userID);
    
        // Check if user data is found, if not, handle appropriately
        if ($user === false) {
            // Handle the case where user is not found
            $errors[] = 'User not found.';
        } else {
            // If user data is found, add it to the $data array
            $data['user'] = $user;
        }
    
        // Pass $data to the view
        $this->view('pages/settings', ["data"=>$data,'errors'=>$errors]);
    }

    public function myPosts(){
        $this->view('pages/my_posts');
    }

    public function viewProfile(){
        $userModel = new UserModel();
        $userID = (int)$_GET["id"];
 
    
        // Check if user is logged in, redirect if not
        if (!isset($userID)) {
            $_SESSION['errors']=["Cannot Find user"];
            header('Location: ');  
            exit();
        }

        $user=$userModel->getUserByID( $userID);

        if(!empty($user)){
            $this->view('pages/view_profile',["user"=>   $user]);
        }
        else{
        $_SESSION['errors']=["Cannot Find user"];
        header('Location: ');  
        exit;
        }
    }
}

?>