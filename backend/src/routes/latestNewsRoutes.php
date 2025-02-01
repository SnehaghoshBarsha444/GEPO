<?php

require_once __DIR__ . '/../controllers/latestNewsController.php';

use Controllers\LatestNewsController;
use Middleware\VerifyUser;

$router->get('/latest-news', [LatestNewsController::class, 'getAllLatestNews']);
$router->post('/latest-news', [LatestNewsController::class, 'createLatestNews'], [VerifyUser::class]);
$router->patch('/latest-news', [LatestNewsController::class, 'updateLatestNews'], [VerifyUser::class]);
$router->delete('/latest-news', [LatestNewsController::class, 'deleteLatestNews'], [VerifyUser::class]);
