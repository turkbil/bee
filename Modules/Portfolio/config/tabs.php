<?php

return [
    'tabs' => [
        [
            'key' => 'basic',
            'name' => 'Temel Bilgiler',
            'icon' => 'fas fa-briefcase',
            'required_fields' => ['title']
        ],
        [
            'key' => 'seo',
            'name' => 'SEO',
            'icon' => 'fas fa-search',
            'required_fields' => ['seo_title']
        ]
        // Code tabı yok - Portfolio'da kod alanı olmayacak (Page gibi değil, Announcement gibi)
    ]
];