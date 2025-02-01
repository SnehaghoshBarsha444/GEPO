<?php

require_once __DIR__ . '/../controllers/facultyResearchExchangeController.php';

use Controllers\FacultyResearchExchangeController;
use Middleware\VerifyUser;

$router->get('/faculty-research-exchange', [FacultyResearchExchangeController::class, 'getAllFacultyResearchExchange']);
$router->post('/faculty-research-exchange', [FacultyResearchExchangeController::class, 'createFacultyResearchExchange'], [VerifyUser::class]);
$router->patch('/faculty-research-exchange', [FacultyResearchExchangeController::class, 'updateFacultyResearchExchange'], [VerifyUser::class]);
$router->delete('/faculty-research-exchange', [FacultyResearchExchangeController::class, 'deleteFacultyResearchExchange'], [VerifyUser::class]);
