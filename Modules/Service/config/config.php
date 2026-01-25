<?php

return [
    'name' => 'Service',

    // ROUTE TANIMLAMALARI - DynamicRouteService tarafından kullanılıyor
    'slugs' => [
        'index' => 'service',
        'show' => 'service',
        'category' => 'kategori',
    ],

    'routes' => [
        'index' => [
            'controller' => \Modules\Service\App\Http\Controllers\Front\ServiceController::class,
            'method' => 'index'
        ],
        'show' => [
            'controller' => \Modules\Service\App\Http\Controllers\Front\ServiceController::class,
            'method' => 'show'
        ],
        'category' => [
            'controller' => \Modules\Service\App\Http\Controllers\Front\ServiceController::class,
            'method' => 'category'
        ]
    ],

    // TAB SİSTEMİ - SERVICE
    'tabs' => [
        [
            'key' => 'content',
            'name' => 'İçerik',
            'icon' => 'edit',
            'required_fields' => ['title', 'body']
        ],
        [
            'key' => 'seo',
            'name' => 'SEO',
            'icon' => 'search',
            'required_fields' => []
        ]
    ],

    // FORM YÖNETİMİ
    'form' => [
        'persistence' => [
            'save_active_tab' => true,
            'storage_key' => 'service_active_tab',
            'restore_on_load' => true
        ],
        'validation' => [
            'real_time' => true,
            'submit_button_states' => true
        ]
    ],

    // Menu URL tipleri - MenuManagement için dinamik yapı
    'menu_url_types' => [
        [
            'type' => 'detail',
            'label' => 'service::admin.service_detail',
            'needs_selection' => true
        ]
    ],

    /**
     * Pagination Ayarları
     */
    'pagination' => [
        'admin_per_service' => env('SERVICE_ADMIN_PER_PAGE', 10),
        'front_per_service' => env('SERVICE_FRONT_PER_PAGE', 12),
        'max_per_service' => 100,
    ],

    /**
     * Özellik Toggleları (Feature Flags)
     */
    'features' => [
        'ai_translation' => env('SERVICE_AI_TRANSLATION', true),
        'bulk_operations' => env('SERVICE_BULK_OPERATIONS', true),
        'inline_editing' => env('SERVICE_INLINE_EDITING', true),
        'version_control' => env('SERVICE_VERSION_CONTROL', false),
        'preview_mode' => env('SERVICE_PREVIEW_MODE', true),
        'custom_css_js' => false,
    ],

    /**
     * Entegrasyon Ayarları
     */
    'integrations' => [
        'studio' => [
            'enabled' => env('SERVICE_STUDIO_ENABLED', true),
            'component' => 'Modules\Studio\App\Http\Livewire\EditorComponent',
        ],
    ],

    /**
     * Queue Ayarları
     */
    'queue' => [
        'connection' => env('SERVICE_QUEUE_CONNECTION', 'redis'),
        'queue_name' => env('SERVICE_QUEUE_NAME', 'tenant_isolated'),
        'retry_after' => 90,
        'tries' => 3,
        'timeout' => 300, // 5 dakika
    ],

    /**
     * Performans Ayarları
     */
    'performance' => [
        'eager_loading' => ['seoSetting'],
        'chunk_size' => 100,
        'index_columns' => [
            ['is_active', 'deleted_at', 'created_at'],
        ],
    ],

    /**
     * Varsayılan Değerler
     */
    'defaults' => [
        'is_active' => true,
    ],

    /**
     * Media Ayarları
     */
    'media' => [
        'upload_path' => 'services',
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf'],
        'max_file_size' => 5120, // 5MB in KB
    ],

    // ========================================
    // CACHE YÖNETİMİ
    // ========================================
    'cache' => [
        'enabled' => env('SERVICE_CACHE_ENABLED', true),
        'ttl' => [
            'list' => 3600,      // 1 saat - Liste sayfaları
            'detail' => 7200,    // 2 saat - Detay sayfaları
        ],
        'tags' => ['services', 'content'],
        'warming' => [
            'enabled' => env('SERVICE_CACHE_WARMING_ENABLED', true),
            'schedule' => 'hourly', // hourly, daily, weekly
            'batch_size' => 50,
            'include_urls' => true,
        ],
    ],

    // ========================================
    // SEO YÖNETİMİ
    // ========================================
    'seo' => [
        'enabled' => true,
        'fields' => [
            'seo_title' => ['required' => false, 'max_length' => 60],
            'seo_description' => ['required' => false, 'max_length' => 160],
            'seo_keywords' => ['required' => false, 'max_keywords' => 10],
            'canonical_url' => ['required' => false],
        ],
        'tab_name' => 'SEO',
        'tab_icon' => 'search',
        'fallbacks' => [
            'use_title_for_meta_title' => true,
            'use_excerpt_for_meta_description' => true,
            'auto_generate_keywords' => true,
            'max_auto_keywords' => 5,
        ],
        'schema' => [
            'enabled' => true,
            'type' => 'WebPage',
            'include_breadcrumbs' => true,
            'include_organization' => true,
        ],
    ],

    // ========================================
    // VALİDASYON KURALLARI
    // ========================================
    'validation' => [
        'title' => [
            'min' => 3,
            'max' => 191,
        ],
        'slug' => [
            'min' => 3,
            'max' => 255,
            'separator' => '-',
            'lowercase' => true,
            'unique_check' => true,
            'reserved_slugs' => [
                'admin',
                'api',
                'login',
                'logout',
                'register',
                'dashboard',
                'profile',
                'settings',
                'search'
            ],
        ],
        'body' => [
            'max' => 65535, // TEXT field limit
        ],
    ],

    // ========================================
    // İÇERİK GÜVENLİĞİ
    // ========================================
    'security' => [
        'sanitize_html' => true,
        // NOT: allowed_tags/attributes kullanılmıyor, SecurityValidationService global olarak yönetiyor
        // NOT: CSS/JS security ayarları yok çünkü Service'ta custom CSS/JS desteği yok
    ],

    // ========================================
    // DEBUG & LOGGING
    // ========================================
    'debug' => [
        'enabled' => env('SERVICE_DEBUG_ENABLED', env('APP_DEBUG', false)),
        'verbose_logs' => env('SERVICE_VERBOSE_LOGS', false),
        'log_channel' => env('SERVICE_LOG_CHANNEL', 'stack'),
        'log_queries' => env('SERVICE_LOG_QUERIES', false),
        'log_routes' => env('SERVICE_LOG_ROUTES', false),
    ],
];
