<?php

return [
    'name' => 'Studio',
    'prefix' => 'studio', // route prefix
    'editor' => [
        'use_cdn' => false, // GrapesJS'i CDN'den mi yoksa lokal mi yükleyeceğiz
        'use_jquery' => true, // jQuery kullanımı
        'panels' => [
            'default' => true, // Varsayılan panelleri kullan
            'custom' => true,  // Özel panelleri kullan
        ],
        'plugins' => [
            'blocks-basic' => true,
            'preset-webpage' => true,
            'style-bg' => true,
            'plugin-export' => true,
            'plugin-forms' => true,
            'custom-code' => true,
            'touch' => true,
            'component-countdown' => true,
            'tabs' => true,
            'typed' => true,
            'tui-image-editor' => true
        ],
    ],
    'widget' => [
        'enable' => true, // Widget entegrasyonu
        'categories' => [
            'basic' => 'Temel Bileşenler',
            'section' => 'Bölümler',
            'media' => 'Medya',
            'custom' => 'Özel Bileşenler',
            'widget' => 'Widgetlar'
        ]
    ],
    'theme' => [
        'enable' => true, // Tema entegrasyonu
    ]
];