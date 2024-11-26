<?php

require_once("Controller.php");
require_once(__DIR__."/../Models/CommentModel.php");
class CommentController extends Controller
{
    public function createComment() {
        $post_id = isset($_GET['id']) ? $_GET['id'] : null;
        $comment = isset($_GET['comment']) ? $_GET['comment'] : '';
        $user_id = $_SESSION['id'];
    
        if (empty($post_id) || empty($comment)) {
            echo json_encode(['status' => 'error', 'message' => 'Post ID or comment is missing']);
            return;
        }
    
        // Insert the comment
        $commentModel = new CommentModel();
        $result = $commentModel->insertComment($user_id, $post_id, $comment);

        header('Content-Type: application/json');
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Comment added successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add comment']);
        }
    }
    public function getComments() {
        // Retrieve the post_id and index from the GET request
        $post_id = isset($_GET['id']) ? $_GET['id'] : null;
        $index = isset($_GET['index']) ? (int)$_GET['index'] : 0;
    
        // Check if the post_id is provided
        if (empty($post_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Post ID is missing']);
            return;
        }
    
        // Instantiate the CommentModel and fetch the comments
        $commentModel = new CommentModel();
        $comments = $commentModel->getCommentsByPostId($post_id, $index); // Fetch comments by post_id and index
        
        // Set the content type to JSON for the response
        header('Content-Type: application/json');
        
        if ($comments) {
            // Check if there are more comments to load (assuming 20 comments per page)
            $has_more = count($comments) === 20;  // If exactly 20 comments are returned, more might exist
            
            // Return the comments and the "has_more" flag
            echo json_encode([
                'status' => 'success',
                'message' => 'Comments fetched successfully',
                'data' => $comments,
                'has_more' => $has_more // Add this flag to indicate whether there are more comments
            ]);
        } else {
            // If no comments exist, return an error message
            echo json_encode(['status' => 'error', 'message' => 'No comments found for this post']);
        }
    }
    
}