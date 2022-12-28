<?php

use Ayacoo\Tiktok\Controller\OnlineMediaUpdateController;

return [
    // Save a newly added online media
    'ayacoo_tiktok_online_media_updater' => [
        'path' => '/ayacoo-tiktok/update',
        'target' => OnlineMediaUpdateController::class . '::updateAction',
    ],
];
