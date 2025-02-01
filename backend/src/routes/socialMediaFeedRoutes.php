<?php

require_once __DIR__ . '/../controllers/socialMediaFeedController.php';

use Controllers\SocialMediaFeedController;
use Middleware\VerifyUser;

$router->post('/social-media-feed', [SocialMediaFeedController::class, 'createSocialMediaFeed'], VerifyUser::class);
$router->get('/social-media-feed', [SocialMediaFeedController::class, 'getAllSocialMediaFeeds']);
$router->patch('/social-media-feed', [SocialMediaFeedController::class, 'updateSocialMediaFeed'], VerifyUser::class);
$router->delete('/social-media-feed', [SocialMediaFeedController::class, 'deleteSocialMediaFeed'], VerifyUser::class);
