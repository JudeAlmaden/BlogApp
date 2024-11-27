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
    try {
        $conn = $this->connect();

        // Check if any users exist
        $sqlCheck = "SELECT COUNT(*) FROM users";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->execute();
        $userCount = $stmtCheck->fetchColumn();

        // Set privilege to 'admin' if no users exist, otherwise set 'user' by default
        $privilege = ($userCount == 0) ? 'admin' : 'user';

        // Insert the new user
        $sql = "INSERT INTO users (name, email, password, privilege) 
                VALUES (:name, :email, :password, :privilege)";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
        $stmt->bindParam(':privilege', $privilege, PDO::PARAM_STR); // Bind privilege parameter

        if ($stmt->execute()) {
            return true;  // If the insertion was successful, return true
        } else {
            return false; // If the insertion failed, return false
        }
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
  public function updateProfile($id, $name = null, $bio = "", $gender, $img = null) {
  try {
      $conn = $this->connect();

      $sql = "UPDATE users SET bio = :bio, gender = :gender";
      if ($name !== null) {
          $sql .= ", name = :name";
      }
      if ($img !== null) {
          $sql .= ", profile_image = :profile_image";
      }
      $sql .= " WHERE id = :id";
      $stmt = $conn->prepare($sql);

      $stmt->bindParam(':bio', $bio, PDO::PARAM_STR);
      $stmt->bindParam(':gender', $gender, PDO::PARAM_STR);
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);

      if ($name !== null) {
          $stmt->bindParam(':name', $name, PDO::PARAM_STR);
      }
      if ($img !== null) {
          $stmt->bindParam(':profile_image', $img, PDO::PARAM_STR);
      }

      $stmt->execute();

      return true;
  } catch (PDOException $e) {
      error_log("Error updating profile: " . $e->getMessage());
      return false;
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
      $sql = "SELECT id,name, email, profile_image, bio, gender,profile_image,privilege,password FROM users WHERE id = :id";
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

  public function isUserAdmin($id){
    try {
        $conn = $this->connect();
        $sql = "SELECT 1 FROM users WHERE id = :id AND privilege = 'admin' OR privilege = 'moderator'";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        $stmt->execute();


        
        return $stmt->fetchColumn() ? true : false;
    } catch (PDOException $e) {
        error_log("Error checking admin status: " . $e->getMessage());
        return false; // Return false on failure to ensure safety.
    }
  }


    public function setUserPrivilege($privilege, $userId) {
      try {
          $conn = $this->connect(); // Assuming you have a connect method to get the database connection
          
          $sql = "UPDATE users SET privilege = :privilege WHERE id = :id";
          $stmt = $conn->prepare($sql);
          $stmt->bindParam(':privilege', $privilege, PDO::PARAM_STR);
          $stmt->bindParam(':id', $userId, PDO::PARAM_INT);

          // Execute query
          if ($stmt->execute()) {
              return ["status" => "success", "message" => "Privilege updated successfully."];
          } else {
              throw new Exception("Failed to update privilege.");
          }

      } catch (Exception $e) {
          // Return error message
          return ["status" => "error", "message" => $e->getMessage()];
      }
  }

  public function searchUsersByQuery($query) {
      try {
      $conn = $this->connect();
      // Escape special characters to prevent SQL injection (optional)
      $query = "%" . $query . "%";

      // Prepare the SQL query
      $sql = "SELECT * FROM users WHERE name LIKE :query OR email LIKE :query LIMIT 30";

      // Execute the query using a prepared statement
      $stmt = $this->conn->prepare($sql);
      $stmt->bindParam(':query', $query, PDO::PARAM_STR);
      $stmt->execute();

      // Fetch all matching users
      $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

      return $users;
      } catch (Exception $e) {
        // Return error message
        return ["status" => "error", "message" => $e->getMessage()];
    }
  }
}


