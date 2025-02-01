<?php

require_once __DIR__ . '/../controllers/contactApplicationController.php';

use Controllers\ContactApplicationController;
use Middleware\VerifyUser;

$router->post('/contact-application', [ContactApplicationController::class, 'createApplication']);
$router->get('/contact-application', [ContactApplicationController::class, 'getAllApplications']);
$router->delete('/contact-application', [ContactApplicationController::class, 'deleteApplication'], VerifyUser::class);
