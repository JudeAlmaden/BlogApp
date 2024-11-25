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
    function uploadBlog($user_id, $title, $content, $status, $scheduled_at) {
        $conn = $this->connect(); // Assuming you have a method that returns the DB connection

        $query = "INSERT INTO blog_posts (user_id, title, content, status, scheduled_at, created_at, updated_at) 
                VALUES (:user_id, :title, :content, :status, :scheduled_at, NOW(), NOW())";

        try {
            $stmt = $conn->prepare($query);

            // Bind parameters
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':content', $content, PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':scheduled_at', $scheduled_at, PDO::PARAM_STR);

            // Execute the query
            if ($stmt->execute()) {;
                return $conn->lastInsertId();
            } else {
                return "Error: Could not insert the blog post.";
            }
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage(); // Query error
        }
    }

    function updateBlog($post_id, $user_id, $title, $content, $status, $scheduled_at) {
        $conn = $this->connect(); // Assuming you have a method that returns the DB connection
    
        // Update query for modifying an existing blog post
        $query = "
            UPDATE blog_posts 
            SET 
                title = :title,
                content = :content,
                status = :status,
                scheduled_at = :scheduled_at,
                updated_at = NOW()
            WHERE 
                id = :post_id AND user_id = :user_id";
    
        try {
            $stmt = $conn->prepare($query);
    
            // Bind parameters
            $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':content', $content, PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':scheduled_at', $scheduled_at, PDO::PARAM_STR);
    
            // Execute the query
            if ($stmt->execute()) {
                // Check if any rows were affected
                if ($stmt->rowCount() > 0) {
                    return "Blog post updated successfully.";
                } else {
                    return "No changes made or blog post not found.";
                }
            } else {
                return "Error: Could not update the blog post.";
            }
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage(); // Handle query error
        }
    }
    
    //For retrieving via id
    public function getById($id) {
        try{
            $conn = $this->connect();

            $query = "
            SELECT 
                blog_posts.*,
                users.name as author,
                users.id as user_id,
                GROUP_CONCAT(DISTINCT tags.name ORDER BY tags.name) AS all_tags,
                GROUP_CONCAT(DISTINCT categories.name ORDER BY categories.name) AS all_categories,
                GROUP_CONCAT(DISTINCT blog_post_media.file_Path ORDER BY blog_post_media.id) as media_url
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
            LEFT JOIN
                blog_post_media on blog_posts.id = blog_post_media.blog_post_id
            LEFT JOIN
                users on blog_posts.user_id = users.id
            WHERE 
                (blog_posts.status = 'published'
                OR (blog_posts.scheduled_at > '0000-00-00 00:00:00' AND blog_posts.scheduled_at < NOW()))
                AND blog_posts.id = ?
            GROUP BY 
                blog_posts.id";

            // Prepare the query
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                return ["error" => "Query preparation failed."];
            }

            // Bind the ID parameter and execute the query
            $stmt->execute([$id]);

            // Fetch the result
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if the blog post exists
            if ($result) {
                return $result;
            } else {
                return ["error" => "Blog post not found."];
            }
        }catch (PDOException $e) {
            // Handle exceptions
            return ["success" => false, "message" => "Error: " . $e->getMessage()];
        }
    }

    public function filteredSearch($keyword = "%", $categories = [], $tags = [], $date_from = null, $date_to = null, $offset = 0, $limit = 20, $sortBy = "date", $sortOrder = "desc", $status = "%", $user_id = null, $isAdmin = false, $author = "%") {
        $conn = $this->connect();
        $this->publishScheduledPost(); // Ensure scheduled posts are published
        $offset = $offset * $limit; // Calculate offset
    
        // Base query
        $query = "
            SELECT 
                blog_posts.id,
                blog_posts.title,
                blog_posts.content,
                blog_posts.status,
                blog_posts.created_at,
                blog_posts.likes,
                users.name,
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
            INNER JOIN
                users ON blog_posts.user_id = users.id
            WHERE 1
        ";
    
        // Initialize conditions and parameters
        $conditions = [];
        $params = [];
    
        if (!empty($user_id)) {
            $conditions[] = "(users.id = ?)";
            $params[] = $user_id;
        }
        if (!empty($author)) {
            $conditions[] = "(users.name LIKE ?)";
            $params[] = "%".$author."%";
        }
    
        if (!$isAdmin) {
            $conditions[] = "(blog_posts.status = 'published' OR (blog_posts.scheduled_at > '0000-00-00 00:00:00' AND blog_posts.scheduled_at < NOW()))";
        }
    
        if (!empty($status)) {
            $conditions[] = "(blog_posts.status = ?)";
            $params[] = $status;
        }
    
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
    
        // Validate sortBy and sortOrder
        $validSortByOptions = ['date', 'likes', 'updated'];
        $validSortOrderOptions = ['asc', 'desc'];
    
        if (!in_array($sortBy, $validSortByOptions)) {
            $sortBy = 'date';
        }
    
        if (!in_array($sortOrder, $validSortOrderOptions)) {
            $sortOrder = 'desc';
        }
    
        // Map sortBy to corresponding column
        $sortColumn = $sortBy === 'date' ? 'blog_posts.created_at' : ($sortBy === 'updated' ? 'blog_posts.updated_at' : 'blog_posts.likes');
    
        // Add ORDER BY clause
        $query .= " ORDER BY $sortColumn $sortOrder";
    
        // Add LIMIT and OFFSET for pagination
        $query .= " LIMIT $limit OFFSET $offset";
    
        $stmt = $conn->prepare($query);
    
        if ($stmt) {
            try {
                $stmt->execute($params);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $result;
            } catch (PDOException $e) {
                return ["error" => $e->getMessage()];
            }
        } else {
            return ["error" => "Query preparation failed."];
        }
    }

    public function publishScheduledPost() {
        try {
            // Establish database connection
            $conn = $this->connect();
    
            // SQL query to update scheduled posts
            $query = "UPDATE blog_posts
                      SET status = 'published'
                      WHERE blog_posts.scheduled_at > '0000-00-00 00:00:00'
                      AND blog_posts.scheduled_at < NOW();";
    
            // Prepare and execute the query
            $stmt = $conn->prepare($query);
            $result = $stmt->execute();
    
            // Check if rows were updated
            if ($result && $stmt->rowCount() > 0) {
                return ["success" => true, "message" => $stmt->rowCount() . " posts have been published."];
            } else {
                return ["success" => false, "message" => "No posts were updated."];
            }
        } catch (PDOException $e) {
            // Handle exceptions
            return ["success" => false, "message" => "Error: " . $e->getMessage()];
        }
    }
    
    public function isAuthor($user_id, $post_id) {
        try{
            $conn = $this->connect();

            $query = "
            SELECT 
                blog_posts.*,
                users.name as author,
                users.id as user_id,
                GROUP_CONCAT(DISTINCT tags.name ORDER BY tags.name) AS all_tags,
                GROUP_CONCAT(DISTINCT categories.name ORDER BY categories.name) AS all_categories,
                GROUP_CONCAT(DISTINCT blog_post_media.file_Path ORDER BY blog_post_media.id) as media_url
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
            LEFT JOIN
                blog_post_media on blog_posts.id = blog_post_media.blog_post_id
            LEFT JOIN
                users on blog_posts.user_id = users.id
            WHERE 
                users.id = ? AND blog_posts.id = ?
            GROUP BY 
                blog_posts.id";

            // Prepare the query
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                return ["error" => "Query preparation failed."];
            }

            // Bind the ID parameter and execute the query
            $stmt->execute([$user_id,$post_id]);

            // Fetch the result
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if the blog post exists
            if ($result) {
                return $result;
            } else {
                return ["error" => "Blog post not found."];
            }
        }catch (PDOException $e) {
            // Handle exceptions
            return ["success" => false, "message" => "Error: " . $e->getMessage()];
        }
    }
    
}
?>
