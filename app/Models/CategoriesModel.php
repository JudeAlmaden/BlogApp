<?php

class CategoriesModel {

  private $conn;

  // Establish a connection using PDO
  function connect() {
    if ($this->conn === null) {
      require("connect.php");  // Assuming connect.php contains the PDO connection setup
      $this->conn = $conn;     // $conn is the PDO instance created in connect.php
    }
    return $this->conn;
  }

  // Method to fetch all categories
  public function getAllCategories() {
    $conn = $this->connect();
    $query = "SELECT * FROM categories";  // Adjust the query as needed

    // Prepare the query and execute it using PDO
    $stmt = $conn->prepare($query);
    $stmt->execute();

    // Fetch all results as an associative array
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function searchByName($name) {
    $conn = $this->connect();

    // Adjust query to use LIKE with placeholders
    $query = "SELECT * FROM categories WHERE name LIKE :name LIMIT 5";  // Use a placeholder for the name

    // Prepare the query
    $stmt = $conn->prepare($query);

    // Bind the :name parameter to the $name value (using % for LIKE clause)
    $stmt->bindValue(':name', "%" . $name . "%", PDO::PARAM_STR);

    // Execute the query
    $stmt->execute();

    // Fetch all results as an associative array
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function insertBlogPostCategories($blog_post_id, $categoryId) {
    try {
        $conn = $this->connect();
        
        // Prepare the SQL query to insert a new record into the pivot table 'post_tags'
        $query = "INSERT INTO blog_post_category (blog_post_id, category_id) VALUES (:blog_post_id, :categoryId)";
        $stmt = $conn->prepare($query);

        // Bind the parameters and execute the statement
        $stmt->execute([
            ':blog_post_id' => $blog_post_id,
            ':categoryId' => $categoryId,
        ]);

    } catch (PDOException $e) {
        echo "Error associating tag with post: " . $e->getMessage();
    }
  }

  public function deleteByBlogId($id){
      try {
        $conn = $this->connect();
        
        // Prepare the SQL query to insert a new record into the pivot table 'post_tags'
        $query = "DELETE FROM  blog_post_category WHERE blog_post_id = :id";
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
