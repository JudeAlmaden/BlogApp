<?php

class TagsModel {

  private $conn;

  // Establish a connection using PDO
  function connect() {
    if ($this->conn === null) {
      require("connect.php");  
      $this->conn = $conn;     
    }
    return $this->conn;
  }


  public function getAllTags() {
    $conn = $this->connect();
    $query = "SELECT * FROM tags"; 
    $stmt = $conn->prepare($query);
    $stmt->execute();


    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function searchByName($name) {
    $conn = $this->connect();

    // Adjust query to use LIKE with placeholders
    $query = "SELECT * FROM tags WHERE name LIKE :name LIMIT 7";  // Use a placeholder for the name

    // Prepare the query
    $stmt = $conn->prepare($query);

    // Bind the :name parameter to the $name value (using % for LIKE clause)
    $stmt->bindValue(':name', "%" . $name . "%", PDO::PARAM_STR);

    // Execute the query
    $stmt->execute();

    // Fetch all results as an associative array
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function insertBlogPostTags($blogPostId, $tagId) {
    try {
        $conn = $this->connect();
        
        // Prepare the SQL query to insert a new record into the pivot table 'post_tags'
        $query = "INSERT INTO blog_post_tags (blog_post_id, tag_id) VALUES (:blog_post_id, :tag_id)";
        $stmt = $conn->prepare($query);

        // Bind the parameters and execute the statement
        $stmt->execute([
            ':blog_post_id' => $blogPostId,
            ':tag_id' => $tagId,
        ]);

    } catch (PDOException $e) {
        echo "Error associating tag with post: " . $e->getMessage();
    }
  }

  public function deleteByBlogId($id){
    try {
      $conn = $this->connect();
      
      // Prepare the SQL query to insert a new record into the pivot table 'post_tags'
      $query = "DELETE FROM  blog_post_tag WHERE blog_post_id = :id";
      $stmt = $conn->prepare($query);

      // Bind the parameters and execute the statement
      $stmt->execute([
          ':id' => $id
      ]);

  } catch (PDOException $e) {
      echo "Error associating tag with post: " . $e->getMessage();
  }
}
}
