<?php

return [
    'name' => 'Portfolio',
    'slugs' => [
        'index' => 'portfolios',
        'show' => 'portfolio',
        'category' => 'portfolio-category',
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
    ]
];
