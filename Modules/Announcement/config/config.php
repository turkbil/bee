<?php

return [
    'name' => 'Announcement',
    'slugs' => [
        'index' => 'announcements',
        'show' => 'announcements',
    ],
    'routes' => [
        'index' => [
            'controller' => \Modules\Announcement\App\Http\Controllers\Front\AnnouncementController::class,
            'method' => 'index'
        ],
        'show' => [
            'controller' => \Modules\Announcement\App\Http\Controllers\Front\AnnouncementController::class,
            'method' => 'show'
        ]
    ]
];
