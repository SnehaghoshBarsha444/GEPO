<?php
// CORS Headers
header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

// Handle preflight (OPTIONS) requests
if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    http_response_code(200);
    exit;
}

// Load environment variables
require_once __DIR__ . "/../src/core/LoadEnv.php";
(new \Core\LoadEnv())->load();

// Load JWT Handler
require_once __DIR__ . "/../src/core/JwtHandler.php";

use Core\Jwt;

Jwt::init();


// Load dependencies
require_once "../src/core/Router.php";
require_once "../src/core/Response.php";
require_once "../src/core/Database.php";
require_once "../src/middleware/FileUpload.php";
require_once "../src/middleware/VerifyUser.php";

// Set error reporting to E_ALL to catch all types of errors
// error_reporting(E_ALL);


// use Core\Response;
// // Set a custom error handler function
// function errorHandler($errno, $errstr, $errfile, $errline)
// {
//     // Handle the error here
//     Response::error(500, 'Internal Server Error', ['error' => $errstr, 'file' => $errfile, 'line' => $errline]);
//     exit;
// }

// // Set the custom error handler function
// set_error_handler('errorHandler');

// // Set a shutdown function to catch any fatal errors
// function shutdownFunction()
// {
//     $error = error_get_last();
//     if ($error['type'] === E_ERROR) {
//         // Handle the fatal error here
//         Response::error(500, 'Internal Server Error', ['error' => $error['message'], 'file' => $error['file'], 'line' => $error['line']]);
//         exit;
//     }
// }

// Set the shutdown function
// register_shutdown_function('shutdownFunction');

use Core\Router;

$router = new Router();

require_once "../src/routes/api.php"; // Load routes

$router->resolve();
