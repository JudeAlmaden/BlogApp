<?php

require_once("Controller.php");
require_once(__DIR__."/../Models/ReplyModel.php");
require_once(__DIR__."/../Models/BlogModel.php");
class ReplyController extends Controller
{
    public function createReply(){
        $comment_id = isset($_GET['comment_id']) ? $_GET['comment_id'] : null;
        $reply_content = isset($_GET['reply_content']) ? $_GET['reply_content'] : '';
        $user_id = $_SESSION['id'];
    
        if (empty($reply_content) || empty($comment_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Missing content or comment ID']);
            return;
        }
    
        // Insert the comment
        $replyController = new ReplyModel();
        $result = $replyController->insertReply($user_id, $comment_id, $reply_content);

        header('Content-Type: application/json');
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Comment added successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add comment']);
        }
    }


    public function getReplies() {
        // Retrieve the post_id and index from the GET request
        $comment_id = isset($_GET['comment_id']) ? $_GET['comment_id'] : null;
        $index = isset($_GET['index']) ? (int)$_GET['index'] : 0;
    
        // Check if the post_id is provided
        if (empty($comment_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Post ID is missing']);
            return;
        }
    
        // Instantiate the CommentModel and fetch the comments
        $replyModel = new ReplyModel();
        $replies = $replyModel->getRepliesByCommentId($comment_id, $index); // Fetch comments by post_id and index
        
        // Set the content type to JSON for the response
        header('Content-Type: application/json');
        
        if ($replies) {
            // Check if there are more comments to load (assuming 20 comments per page)
            $has_more = count($replies) === 20;  // If exactly 20 comments are returned, more might exist
            
            // Return the comments and the "has_more" flag
            echo json_encode([
                'status' => 'success',
                'message' => 'Comments fetched successfully',
                'data' => $replies,
                'has_more' => $has_more // Add this flag to indicate whether there are more comments
            ]);
        } else {
            // If no comments exist, return an error message
            echo json_encode(['status' => 'error', 'message' => 'No comments found for this post']);
        }
    }

    public function deleteReply(){
        $reply_id = isset($_GET['id']) ? htmlspecialchars($_GET['id'], ENT_QUOTES) : '';
        $post_id = isset($_GET['post_id']) ? htmlspecialchars($_GET['post_id'], ENT_QUOTES) : '';
        $user_id = $_SESSION['id'];

        $errors = [];

        if (empty($user_id)) {
            $errors = 'Must be logged in required.';
        }
        if (empty($reply_id)) {
            $errors = 'Comment must exist';
        }
        if (empty($id) || !is_numeric($id)) {
            $errors = "Post not valid";
        }

        
        if(!empty($errors)){
            $replyModel = new ReplyModel();
            $blogModel = new BlogModel();
            //Attempt to delete the post
            if($_SESSION['privilege'] === 'admin' || $_SESSION['privilege'] === 'moderator' || $blogModel->isUserAuthor($user_id,$post_id)){
                $replyModel->deleteReply($reply_id);
                $_SESSION['success'] = ["Successfully deleted post"];
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit;
            }

            $_SESSION['errors'] = ["Error: Check if post exsist or privilege is author or admin"];
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }else{
            $_SESSION['errors'] = $errors;
            header("location:home"); 
            exit();
        }

    }
}