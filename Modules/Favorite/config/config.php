<?php

return [
    'name' => 'Favorite',

    // ROUTE TANIMLAMALARI - DynamicRouteService tarafından kullanılıyor
    'slugs' => [
        'index' => 'favorite',
        'show' => 'favorite',
    ],

    'routes' => [
        'index' => [
            'controller' => \Modules\Favorite\App\Http\Controllers\Front\FavoriteController::class,
            'method' => 'index'
        ],
        'show' => [
            'controller' => \Modules\Favorite\App\Http\Controllers\Front\FavoriteController::class,
            'method' => 'show'
        ]
    ],

    // TAB SİSTEMİ
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
            'storage_key' => 'favorite_active_tab',
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
            'label' => 'favorite::admin.favorite_detail',
            'needs_selection' => true
        ]
    ],

    /**
     * Pagination Ayarları
     */
    'pagination' => [
        'admin_per_favorite' => env('FAVORITE_ADMIN_PER_PAGE', 10),
        'front_per_favorite' => env('FAVORITE_FRONT_PER_PAGE', 12),
        'max_per_favorite' => 100,
    ],

    /**
     * Özellik Toggleları (Feature Flags)
     */
    'features' => [
        'ai_translation' => env('FAVORITE_AI_TRANSLATION', true),
        'bulk_operations' => env('FAVORITE_BULK_OPERATIONS', true),
        'inline_editing' => env('FAVORITE_INLINE_EDITING', true),
        'version_control' => env('FAVORITE_VERSION_CONTROL', false),
        'preview_mode' => env('FAVORITE_PREVIEW_MODE', true),
        'custom_css_js' => false,
    ],

    /**
     * Entegrasyon Ayarları
     */
    'integrations' => [
        'studio' => [
            'enabled' => env('FAVORITE_STUDIO_ENABLED', true),
            'component' => 'Modules\Studio\App\Http\Livewire\EditorComponent',
        ],
    ],

    /**
     * Queue Ayarları
     */
    'queue' => [
        'connection' => env('FAVORITE_QUEUE_CONNECTION', 'redis'),
        'queue_name' => env('FAVORITE_QUEUE_NAME', 'tenant_isolated'),
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
        'upload_path' => 'favorites',
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf'],
        'max_file_size' => 5120, // 5MB in KB
    ],

    // ========================================
    // CACHE YÖNETİMİ
    // ========================================
    'cache' => [
        'enabled' => env('FAVORITE_CACHE_ENABLED', true),
        'ttl' => [
            'list' => 3600,      // 1 saat - Liste sayfaları
            'detail' => 7200,    // 2 saat - Detay sayfaları
        ],
        'tags' => ['favorites', 'content'],
        'warming' => [
            'enabled' => env('FAVORITE_CACHE_WARMING_ENABLED', true),
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
        // NOT: CSS/JS security ayarları yok çünkü Favorite'ta custom CSS/JS desteği yok
    ],

    // ========================================
    // DEBUG & LOGGING
    // ========================================
    'debug' => [
        'enabled' => env('FAVORITE_DEBUG_ENABLED', env('APP_DEBUG', false)),
        'verbose_logs' => env('FAVORITE_VERBOSE_LOGS', false),
        'log_channel' => env('FAVORITE_LOG_CHANNEL', 'stack'),
        'log_queries' => env('FAVORITE_LOG_QUERIES', false),
        'log_routes' => env('FAVORITE_LOG_ROUTES', false),
    ],
];
