<?php

require_once __DIR__ . '/../controllers/studyTourController.php';

use Controllers\StudyTourController;
use Middleware\VerifyUser;

$router->get('/all-winter-tours', [StudyTourController::class, 'getAllWinterTours']);
$router->get('/all-summer-tours', [StudyTourController::class, 'getAllSummerTours']);

$router->post('/study-tour', [StudyTourController::class, 'createStudyTour'], [VerifyUser::class]);
$router->patch('/study-tour', [StudyTourController::class, 'updateStudyTour'], [VerifyUser::class]);
$router->delete('/study-tour', [StudyTourController::class, 'deleteStudyTour'], [VerifyUser::class]);
