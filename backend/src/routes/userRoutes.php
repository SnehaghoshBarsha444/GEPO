<?php
require_once __DIR__ . '/../controllers/UserController.php';

use Controllers\UserController;
use Middleware\VerifyUser;

$router->get("/users", [UserController::class, "getUser"], VerifyUser::class);
$router->post("/users", [UserController::class, "createUser"]);
$router->post("/login", [UserController::class, "login"]);
$router->post("/logout", [UserController::class, "logout"], VerifyUser::class);
$router->patch("/users", [UserController::class, "updateUserData"], VerifyUser::class);
$router->patch("/users/password", [UserController::class, "updatePassword"], VerifyUser::class);
$router->delete("/users", [UserController::class, "deleteUser"], VerifyUser::class);
