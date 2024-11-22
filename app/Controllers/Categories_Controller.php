<?php

require_once("Controller.php");
require_once(__DIR__."/../Models/CategoriesModel.php");
class CategoriesController extends Controller{

    public function getAll() {
        $categoriesModel = new CategoriesModel();
        $categories = $categoriesModel->getAllCategories();  // Fetch categories

        // Return as JSON response
        header('Content-Type: application/json');
        echo json_encode($categories);
      }


      public function search() {
        $search = isset($_GET['search']) ? $_GET['search'] : '';

        $categoriesModel = new CategoriesModel();
        $categories = $categoriesModel->searchByName($search);  // Fetch categories

        // Return as JSON response
        header('Content-Type: application/json');
        echo json_encode($categories);
      }
}

?>