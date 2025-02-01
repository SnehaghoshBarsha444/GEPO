<?php

require_once __DIR__ . '/../controllers/scholarshipController.php';

use Controllers\ScholarshipController;
use Middleware\VerifyUser;

$router->get('/scholarships', [ScholarshipController::class, 'getAllScholarships']);
$router->post('/scholarships', [ScholarshipController::class, 'createScholarship'], [VerifyUser::class]);
$router->patch('/scholarships', [ScholarshipController::class, 'updateScholarship'], [VerifyUser::class]);
$router->delete('/scholarships', [ScholarshipController::class, 'deleteScholarship'], [VerifyUser::class]);
