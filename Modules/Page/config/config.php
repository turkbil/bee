<?php

return [
    'name' => 'Page',
    'slugs' => [
        'index' => 'pages',
        'show' => 'page',
    ],
    'routes' => [
        'index' => [
            'controller' => \Modules\Page\App\Http\Controllers\Front\PageController::class,
            'method' => 'index'
        ],
        'show' => [
            'controller' => \Modules\Page\App\Http\Controllers\Front\PageController::class,
            'method' => 'show'
        ],
        'homepage' => [
            'controller' => \Modules\Page\App\Http\Controllers\Front\PageController::class,
            'method' => 'homepage'
        ]
    ]
];
