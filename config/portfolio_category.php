<?php

return [
    'name' => 'PortfolioCategory',

    // TAB SİSTEMİ - PORTFOLIO CATEGORY
    'tabs' => [
        [
            'key' => 'basic',
            'name' => 'Temel Bilgiler',
            'icon' => 'fas fa-file-text',
            'required_fields' => ['title']
        ],
        [
            'key' => 'seo',
            'name' => 'SEO',
            'icon' => 'fas fa-search',
            'required_fields' => []
        ]
    ],

    // FORM YÖNETİMİ
    'form' => [
        'persistence' => [
            'save_active_tab' => true,
            'storage_key' => 'portfolio_category_active_tab',
            'restore_on_load' => true
        ],
        'validation' => [
            'real_time' => true,
            'submit_button_states' => true
        ]
    ],
];
