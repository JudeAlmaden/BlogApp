<?php

class CommentModel {

  private $conn;

  function connect() {
    if ($this->conn === null) {
      require("connect.php");
      $this->conn = $conn;
    }
    return $this->conn;
  }

  public function insertComment($user_id, $post_id, $content) {
    try {
        $conn = $this->connect();

        // Step 1: Check if the blog post is published
        $checkSql = "SELECT id FROM blog_posts WHERE id = :post_id AND status = 'published'";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        $checkStmt->execute();
        $post = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if (!$post) {
            // If the blog post doesn't exist or is not published
            return ['status' => 'error', 'message' => 'Cannot add comment. Blog post is not published.'];
        }

        // Step 2: Insert the comment
        $insertSql = "INSERT INTO comments (blog_post_id, user_id, content) 
                      VALUES (:post_id, :user_id, :content)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        $insertStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $insertStmt->bindParam(':content', $content, PDO::PARAM_STR);

        if ($insertStmt->execute()) {
            // If the insertion is successful
            return ['status' => 'success', 'message' => 'Comment added successfully'];
        } else {
            // If the insertion failed
            return ['status' => 'error', 'message' => 'Failed to add comment'];
        }

    } catch (PDOException $e) {
        // Handle database errors
        return ['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()];
    }
  }


  function getCommentsByPostId($post_id, $offset) {
    try {
        // Set the offset to fetch the appropriate number of comments (e.g., 20 comments per page)
        $limit = 20;
        $offset = $offset * $limit;  // This will calculate the correct offset based on the page number

        $conn = $this->connect();

        // SQL query to select comments for the given post_id with pagination
        $sql = "SELECT 
                    comments.id,
                    comments.content,
                    comments.created_at,
                    users.name,
                    users.profile_image,
                    COALESCE(COUNT(replies.id), 0) AS responses
                FROM 
                    comments
                INNER JOIN 
                    users ON users.id = comments.user_id
                LEFT JOIN 
                    replies ON replies.comment_id = comments.id
                WHERE 
                    comments.blog_post_id = :post_id
                GROUP BY 
                    comments.id, users.id
                LIMIT 
                    :offset, :limit;
                ";

        $stmt = $conn->prepare($sql);
        
        // Bind the parameters
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);  // Use PARAM_INT for post_id
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);    // Use PARAM_INT for offset
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);      // Use PARAM_INT for limit (number of comments per request)

        // Execute the query
        $stmt->execute();

        // Fetch all the results as an associative array
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return the result (an array of comments) or false if no comments are found
        return $result ? $result : false;

    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;  // Return false if there's an error
    }
  }



}


