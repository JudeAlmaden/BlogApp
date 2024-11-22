<?php

require_once("Controller.php");
require_once(__DIR__."/../Models/BlogModel.php");
require_once(__DIR__."/../Models/MediaModel.php");
class BlogController extends Controller{

    public function createBLogPost()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Sanitize Input Data
            $title = htmlspecialchars(trim($_POST['title'] ?? ''));
            $content = htmlspecialchars(trim($_POST['content'] ?? ''));
            $categories = json_decode($_POST['categories'][0] ?? '[]', true);
            $tags = json_decode($_POST['tags'][0] ?? '[]', true);
            $scheduled_at = $_POST['scheduled_at'] ?? null;
            $user_id = $_SESSION['id'];
            $status = htmlspecialchars(trim($_POST['action'] ?? ''));

            // Map the status based on the button clicked
            if ($status === 'save') {
                $status = 'draft'; // Status for "Save Post"
            } elseif ($status === 'publish') {
                $status = 'published'; // Status for "Publish Post"
            } else {
                $status = 'unknown'; // Fallback status
            }
            
            // Example usage:
            echo "Post status: $status";
            
            $errors = [];

            if (empty($user_id)) {
                $errors[] = 'Must be logged in required.';
            }

            if (empty($title)) {
                $errors[] = 'Title is required.';
            }
    
            if (empty($content)) {
                $errors[] = 'Content is required.';
            }
    
            if (empty($categories) || !is_array($categories)) {
                $errors[] = 'At least one category must be selected.';
            }
    
            if (empty($tags) || !is_array($tags)) {
                $errors[] = 'At least one tag must be selected.';
            }

            if (!empty($scheduled_at) && !strtotime($scheduled_at)) {
                $errors[] = 'Scheduled date and time are invalid.';
            }
    
            // 2. Handle File Uploads
            $media_files = [];
            if (!empty($_FILES['media']['name'][0])) {
                $media_dir = __DIR__ . '/../../public/uploads/media/';
                
                // Create directory if it doesn't exist
                if (!is_dir($media_dir)) {
                    mkdir($media_dir, 0777, true);
                }
            
                // Initialize fileinfo resource
                $finfo = finfo_open(FILEINFO_MIME_TYPE); // Open fileinfo resource for MIME type detection
                
                foreach ($_FILES['media']['tmp_name'] as $key => $tmp_name) {
                    $file_name = basename($_FILES['media']['name'][$key]);
                    
                    // Use finfo_file to get the MIME type
                    $file_type = finfo_file($finfo, $tmp_name);
            
                    // If MIME type is still null or invalid, fallback to file extension
                    if ($file_type === false) {
                        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
                        $file_type = ($file_extension == 'mp4') ? 'video/mp4' :
                                     (($file_extension == 'jpeg' || $file_extension == 'jpg') ? 'image/jpeg' : 'image/png');
                    }
            
                    // Map MIME types to 'image', 'video', 'audio', or 'other'
                    $mapped_type = 'other'; // Default value if not matching
                    if (strpos($file_type, 'image') !== false) {
                        $mapped_type = 'image';
                    } elseif (strpos($file_type, 'video') !== false) {
                        $mapped_type = 'video';
                    } elseif (strpos($file_type, 'audio') !== false) {
                        $mapped_type = 'audio';
                    }
            
                    // Allowed MIME types
                    $allowed_types = ['image/png', 'image/jpeg', 'video/mp4', 'audio/mpeg'];
            
                    // Check if the MIME type is valid
                    if (in_array($file_type, $allowed_types)) {
                        $target_path = $media_dir . uniqid() . '_' . $file_name;
                        if (move_uploaded_file($tmp_name, $target_path)) {
                            // Store both the file path and mapped type
                            $media_files[] = [
                                'path' => $target_path,
                                'type' => $mapped_type // Insert the mapped type here
                            ];
                        } else {
                            $errors[] = 'Failed to upload file: ' . $file_name;
                        }
                    } else {
                        $errors[] = 'Unsupported file type: ' . $file_name;
                    }
                }
            
                // Close fileinfo resource after use
                finfo_close($finfo);
            }
            
            // If errors exist, return to form with errors
            if (!empty($errors)) {
                $this->view('pages/create_post', ['errors'=>$errors]);
                exit;
            }else{

                // Upload the post
                $blogsModel = new BlogModel();
                $tagsModel = new TagsModel();
                $mediaModel = new MediaModel();
                $categoriesModel = new CategoriesModel();

                $blog_post_id = $blogsModel->uploadBlog($user_id,$title,$content,$categories,$status,$scheduled_at);

                foreach($tags as $tag){
                    $tagsModel->insertBlogPostTags($blog_post_id,$tag['id']);
                }
                foreach($categories as $category){
                    $categoriesModel->insertBlogPostCategories($blog_post_id,$category['id']);
                }
                foreach($media_files as $media_file){
                    $mediaModel->insertBlogPostMedia($blog_post_id,$media_file['path'],$media_file['type']);
                }

                $_SESSION['success'] = ["Success"];
                echo  $_SESSION['success'][0];
                header('location:homepage');
                exit;
            }
        }
    }
}
?>