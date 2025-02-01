<?php

require_once __DIR__ . '/../controllers/FileController.php';

use Controllers\FileController;


$router->get("/image", [FileController::class, "getImage"]);
$router->get("/video", [FileController::class, "getVideo"]);
