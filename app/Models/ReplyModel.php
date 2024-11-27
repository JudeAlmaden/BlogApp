<?php

class ReplyModel {

  private $conn;

  function connect() {
    if ($this->conn === null) {
      require("connect.php");
      $this->conn = $conn;
    }
    return $this->conn;
  }

  function insertReply($user_id, $comment_id, $replyContent){
    try{
      $conn = $this->connect();

      $sql = "INSERT INTO  replies (comment_id, user_id, content) 
      VALUES(:comment_id, :user_id,:content)";

      $stmt = $conn->prepare($sql);

      $stmt->bindParam(':comment_id', $comment_id, PDO::PARAM_STR);
      $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
      $stmt->bindParam(':content', $replyContent, PDO::PARAM_STR);
      
      if ($stmt->execute()) {
        return true;  // If the insertion was successful, return true
      } else {
          return false; // If the insertion failed, return false
      }

      } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
  }


  function getRepliesByCommentId($comment_id, $offset) {
    try {
        // Set the offset to fetch the appropriate number of comments (e.g., 20 comments per page)
        $limit = 20;
        $offset = $offset * $limit; 

        $conn = $this->connect();

        // SQL query to select comments for the given post_id with pagination
        $sql = "SELECT 
                replies.id,
                replies.content,
                replies.created_at,
                users.name,
                users.profile_image
            FROM 
                replies
            INNER JOIN 
                users ON users.id = replies.user_id
            WHERE 
                replies.comment_id = :comment_id
            ORDER BY 
                replies.created_at DESC
            LIMIT 
                :offset, :limit;";

        $stmt = $conn->prepare($sql);
        
        // Bind the parameters
        $stmt->bindParam(':comment_id', $comment_id, PDO::PARAM_INT); 
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);   
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);      

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


  public function deleteReply($reply_id){
    try {
        $conn = $this->connect(); // Assuming you have a method that returns the DB connection       
        $query = "
            DELETE FROM replies 
            WHERE replies.id = :reply_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':reply_id', $reply_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return $stmt->rowCount() > 0;
        }
        
        return false;
    } catch (PDOException $e) {
        return "Error: " . $e->getMessage(); // Handle query error

    }
}
}


