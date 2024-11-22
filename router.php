<?php
class Router {
    private $routes = [];
    private $base_url = '/IntegrativeProgramming/finals/BlogWebApp'; // Set base URL

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

        // If no route matches
        echo "404 Not Found!";
        return;
    }

    // Call the controller's method
    private function callControllerMethod($controller, $method) {
        // Assuming controllers are properly included/loaded
        $controllerObj = new $controller();
        $controllerObj->$method();
    }
}
