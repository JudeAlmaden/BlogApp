<?php

class UserModel {

  private $conn;

  function connect() {
    if ($this->conn === null) {
      require("connect.php");
      $this->conn = $conn;
    }
    return $this->conn;
  }

  function isEmailValid($email) {
    try{
      $conn = $this->connect();

      $sql = "SELECT * FROM users WHERE email = :email ";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':email', $email, PDO::PARAM_STR);
      $stmt->execute();
      
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      return $result;

      } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
  }
  
  function insertUser($name, $email, $password) {
    try{
      $conn = $this->connect();

      $sql = "INSERT INTO  users (name,email,password) 
      VALUES(:name, :email,:password)";

      $stmt = $conn->prepare($sql);

      $stmt->bindParam(':name', $name, PDO::PARAM_STR);
      $stmt->bindParam(':email', $email, PDO::PARAM_STR);
      $stmt->bindParam(':password', password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
      
      $stmt->execute();

      return;

      } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
  }

  function getUserLogin($email, $password) {
    try {
        $conn = $this->connect();

        // Query to fetch user by email
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // If user exists, verify the password
        if ($result && password_verify($password, $result['password'])) {
            return $result;
        } else {
            return false;  // Invalid email or password
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    return false;
  }

}
