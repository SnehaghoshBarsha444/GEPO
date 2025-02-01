<?php

require_once __DIR__ . '/../controllers/studyAbroadProgramController.php';

use Controllers\StudyAbroadProgramController;
use Middleware\VerifyUser;

$router->get('/study-abroad-programs', [StudyAbroadProgramController::class, 'getAllPrograms']);
$router->post('/study-abroad-programs', [StudyAbroadProgramController::class, 'createProgram'], [VerifyUser::class]);
$router->patch('/study-abroad-programs', [StudyAbroadProgramController::class, 'updateProgram'], [VerifyUser::class]);
$router->delete('/study-abroad-programs', [StudyAbroadProgramController::class, 'deleteProgram'], [VerifyUser::class]);
