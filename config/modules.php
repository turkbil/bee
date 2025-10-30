<?php

use Nwidart\Modules\Activators\FileActivator;

return [

    /*
    |--------------------------------------------------------------------------
    | Global Module Settings
    |--------------------------------------------------------------------------
    |
    | Bu ayarlar TÜM modüller için geçerlidir.
    | Modül-specific ayarlar için: config/{module-name}.php
    |
    */

    // ========================================
    // NWIDART/LARAVEL-MODULES PAKETİ AYARLARI (KRİTİK!)
    // ========================================

    /*
     * Module namespace
     */
    'namespace' => 'Modules',

    /*
     * Module paths
     */
    'paths' => [
        'modules' => base_path('Modules'),
        'assets' => public_path('modules'),
        'migration' => base_path('database/migrations'),
        'app_folder' => 'app/',
        'generator' => [
            'config' => ['path' => 'config', 'generate' => true],
            'provider' => ['path' => 'app/Providers', 'generate' => true],
            'route-provider' => ['path' => 'app/Providers', 'generate' => true],
        ],
    ],

    /*
     * Module activators - Config cache için ZORUNLU!
     */
    'activators' => [
        'file' => [
            'class' => FileActivator::class,
            'statuses-file' => base_path('modules_statuses.json'),
        ],
    ],

    /*
     * Default activator
     */
    'activator' => 'file',

    /*
     * Auto-discover features
     */
    'auto-discover' => [
        'migrations' => true,
        'translations' => false,
        'config' => true,
        'providers' => false,
    ],

    // ========================================
    // DİL SİSTEMİ
    // ========================================
    // NOT: Her modül bu değerleri kendi config'inde override edebilir
    'system_languages' => env('SYSTEM_LANGUAGES') ? explode(',', env('SYSTEM_LANGUAGES')) : ['tr', 'en'],
    'default_language' => env('DEFAULT_LANGUAGE', 'tr'),

    // ========================================
    // MEDYA YÖNETİMİ
    // ========================================
    'media' => [
        // Genel dosya boyutu limitleri
        'max_file_size' => env('MEDIA_MAX_FILE_SIZE', 10240), // KB (10MB)
        
        // Koleksiyon limitleri
        'max_items' => [
            'featured' => 1,
            'gallery' => 50,
            'documents' => 20,
        ],
        
        // İzin verilen dosya tipleri
        'allowed_extensions' => [
            'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
            'document' => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
            'video' => ['mp4', 'avi', 'mov', 'wmv'],
        ],
        
        // Görsel dönüşüm boyutları
        'conversions' => [
            'thumb' => ['width' => 150, 'height' => 150],
            'medium' => ['width' => 300, 'height' => 300],
            'large' => ['width' => 1024, 'height' => 768],
            'responsive' => ['width' => 1920, 'height' => null],
        ],
        
        // Upload path (tenant-aware)
        'base_path' => 'uploads',
    ],

    // ========================================
    // SAYFALAMA (PAGINATION)
    // ========================================
    'pagination' => [
        'admin_per_page' => env('ADMIN_PER_PAGE', 10),
        'front_per_page' => env('FRONT_PER_PAGE', 12),
        'max_per_page' => 100,
        'available_per_page' => [10, 25, 50, 100],
    ],

    // ========================================
    // CACHE YÖNETİMİ
    // ========================================
    'cache' => [
        'enabled' => env('MODULE_CACHE_ENABLED', true),
        'ttl' => [
            'list' => env('CACHE_TTL_LIST', 3600),      // 1 saat
            'detail' => env('CACHE_TTL_DETAIL', 7200),  // 2 saat
            'api' => env('CACHE_TTL_API', 1800),        // 30 dakika
        ],
        'warming' => [
            'enabled' => env('CACHE_WARMING_ENABLED', true),
            'batch_size' => 50,
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
        ],
        'body' => [
            'max' => 65535, // TEXT field limit
        ],
        'description' => [
            'max' => 500,
        ],
    ],

    // ========================================
    // RESERVED SLUGS (Tüm Modüller İçin)
    // ========================================
    'reserved_slugs' => [
        'admin',
        'api',
        'login',
        'logout',
        'register',
        'password',
        'dashboard',
        'profile',
        'settings',
        'search',
        'home',
        'index',
        'create',
        'edit',
        'delete',
        'update',
        'store',
        'show',
    ],

    // ========================================
    // QUEUE YÖNETİMİ
    // ========================================
    'queue' => [
        'connection' => env('MODULE_QUEUE_CONNECTION', 'redis'),
        'queue_name' => env('MODULE_QUEUE_NAME', 'tenant_isolated'),
        'timeout' => env('QUEUE_TIMEOUT', 300),
        'tries' => env('QUEUE_TRIES', 3),
        'retry_after' => env('QUEUE_RETRY_AFTER', 90),
    ],

    // ========================================
    // SEO YÖNETİMİ
    // ========================================
    'seo' => [
        'enabled' => true,
        'fields' => [
            'seo_title' => ['max_length' => 60],
            'seo_description' => ['max_length' => 160],
            'seo_keywords' => ['max_keywords' => 10],
        ],
        'fallbacks' => [
            'use_title_for_meta_title' => true,
            'use_excerpt_for_meta_description' => true,
            'auto_generate_keywords' => true,
            'max_auto_keywords' => 5,
        ],
        'schema' => [
            'enabled' => true,
            'include_breadcrumbs' => true,
            'include_organization' => true,
        ],
    ],

    // ========================================
    // SECURITY
    // ========================================
    'security' => [
        'sanitize_html' => true,
        'xss_protection' => true,
        'csrf_protection' => true,
    ],

    // ========================================
    // PERFORMANS
    // ========================================
    'performance' => [
        'eager_loading' => ['seoSetting'],
        'chunk_size' => 100,
        'lazy_loading' => true,
    ],

    // ========================================
    // AI & TRANSLATION
    // ========================================
    'ai' => [
        'translation_enabled' => env('AI_TRANSLATION_ENABLED', true),
        'content_generation_enabled' => env('AI_CONTENT_ENABLED', false),
    ],

    // ========================================
    // FORM YÖNETİMİ
    // ========================================
    'form' => [
        'auto_save' => env('FORM_AUTO_SAVE', false),
        'auto_save_interval' => 30, // saniye
        'validation' => [
            'real_time' => true,
            'submit_button_states' => true,
        ],
    ],

];
