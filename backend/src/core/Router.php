<?php

namespace Core;

class Router
{
    private $routes = [];

    public function register($method, $path, $callback, $middleware = null)
    {
        $this->routes[strtoupper($method)][$this->formatPath($path)] = ['callback' => $callback, 'middleware' => $middleware];
    }

    public function get($path, $callback, $middleware = null)
    {
        $this->register('GET', $path, $callback, $middleware);
    }

    public function post($path, $callback, $middleware = null)
    {
        $this->register('POST', $path, $callback, $middleware);
    }

    public function patch($path, $callback, $middleware = null)
    {
        $this->register('PATCH', $path, $callback, $middleware);
    }

    public function put($path, $callback, $middleware = null)
    {
        $this->register('PUT', $path, $callback, $middleware);
    }

    public function delete($path, $callback, $middleware = null)
    {
        $this->register('DELETE', $path, $callback, $middleware);
    }

    private function formatPath($path)
    {
        return "/api" . (strpos($path, "/api") === 0 ? substr($path, 4) : $path);
    }

    public function resolve()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = $_SERVER['REQUEST_URI'];

        // Parse query string (e.g., ?id=1)
        $queryParams = [];
        if (($pos = strpos($path, '?')) !== false) {
            $queryString = substr($path, $pos + 1);
            parse_str($queryString, $queryParams);
            $path = substr($path, 0, $pos);
        }

        // Get POST and FILES data
        $postData = $_POST;  // Non-file form fields
        $fileData = $_FILES; // Files uploaded

        // Check if the request body contains JSON data
        $contentType = $_SERVER['CONTENT_TYPE'];
        if (strpos($contentType, 'application/json') !== false) {
            $jsonBody = json_decode(file_get_contents('php://input'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $postData = array_merge($postData, $jsonBody);
            }
        }

        // Combine POST and FILES data
        $data = array_merge($postData, $fileData) ?? [];

        // Check if the route exists for the given method and path
        if (isset($this->routes[$method][$path])) {
            $route = $this->routes[$method][$path];

            // Check and apply middleware if exists
            if (isset($route['middleware']) && is_string($route['middleware']) && class_exists($route['middleware'])) {
                error_log("Applying Middleware: " . print_r($route['middleware'], true));
                $data = $route['middleware']::handle($data);
                // If middleware returns an error, stop further execution
                if (isset($data['error'])) {
                    echo json_encode($data); // Return the error response
                    return;
                }
            }

            // Log incoming data for debugging
            error_log("Incoming data: " . print_r($data, true));

            // Call the controller callback
            echo call_user_func($route['callback'], $queryParams, $data);
        } else {
            // Handle route not found
            http_response_code(404);
            header("Content-Type: application/json");
            return Response::error(404, 'Route not found', ['Route not found']);
        }
    }
}
