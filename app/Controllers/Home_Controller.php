<?php

require_once("Controller.php");

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
}

?>