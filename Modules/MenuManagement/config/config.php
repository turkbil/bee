<?php

return [
    'name' => 'MenuManagement',
    'slugs' => [
        'index' => 'menus',
        'show' => 'menu',
    ],
    // MenuManagement sadece admin modülü - frontend routes yok

    // MENÜ KONUMLARI
    'menu_locations' => [
        'header' => 'Header Menu',
        'footer' => 'Footer Menu',
        'sidebar' => 'Sidebar Menu',
        'mobile' => 'Mobile Menu',
        'main' => 'Main Menu',
        'secondary' => 'Secondary Menu',
        'utility' => 'Utility Menu',
        'social' => 'Social Menu',
        'breadcrumb' => 'Breadcrumb Menu',
        'category' => 'Category Menu'
    ],


    // TAB SİSTEMİ - Sadece tek tab
    'tabs' => [
        [
            'key' => 'basic',
            'name' => 'Temel Bilgiler',
            'icon' => 'menu',
            'required_fields' => ['name', 'location']
        ]
    ],

    // FORM YÖNETİMİ
    'form' => [
        'persistence' => [
            'save_active_tab' => true,
            'storage_key' => 'menu_active_tab',
            'restore_on_load' => true
        ],
        'validation' => [
            'real_time' => true,
            'submit_button_states' => true
        ]
    ]
];