<?php
require_once("app/Controllers/User_Controller.php");
require_once("app/Controllers/Home_Controller.php");
require_once("app/Controllers/Blog_Controller.php");
require_once("app/Controllers/Tags_Controller.php");
require_once("app/Controllers/Media_Controller.php");
require_once("app/Controllers/Categories_Controller.php");
require_once("app/utils/sanitize.php");
require_once("router.php");

session_start();

// Create a new Router instance
$router = new Router();

//Login
$router->addRoute('/', 'UserController', 'login', 'GET');
$router->addRoute('/login', 'UserController', 'login', 'GET');
$router->addRoute('/login', 'UserController', 'login', 'POST');

//Register
$router->addRoute('/register', 'UserController', 'register', 'GET');
$router->addRoute('/register', 'UserController', 'register', 'POST');

//Homepage
$router->addRoute('/homepage', 'HomeController', 'index', 'GET');
$router->addRoute('/create-post', 'HomeController', 'createBlogFrom', 'GET');
$router->addRoute('/create-post', 'BlogController', 'createBlogPost', 'POST');

//Posts
$router->addRoute('/posts', 'HomeController', 'getPostList', 'GET');    //Just a route for view, Uses API route for Blog Controller to actually display
$router->addRoute('/view', 'BlogController', 'readPost', 'GET');


//API ROUTES
$router->addRoute('/api/get/tags/search', 'TagsController', 'search', 'GET');
$router->addRoute('/api/get/categories/search', 'CategoriesController', 'search', 'GET');
$router->addRoute('/api/get/search-posts', 'BlogController', 'search', 'GET');

//Logout
$router->addRoute('/logout', 'UserController', 'endSession', 'GET');

// Simulate request URI and method
$requestUri = $_SERVER['REQUEST_URI'];  // Actual request URI
$requestMethod = $_SERVER['REQUEST_METHOD'];  // Actual request method

// Dispatch request
$router->dispatch($requestUri, $requestMethod);
