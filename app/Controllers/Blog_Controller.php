<?php

require_once("Controller.php");

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
    
            // Validate Fields
            $errors = [];
    
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
                $media_dir = __DIR__ . '/../uploads/media/';
                if (!is_dir($media_dir)) {
                    mkdir($media_dir, 0777, true);
                }
    
                foreach ($_FILES['media']['tmp_name'] as $key => $tmp_name) {
                    $file_name = basename($_FILES['media']['name'][$key]);
                    $file_type = mime_content_type($tmp_name);
                    $allowed_types = ['image/png', 'image/jpeg', 'video/mp4', 'audio/mpeg'];
    
                    if (in_array($file_type, $allowed_types)) {
                        $target_path = $media_dir . uniqid() . '_' . $file_name;
                        if (move_uploaded_file($tmp_name, $target_path)) {
                            $media_files[] = $target_path;
                        } else {
                            $errors[] = 'Failed to upload file: ' . $file_name;
                        }
                    } else {
                        $errors[] = 'Unsupported file type: ' . $file_name;
                    }
                }
            }
    
            // If errors exist, return to form with errors
            if (!empty($errors)) {
                $this->view('pages/create_post', ['errors'=>$errors]);
                exit;
            }else{
                header(header: 'homepage');
                exit;
            }
        }
    }
}

?>