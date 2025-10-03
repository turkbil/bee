<?php

return [
    'name' => 'Page',

    // ROUTE TANIMLAMALARI - DynamicRouteService tarafından kullanılıyor
    'slugs' => [
        'index' => 'page',
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
    ],

    // Menu URL tipleri - MenuManagement için dinamik yapı
    'menu_url_types' => [
        [
            'type' => 'detail',
            'label' => 'page::admin.page_detail',
            'needs_selection' => true
        ]
    ],

    /**
     * Pagination Ayarları
     */
    'pagination' => [
        'admin_per_page' => env('PAGE_ADMIN_PER_PAGE', 10),
        'front_per_page' => env('PAGE_FRONT_PER_PAGE', 12),
        'max_per_page' => 100,
    ],

    /**
     * Özellik Toggleları (Feature Flags)
     */
    'features' => [
        'ai_translation' => env('PAGE_AI_TRANSLATION', true),
        'bulk_operations' => env('PAGE_BULK_OPERATIONS', true),
        'inline_editing' => env('PAGE_INLINE_EDITING', true),
        'version_control' => env('PAGE_VERSION_CONTROL', false),
        'preview_mode' => env('PAGE_PREVIEW_MODE', true),
        'custom_css_js' => env('PAGE_CUSTOM_CSS_JS', true),
    ],

    /**
     * Queue Ayarları
     */
    'queue' => [
        'connection' => env('PAGE_QUEUE_CONNECTION', 'redis'),
        'queue_name' => env('PAGE_QUEUE_NAME', 'tenant_isolated'),
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
            ['is_homepage', 'is_active'],
        ],
    ],

    /**
     * Varsayılan Değerler
     */
    'defaults' => [
        'is_active' => true,
        'is_homepage' => false,
        'css' => null,
        'js' => null,
    ],

    /**
     * Media Ayarları
     */
    'media' => [
        'upload_path' => 'pages',
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf'],
        'max_file_size' => 5120, // 5MB in KB
    ],

    // ========================================
    // CACHE YÖNETİMİ
    // ========================================
    'cache' => [
        'enabled' => env('PAGE_CACHE_ENABLED', true),
        'ttl' => [
            'list' => 3600,      // 1 saat - Liste sayfaları
            'detail' => 7200,    // 2 saat - Detay sayfaları
            'homepage' => 1800,  // 30 dakika - Ana sayfa
        ],
        'tags' => ['pages', 'content'],
        'warming' => [
            'enabled' => env('PAGE_CACHE_WARMING_ENABLED', true),
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
        'max_css_size' => 50000, // 50KB - PageObserver tarafından kullanılıyor
        'max_js_size' => 50000,  // 50KB - PageObserver tarafından kullanılıyor
    ],
];