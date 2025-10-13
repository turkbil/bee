<?php

return [
    'name' => 'Shop',

    // ROUTE TANIMLAMALARI - DynamicRouteService tarafından kullanılıyor
    'slugs' => [
        'index' => 'shop',
        'show' => 'shop',
    ],

    'routes' => [
        'index' => [
            'controller' => \Modules\Shop\App\Http\Controllers\Front\ShopController::class,
            'method' => 'index'
        ],
        'show' => [
            'controller' => \Modules\Shop\App\Http\Controllers\Front\ShopController::class,
            'method' => 'show'
        ]
    ],

    // TAB SİSTEMİ - SHOP PRODUCTS
    'tabs' => [
        [
            'key' => 'content',
            'name' => 'Ürün Bilgileri',
            'icon' => 'shopping-cart',
            'required_fields' => ['title', 'sku']
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
            'storage_key' => 'shop_active_tab',
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
            'label' => 'shop::admin.shop_detail',
            'needs_selection' => true
        ]
    ],

    /**
     * Pagination Ayarları
     */
    'pagination' => [
        'admin_per_shop' => env('SHOP_ADMIN_PER_PAGE', 10),
        'front_per_shop' => env('SHOP_FRONT_PER_PAGE', 12),
        'max_per_shop' => 100,
    ],

    /**
     * Özellik Toggleları (Feature Flags)
     */
    'features' => [
        'ai_translation' => env('SHOP_AI_TRANSLATION', true),
        'bulk_operations' => env('SHOP_BULK_OPERATIONS', true),
        'inline_editing' => env('SHOP_INLINE_EDITING', true),
        'version_control' => env('SHOP_VERSION_CONTROL', false),
        'preview_mode' => env('SHOP_PREVIEW_MODE', true),
        'variants' => env('SHOP_VARIANTS_ENABLED', true),
        'inventory_tracking' => env('SHOP_INVENTORY_ENABLED', false),
        'quote_system' => env('SHOP_QUOTE_SYSTEM', true),
    ],

    /**
     * Entegrasyon Ayarları
     */
    'integrations' => [
        'studio' => [
            'enabled' => env('SHOP_STUDIO_ENABLED', true),
            'component' => 'Modules\Studio\App\Http\Livewire\EditorComponent',
        ],
    ],

    /**
     * Queue Ayarları
     */
    'queue' => [
        'connection' => env('SHOP_QUEUE_CONNECTION', 'redis'),
        'queue_name' => env('SHOP_QUEUE_NAME', 'tenant_isolated'),
        'retry_after' => 90,
        'tries' => 3,
        'timeout' => 300, // 5 dakika
    ],

    /**
     * Performans Ayarları
     */
    'performance' => [
        'eager_loading' => ['category', 'brand', 'seoSetting', 'childProducts'],
        'chunk_size' => 100,
        'index_columns' => [
            ['is_active', 'deleted_at', 'created_at'],
            ['category_id', 'brand_id'],
        ],
    ],

    /**
     * Varsayılan Değerler
     */
    'defaults' => [
        'is_active' => true,
        'product_type' => 'physical',
        'condition' => 'new',
        'currency' => 'TRY',
        'price_on_request' => false,
    ],

    /**
     * Media Ayarları
     */
    'media' => [
        'upload_path' => 'shop/products',
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
        'max_file_size' => 5120, // 5MB in KB
    ],

    /**
     * Quote (Teklif) Sistemi Ayarları
     */
    'quote' => [
        'enabled' => env('SHOP_QUOTE_ENABLED', true),
        'admin_email' => env('SHOP_QUOTE_ADMIN_EMAIL', 'info@ixtif.com'),
        'send_customer_confirmation' => env('SHOP_QUOTE_CUSTOMER_CONFIRMATION', true),
        'send_admin_notification' => env('SHOP_QUOTE_ADMIN_NOTIFICATION', true),
    ],

    // ========================================
    // CACHE YÖNETİMİ
    // ========================================
    'cache' => [
        'enabled' => env('SHOP_CACHE_ENABLED', true),
        'ttl' => [
            'list' => 3600,      // 1 saat - Liste sayfaları
            'detail' => 7200,    // 2 saat - Detay sayfaları
        ],
        'tags' => ['shops', 'content'],
        'warming' => [
            'enabled' => env('SHOP_CACHE_WARMING_ENABLED', true),
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
            'type' => 'Product',
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
                'cart',
                'checkout',
                'account',
                'orders',
            ],
        ],
        'sku' => [
            'min' => 2,
            'max' => 191,
            'unique_check' => true,
        ],
        'short_description' => [
            'max' => 500,
        ],
        'body' => [
            'max' => 65535, // TEXT field limit
        ],
        'price' => [
            'min' => 0,
            'max' => 999999999.99,
        ],
    ],

    // ========================================
    // İÇERİK GÜVENLİĞİ
    // ========================================
    'security' => [
        'sanitize_html' => true,
        // NOT: allowed_tags/attributes kullanılmıyor, SecurityValidationService global olarak yönetiyor
        // NOT: CSS/JS security ayarları yok çünkü Shop'ta custom CSS/JS desteği yok
    ],

    // ========================================
    // DEBUG & LOGGING
    // ========================================
    'debug' => [
        'enabled' => env('SHOP_DEBUG_ENABLED', env('APP_DEBUG', false)),
        'verbose_logs' => env('SHOP_VERBOSE_LOGS', false),
        'log_channel' => env('SHOP_LOG_CHANNEL', 'stack'),
        'log_queries' => env('SHOP_LOG_QUERIES', false),
        'log_routes' => env('SHOP_LOG_ROUTES', false),
    ],
];
