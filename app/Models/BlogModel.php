<?php

class BlogModel {

  private $conn;

  // Establish a database connection using PDO
  function connect() {
    if ($this->conn === null) {
      require("connect.php");  // Assuming connect.php contains the PDO connection setup
      $this->conn = $conn;     // $conn is the PDO instance created in connect.php
    }
    return $this->conn;
  }

  // Upload a new blog post
  function uploadBlog($user_id, $title, $content, $category_id, $status, $scheduled_at) {
    $conn = $this->connect(); // Assuming you have a method that returns the DB connection

    $query = "INSERT INTO blog_posts (user_id, title, content, category_id, status, scheduled_at, created_at, updated_at) 
              VALUES (:user_id, :title, :content, :category_id, :status, :scheduled_at, NOW(), NOW())";

    try {
        $stmt = $conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':content', $content, PDO::PARAM_STR);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':scheduled_at', $scheduled_at, PDO::PARAM_STR);

        // Execute the query
        if ($stmt->execute()) {
            // Get the ID of the last inserted record
            $blogPostId = $conn->lastInsertId();
            return $blogPostId; // Return the ID of the newly inserted blog post
        } else {
            return "Error: Could not insert the blog post.";
        }
    } catch (PDOException $e) {
        return "Error: " . $e->getMessage(); // Query error
    }
}

}
?>
