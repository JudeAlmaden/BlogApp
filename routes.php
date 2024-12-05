<?php
require_once("app/Controllers/User_Controller.php");
require_once("app/Controllers/Home_Controller.php");
require_once("app/Controllers/Blog_Controller.php");
require_once("app/Controllers/Tags_Controller.php");
require_once("app/Controllers/Media_Controller.php");
require_once("app/Controllers/Comment_Controller.php");
require_once("app/Controllers/Reply_Controller.php");
require_once("app/Controllers/Categories_Controller.php");
require_once("app/utils/sanitize.php");
require_once("router.php");


function secureSessionRegeneration() {
    if (!isset($_SESSION['regenerated'])) {
        session_regenerate_id(true); // Replace the session ID and delete the old one
        $_SESSION['regenerated'] = time();
    } elseif (time() - $_SESSION['regenerated'] > 300) { // Regenerate every 5 minutes
        session_regenerate_id(true);
        $_SESSION['regenerated'] = time();
    }
}

function validateSession() {
    $currentUserAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $currentIp = $_SERVER['REMOTE_ADDR'] ?? '';

    if (!isset($_SESSION['user_agent'])) {
        $_SESSION['user_agent'] = $currentUserAgent;
    } elseif ($_SESSION['user_agent'] !== $currentUserAgent) {
        session_unset();
        session_destroy();
        header('Location: login.php'); // Redirect to login
        exit();
    }

    if (!isset($_SESSION['ip_address'])) {
        $_SESSION['ip_address'] = $currentIp;
    } elseif ($_SESSION['ip_address'] !== $currentIp) {
        session_unset();
        session_destroy();
        header('Location: login.php');
        exit();
    }
}

function enforceSessionTimeout($timeout = 1800) { // Default timeout: 30 minutes
    if (!isset($_SESSION['last_activity'])) {
        $_SESSION['last_activity'] = time();
    } elseif (time() - $_SESSION['last_activity'] > $timeout) {
        session_unset();
        session_destroy();
        header('Location: login.php'); // Redirect to login
        exit();
    }
    $_SESSION['last_activity'] = time();
}

function destroySession() {
    session_unset();   // Unset all session variables
    session_destroy(); // Destroy the session data on the server

    // Delete the session cookie
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
}

// Secure session configuration and management
$cookieParams = [
    'lifetime' => 0,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict',
];

session_set_cookie_params($cookieParams);
session_start();

secureSessionRegeneration();
validateSession();
enforceSessionTimeout();

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
$router->addRoute('/settings', 'HomeController', 'settings', 'GET');
$router->addRoute('/my-posts', 'HomeController', 'myPosts', 'GET');
$router->addRoute('/create-post', 'HomeController', 'createBlogFrom', 'GET');
$router->addRoute('/create-post', 'BlogController', 'createBlogPost', 'POST');
$router->addRoute('/view-profile', 'HomeController', 'viewProfile', 'GET');


//User
$router->addRoute('/update-profile', 'UserController', 'updateProfile', 'POST');
$router->addRoute('/update-email', 'UserController', 'updateEmail', 'POST');
$router->addRoute('/update-password', 'UserController', 'updatePassword', 'POST');
$router->addRoute('/set-user', 'UserController', 'setUserPrivilege', 'POST');
$router->addRoute('/search-users', 'HomeController', 'searchUsers', 'GET');

//Posts
$router->addRoute('/posts-lists', 'HomeController', 'getPostList', 'GET');    //Just a route for view, Uses API route for Blog Controller to actually display
$router->addRoute('/view', 'BlogController', 'readPost', 'GET');
$router->addRoute('/edit-post', 'BlogController', 'editPost', 'GET');
$router->addRoute('/edit-post', 'BlogController', 'editPost', 'POST');
$router->addRoute('/delete-post', 'BlogController', 'deletePost', 'GET');
$router->addRoute('/delete-comment', 'CommentController', 'deleteComment', 'GET');
$router->addRoute('/delete-reply', 'ReplyController', 'deleteReply', 'GET');

//API ROUTES
$router->addRoute('/api/get/tags/search', 'TagsController', 'search', 'GET');
$router->addRoute('/api/get/categories/search', 'CategoriesController', 'search', 'GET');
$router->addRoute('/api/get/search-posts', 'BlogController', 'search', 'GET');
$router->addRoute('/api/get/my-posts/search', 'BlogController', 'searchMyPosts', 'GET');
$router->addRoute('/api/create/comment', 'CommentController', 'createComment', 'GET');
$router->addRoute('/api/get/comment', 'CommentController', 'getComments', 'GET');
$router->addRoute('/api/create/reply', 'ReplyController', 'createReply', 'GET');
$router->addRoute('/api/get/reply', 'ReplyController', 'getReplies', 'GET');
$router->addRoute('/api/like/post', 'BlogController', 'likePost', 'GET');

//Logout
$router->addRoute('/logout', 'UserController', 'endSession', 'GET');

// Simulate request URI and method
$requestUri = $_SERVER['REQUEST_URI'];  // Actual request URI
$requestMethod = $_SERVER['REQUEST_METHOD'];  // Actual request method

// Dispatch request
$router->dispatch($requestUri, $requestMethod);
