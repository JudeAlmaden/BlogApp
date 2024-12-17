<?php
class Router {
    private $routes = [];
    private $base_url;

    public function __construct() {
        // Remove the absolute document root and leave a clean relative path
        $script_path = str_replace('\\', '/', __DIR__); // Normalize slashes for Windows/Linux compatibility
        $document_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
        $relative_path = str_replace($document_root, '', $script_path);
    
        // Ensure the relative path starts clean
        $relative_path = ltrim($relative_path, '/');
    
        $this->base_url = "/".$relative_path ;
    }

    public function getBaseUrl() {
        return $this->base_url;
    }

    // Add a route
    public function addRoute($url, $controller, $method, $requestMethod) {
        $this->routes[] = [
            'url' => $url,
            'controller' => $controller,
            'method' => $method,
            'requestMethod' => strtoupper($requestMethod) // Normalize request method to uppercase
        ];
    }

    // Dispatch the request
    public function dispatch($requestUri, $requestMethod) {
        // Remove the query string from the URL for comparison
        $urlWithoutQuery = strtok($requestUri, '?');

        foreach ($this->routes as $route) {
            // Compare base URL + route with the URL without query parameters
            $routeUrl = $this->base_url . $route['url'];

            // Check if the URL matches the request URI and method
            if ($urlWithoutQuery === $routeUrl && strtoupper($requestMethod) === $route['requestMethod']) {
                $this->callControllerMethod($route['controller'], $route['method']);
                return;
            }
        }

        echo $this->base_url;
        exit;

        // If no route matches
        echo'
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>404 Page Not Found</title>
            <style>
                /* Reset and Basic Styles */
                body, html {
                    margin: 0;
                    padding: 0;
                    font-family: Arial, sans-serif;
                    height: 100%;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    background: #f7f7f7;
                }

                /* Centering Container */
                .container {
                    text-align: center;
                    background: #fff;
                    padding: 30px;
                    border-radius: 10px;
                    box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
                    max-width: 600px;
                    width: 100%;
                }

                /* 404 Number */
                .error-code {
                    font-size: 120px;
                    font-weight: bold;
                    color: #ff6b6b;
                    margin-bottom: 20px;
                }

                /* Title */
                h1 {
                    font-size: 36px;
                    color: #333;
                }

                /* Description Text */
                p {
                    font-size: 18px;
                    color: #555;
                    margin-bottom: 30px;
                }

                /* Button */
                .home-btn {
                    display: inline-block;
                    padding: 10px 20px;
                    background-color: #ff6b6b;
                    color: #fff;
                    text-decoration: none;
                    font-size: 18px;
                    border-radius: 5px;
                    transition: background-color 0.3s ease;
                }

                .home-btn:hover {
                    background-color: #ff3b3b;
                }

                /* Decorative illustration */
                .illustration {
                    margin-top: 30px;
                    width: 200px;
                    height: 200px;
                    background: url("https://img.icons8.com/ios/452/404.png") center/contain no-repeat;
                    margin-bottom: 20px;
                }
            </style>
        </head>
        <body>

            <div class="container">
                <div class="illustration"></div> <!-- Illustration Image -->
                <div class="error-code">404</div>
                <h1>Oops! Page Not Found</h1>
                <p>Sorry, the page you are looking for does not exist or has been moved.</p>
                <a href="http://localhost/IntegrativeProgramming/finals/BlogWebApp/" class="home-btn">Go Back to Home</a>
            </div>

        </body>
        </html>
        ';
        return;
    }

    // Call the controller's method
    private function callControllerMethod($controller, $method) {
        // Assuming controllers are properly included/loaded
        $controllerObj = new $controller();
        $controllerObj->$method();
    }
}
