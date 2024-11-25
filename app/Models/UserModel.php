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

 // Method to update profile (bio, gender, and image)
  public function updateProfile($id, $bio, $gender, $img = null) {
    try {
        $conn = $this->connect();

        // Base SQL query for updating bio and gender
        $sql = "UPDATE users SET bio = :bio, gender = :gender";

        // If $img is not null, add the profile_image update to the SQL query
        if ($img !== null) {
            $sql .= ", profile_image = :profile_image";
        }

        // Complete the query with the WHERE clause
        $sql .= " WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        // Bind the parameters for bio, gender, and id
        $stmt->bindParam(':bio', $bio, PDO::PARAM_STR);
        $stmt->bindParam(':gender', $gender, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // If a new image is provided, bind the image parameter
        if ($img !== null) {
            $stmt->bindParam(':profile_image', $img, PDO::PARAM_STR);
        }

        // Execute the query
        $stmt->execute();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
  }

  // Method to update email (requires password verification)
  public function updateEmail($id, $newEmail, $password) {
    try {
      $conn = $this->connect();  // Make sure the connection is established

      // First, verify the password before updating the email
      $sql = "SELECT password FROM users WHERE id = :id";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      // Check if password matches
      if ($user && password_verify($password, $user['password'])) {
        // Password matches, proceed to update the email
        $sql = "UPDATE users SET email = :email WHERE id = :id";
        $stmt = $conn->prepare($sql);

        // Bind the parameters
        $stmt->bindParam(':email', $newEmail, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();
        return true;
        
      } else {
        return false; // Return false if password does not match
      }
    } catch(PDOException $e) {
      // Log or display the error
      echo "Error: " . $e->getMessage();
    }
  }

  // Method to get user details by ID
  public function getUserByID($id) {
    try {
      $conn = $this->connect();  // Ensure connection is established
      
      // SQL query to fetch name, email, profile_image, and password
      $sql = "SELECT name, email, profile_image, bio, gender,profile_image password FROM users WHERE id = :id";
      $stmt = $conn->prepare($sql);

      // Bind the ID parameter
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);

      // Execute the query
      $stmt->execute();

      // Fetch the user data
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      // If no user is found, return false
      if (!$user) {
        return false;
      }

      // Return the user data
      return $user;
    } catch(PDOException $e) {
      // Log or display the error
      echo "Error: " . $e->getMessage();
    }
  }
  
  public function updatePassword($id, $password, $newPassword) {
    try {
        // Establish database connection
        $conn = $this->connect();

        // Step 1: Verify the current password
        $sql = "SELECT password FROM users WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            // User not found
            return false;
        }

        // Verify the current password
        if (!password_verify($password, $result['password'])) {
            return false; // Current password does not match
        }

        // Step 2: Update the password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateSql = "UPDATE users SET password = :newPassword WHERE id = :id";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bindParam(':newPassword', $hashedPassword, PDO::PARAM_STR);
        $updateStmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($updateStmt->execute()) {
            return true; // Password updated successfully
        } else {
            return false; // Update failed
        }
    } catch (PDOException $e) {
        // Log or display the error as needed
        error_log("Error in updatePassword: " . $e->getMessage());
        return false; // Return false in case of any error
    }
  }

}


