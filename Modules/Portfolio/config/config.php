<?php

return [
    'name' => 'Portfolio',
    'slugs' => [
        'index' => 'portfolio',
        'show' => 'portfolio',
        'category' => 'category',
    ],
    'routes' => [
        'index' => [
            'controller' => \Modules\Portfolio\App\Http\Controllers\Front\PortfolioController::class,
            'method' => 'index'
        ],
        'show' => [
            'controller' => \Modules\Portfolio\App\Http\Controllers\Front\PortfolioController::class,
            'method' => 'show'
        ],
        'category' => [
            'controller' => \Modules\Portfolio\App\Http\Controllers\Front\PortfolioController::class,
            'method' => 'category'
        ]
    ],
    // Menu URL tipleri - MenuManagement için dinamik yapı
    'menu_url_types' => [
        [
            'type' => 'list',
            'label' => 'portfolio::admin.all_portfolios',
            'needs_selection' => false
        ],
        [
            'type' => 'category',
            'label' => 'portfolio::admin.portfolio_category',
            'needs_selection' => true
        ],
        [
            'type' => 'detail',
            'label' => 'portfolio::admin.portfolio_detail',
            'needs_selection' => true
        ]
    ]
];
