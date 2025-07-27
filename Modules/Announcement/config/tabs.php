<?php

return [
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
            'required_fields' => ['seo_title']
        ]
        // Code tabı yok - Announcement'ta kod alanı olmayacak
    ]
];