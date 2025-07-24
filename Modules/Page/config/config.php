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
    ],

    // SEO YÖNETİMİ
    'seo' => [
        'enabled' => true,
        'fields' => [
            'seo_title' => ['required' => true, 'max_length' => 60],
            'seo_description' => ['required' => true, 'max_length' => 160],
            'seo_keywords' => ['required' => false, 'max_keywords' => 10],
            'canonical_url' => ['required' => false],
        ],
        'tab_name' => 'SEO',
        'tab_icon' => 'search'
    ],

    // TAB SİSTEMİ
    'tabs' => [
        [
            'key' => 'content',
            'name' => 'İçerik',
            'icon' => 'edit',
            'required_fields' => ['title', 'content']
        ],
        [
            'key' => 'seo',
            'name' => 'SEO',
            'icon' => 'search',
            'required_fields' => ['seo_title', 'seo_description']
        ],
        [
            'key' => 'advanced',
            'name' => 'Gelişmiş',
            'icon' => 'cogs',
            'required_fields' => []
        ]
    ],

    // FORM YÖNETİMİ
    'form' => [
        'persistence' => [
            'save_active_tab' => true,
            'storage_key' => 'page_active_tab',
            'restore_on_load' => true
        ],
        'validation' => [
            'real_time' => true,
            'submit_button_states' => true
        ]
    ]
];
