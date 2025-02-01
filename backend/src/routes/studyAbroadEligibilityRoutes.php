<?php

require_once __DIR__ . '/../controllers/studyAbroadEligibilityController.php';

use Controllers\StudyAbroadEligibilityController;

$router->get('/eligibility/queries', [StudyAbroadEligibilityController::class, 'getAllQueries']);
$router->post('/eligibility/queries', [StudyAbroadEligibilityController::class, 'createQuery']);
$router->delete('/eligibility/queries', [StudyAbroadEligibilityController::class, 'deleteQuery']);
