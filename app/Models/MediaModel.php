<?php

class MediaModel {

  private $conn;

  function connect() {
    if ($this->conn === null) {
      require("connect.php");
      $this->conn = $conn;
    }
    return $this->conn;
  }

  public function insertBlogPostMedia($blogPostId, $filePath, $fileType) {
    try {
        $conn = $this->connect();
        
        // Prepare the SQL query to insert a new record into the 'blog_post_media' table
        $query = "INSERT INTO blog_post_media (blog_post_id, file_path, file_type, created_at, updated_at) 
                  VALUES (:blog_post_id, :file_path, :file_type, NOW(), NOW())";
        $stmt = $conn->prepare($query);

        // Bind the parameters and execute the statement
        $stmt->execute([
            ':blog_post_id' => $blogPostId,
            ':file_path' => $filePath,
            ':file_type' => $fileType,
        ]);

    } catch (PDOException $e) {
        echo "Error associating media with post: " . $e->getMessage();
    }
  }
}
