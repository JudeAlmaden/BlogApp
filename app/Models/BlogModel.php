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


  public function Search($keyword = "%", $categories, $tags, $date_from, $date_to, $offset = 0,$limit = 20) {
    $conn = $this->connect();
    $offset = $offset * $limit;
    // Base query
    $query = "
        SELECT 
            blog_posts.*,
            GROUP_CONCAT(DISTINCT tags.name ORDER BY tags.name) AS all_tags,
            GROUP_CONCAT(DISTINCT categories.name ORDER BY categories.name) AS all_categories,
            (
                SELECT 
                    blog_post_media.file_path 
                FROM 
                    blog_post_media 
                WHERE 
                    blog_post_media.blog_post_id = blog_posts.id 
                    AND blog_post_media.file_type = 'Image'
                ORDER BY 
                    blog_post_media.id ASC 
                LIMIT 1
            ) AS file_path
        FROM 
            blog_posts
        LEFT JOIN 
            blog_post_tags ON blog_posts.id = blog_post_tags.blog_post_id
        LEFT JOIN 
            tags ON tags.id = blog_post_tags.tag_id
        LEFT JOIN 
            blog_post_category ON blog_posts.id = blog_post_category.blog_post_id
        LEFT JOIN 
            categories ON categories.id = blog_post_category.category_id
        WHERE 
            (blog_posts.status = 'published'
            OR (blog_posts.scheduled_at > '0000-00-00 00:00:00' AND blog_posts.scheduled_at < NOW()))
    ";

    // Initialize conditions and parameters
    $conditions = [];
    $params = [];

    if (!empty($keyword)) {
        $conditions[] = "(blog_posts.title LIKE ? OR blog_posts.content LIKE ?)";
        $params[] = "%" . $keyword . "%";
        $params[] = "%" . $keyword . "%";
    }

    if (!empty($categories)) {
        $placeholders = str_repeat('?,', count($categories) - 1) . '?';
        $conditions[] = "categories.name IN ($placeholders)";
        $params = array_merge($params, $categories);
    }

    if (!empty($tags)) {
        $placeholders = str_repeat('?,', count($tags) - 1) . '?';
        $conditions[] = "tags.name IN ($placeholders)";
        $params = array_merge($params, $tags);
    }

    if (!empty($date_from)) {
        $conditions[] = "blog_posts.created_at >= ?";
        $params[] = $date_from;
    }

    if (!empty($date_to)) {
        $conditions[] = "blog_posts.created_at <= ?";
        $params[] = $date_to;
    }

    // Append conditions to query if any
    if (!empty($conditions)) {
        $query .= " AND " . implode(" AND ", $conditions);
    }

    // Add GROUP BY clause
    $query .= " GROUP BY blog_posts.id";

    // Add LIMIT and OFFSET for pagination
    $query .= " LIMIT $limit OFFSET $offset";

    // Prepare and execute the query
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    } else {
        return ["error" => "Query preparation failed."];
    }
}


}
?>
