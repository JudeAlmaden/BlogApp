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
    $query = "SELECT * FROM tags WHERE name LIKE :name LIMIT 5";  // Use a placeholder for the name

    // Prepare the query
    $stmt = $conn->prepare($query);

    // Bind the :name parameter to the $name value (using % for LIKE clause)
    $stmt->bindValue(':name', "%" . $name . "%", PDO::PARAM_STR);

    // Execute the query
    $stmt->execute();

    // Fetch all results as an associative array
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
