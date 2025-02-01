<?php

require_once __DIR__ . '/../controllers/successStoryController.php';

use Controllers\SuccessStoryController;
use Middleware\VerifyUser;

$router->get('/success-stories', [SuccessStoryController::class, 'getAllSuccessStories']);
$router->post('/success-stories', [SuccessStoryController::class, 'createSuccessStory'], [VerifyUser::class]);
$router->patch('/success-stories', [SuccessStoryController::class, 'updateSuccessStory'], [VerifyUser::class]);
$router->delete('/success-stories', [SuccessStoryController::class, 'deleteSuccessStory'], [VerifyUser::class]);
