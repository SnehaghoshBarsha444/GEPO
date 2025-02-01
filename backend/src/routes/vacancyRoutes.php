<?php

require_once __DIR__ . '/../controllers/vacancyController.php';

use Controllers\VacancyController;
use Middleware\VerifyUser;

$router->get('/vacancies', [VacancyController::class, 'getAllVacancies']);
$router->post('/vacancies', [VacancyController::class, 'createVacancy'], [VerifyUser::class]);
$router->patch('/vacancies', [VacancyController::class, 'updateVacancy'], [VerifyUser::class]);
$router->delete('/vacancies', [VacancyController::class, 'deleteVacancy'], [VerifyUser::class]);
