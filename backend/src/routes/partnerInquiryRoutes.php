<?php

require_once __DIR__ . '/../controllers/partnerInquiryController.php';

use Controllers\PartnerInquiryController;
use Middleware\VerifyUser;

$router->post('/partner-inquiry', [PartnerInquiryController::class, 'createInquiry']);
$router->get('/partner-inquiry', [PartnerInquiryController::class, 'getAllInquiries']);
$router->delete('/partner-inquiry', [PartnerInquiryController::class, 'deleteInquiry'], VerifyUser::class);
