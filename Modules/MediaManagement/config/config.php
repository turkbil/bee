<?php

return [
    'name' => 'MediaManagement',

    // ========================================
    // MEDIA TYPES - Desteklenen medya tipleri
    // ========================================
    'media_types' => [
        'image' => [
            'label' => 'Görsel',
            'icon' => 'image',
            'mime_types' => [
                'image/jpeg',
                'image/jpg',
                'image/png',
                'image/webp',
                'image/gif',
                'image/svg+xml',
            ],
            'extensions' => ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'],
            'max_size' => 10240, // 10MB in KB
            'enabled' => true,
        ],

        'video' => [
            'label' => 'Video',
            'icon' => 'video',
            'mime_types' => [
                'video/mp4',
                'video/webm',
                'video/ogg',
                'video/quicktime',
            ],
            'extensions' => ['mp4', 'webm', 'ogg', 'mov'],
            'max_size' => 102400, // 100MB in KB
            'enabled' => true,
        ],

        'audio' => [
            'label' => 'Ses',
            'icon' => 'music',
            'mime_types' => [
                'audio/mpeg',
                'audio/mp3',
                'audio/wav',
                'audio/ogg',
            ],
            'extensions' => ['mp3', 'wav', 'ogg'],
            'max_size' => 51200, // 50MB in KB
            'enabled' => true,
        ],

        'document' => [
            'label' => 'Doküman',
            'icon' => 'file-text',
            'mime_types' => [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ],
            'extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
            'max_size' => 20480, // 20MB in KB
            'enabled' => true,
        ],

        'archive' => [
            'label' => 'Arşiv',
            'icon' => 'archive',
            'mime_types' => [
                'application/zip',
                'application/x-zip-compressed',
                'application/x-rar-compressed',
            ],
            'extensions' => ['zip', 'rar'],
            'max_size' => 51200, // 50MB in KB
            'enabled' => true,
        ],
    ],

    // ========================================
    // DEFAULT CONVERSIONS - Image conversions
    // ========================================
    'conversions' => [
        'thumb' => [
            'width' => 300,
            'height' => 200,
            'format' => 'webp',
            'quality' => 85,
            'queued' => false, // Instant feedback
        ],

        'medium' => [
            'width' => 800,
            'height' => 600,
            'format' => 'webp',
            'quality' => 90,
            'queued' => true,
        ],

        'large' => [
            'width' => 1920,
            'height' => 1080,
            'format' => 'webp',
            'quality' => 90,
            'queued' => true,
        ],

        'responsive' => [
            'responsive' => true,
            'format' => 'webp',
            'quality' => 90,
            'queued' => true,
        ],
    ],

    // ========================================
    // UI SETTINGS
    // ========================================
    'ui' => [
        'sortable_enabled' => true,
        'set_featured_from_gallery' => true,
        'show_file_info' => true,
        'drag_drop_enabled' => true,
        'preview_enabled' => true,
    ],

    // ========================================
    // DEFAULTS
    // ========================================
    'defaults' => [
        'max_file_size' => 10240, // KB
        'max_gallery_items' => 50,
        'allowed_types' => ['image'], // Default: sadece görsel
    ],

    // ========================================
    // COLLECTION TEMPLATES - Hazır şablonlar
    // ========================================
    'collection_templates' => [
        'featured_image' => [
            'type' => 'image',
            'single_file' => true,
            'max_items' => 1,
            'conversions' => ['thumb', 'medium', 'large', 'responsive'],
            'sortable' => false,
        ],

        'seo_og_image' => [
            'type' => 'image',
            'single_file' => true,
            'max_items' => 1,
            'conversions' => ['thumb', 'medium', 'large'],
            'sortable' => false,
            'label' => 'Sosyal Medya Görseli',
            'recommended_size' => '1200x630',
        ],

        'gallery' => [
            'type' => 'image',
            'single_file' => false,
            'max_items' => 50,
            'conversions' => ['thumb', 'medium', 'large', 'responsive'],
            'sortable' => true,
        ],

        'videos' => [
            'type' => 'video',
            'single_file' => false,
            'max_items' => 10,
            'conversions' => [],
            'sortable' => true,
        ],

        'audio' => [
            'type' => 'audio',
            'single_file' => false,
            'max_items' => 20,
            'conversions' => [],
            'sortable' => true,
        ],

        'documents' => [
            'type' => 'document',
            'single_file' => false,
            'max_items' => 30,
            'conversions' => [],
            'sortable' => true,
        ],
    ],

    // ========================================
    // VALIDATION
    // ========================================
    'validation' => [
        'file_name_max_length' => 255,
        'file_name_sanitize' => true,
    ],

    // ========================================
    // STORAGE
    // ========================================
    'storage' => [
        'disk' => 'public',
        'path_generator' => 'default', // default, date-based, tenant-based
    ],
];
