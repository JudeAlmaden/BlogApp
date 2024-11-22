<?php

class Controller {
    /**
     * Load a view file and pass data to it.
     *
     * @param string $file Path to the view file relative to the views directory.
     * @param array $data Optional associative array of data to pass to the view.
     */
    public function view($path, $data = []) {
        // Convert array keys to variables for use in the view
        extract($data);
        $base_url = "http://localhost/IntegrativeProgramming/finals/BlogWebApp";

        // Path to the view file
        $viewPath = __DIR__."/../Views/" . $path . ".php";

        // Check if the view file exists
        if (file_exists($viewPath)) {
            require $viewPath; // Load the view
        } else {
            echo "View file '$path' not found.";
        }
    }
}
