<?php

return [
    'name' => 'Announcement',
    'slugs' => [
        'index' => 'announcement',
        'show' => 'announcement',
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
    ],
    // Menu URL tipleri - MenuManagement için dinamik yapı
    'menu_url_types' => [
        [
            'type' => 'list',
            'label' => 'announcement::admin.all_announcements',
            'needs_selection' => false
        ],
        [
            'type' => 'detail',
            'label' => 'announcement::admin.announcement_detail',
            'needs_selection' => true
        ]
    ]
];
