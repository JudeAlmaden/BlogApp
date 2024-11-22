<?php

require_once("Controller.php");
require_once(__DIR__."/../Models/TagsModel.php");

class TagsController extends Controller{

    public function getAll() {        
        $tagsModel = new TagsModel();
        $tags = $tagsModel->getAllTags();  // Fetch tags
        echo json_encode($tags);  // Return as JSON response

        header('Content-Type: application/json');
        echo json_encode($tags);
      }

      public function search() {      
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $tagsModel = new TagsModel();
        $tags = $tagsModel->searchByName($search);  // Fetch tags

        // Return as JSON response
        header('Content-Type: application/json');
        echo json_encode($tags);
      }
}

?>