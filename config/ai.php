<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default AI Provider
    |--------------------------------------------------------------------------
    |
    | Bu seçenek, uygulamanızda kullanılacak varsayılan AI provider'ını
    | belirler. Şu anda sadece DeepSeek desteklenmektedir.
    |
    */

    'default_provider' => env('AI_DEFAULT_PROVIDER', 'deepseek'),

    /*
    |--------------------------------------------------------------------------
    | AI Providers
    |--------------------------------------------------------------------------
    |
    | Burada uygulamanızda kullanabileceğiniz AI provider'larını 
    | yapılandırabilirsiniz. Her provider'ın kendine özgü ayarları vardır.
    |
    */

    'providers' => [
        'deepseek' => [
            'driver' => 'deepseek',
            'api_key' => env('DEEPSEEK_API_KEY'),
            'api_url' => env('DEEPSEEK_API_URL', 'https://api.deepseek.com/v1'),
            'model' => env('DEEPSEEK_MODEL', 'deepseek-chat'),
            'system_message' => env('DEEPSEEK_SYSTEM_MESSAGE', 'Sen bir yapay zeka asistanısın. Türkçe yanıt ver.'),
            'temperature' => (float) env('DEEPSEEK_TEMPERATURE', 0.7),
            'max_tokens' => (int) env('DEEPSEEK_MAX_TOKENS', 2000),
            'timeout' => (int) env('DEEPSEEK_TIMEOUT', 300),
        ],

        // Gelecekte eklenebilecek provider'lar
        // 'openai' => [...],
        // 'claude' => [...],
        // 'gemini' => [...],
    ],

    /*
    |--------------------------------------------------------------------------
    | Token Management
    |--------------------------------------------------------------------------
    |
    | Token yönetimi için gerekli ayarlar. Bu ayarlar tüm modüller
    | tarafından kullanılır.
    |
    */

    'token_management' => [
        // Varsayılan token limitleri
        'default_daily_limit' => (int) env('AI_DEFAULT_DAILY_LIMIT', 100),
        'default_monthly_limit' => (int) env('AI_DEFAULT_MONTHLY_LIMIT', 3000),
        
        // Cache ayarları
        'cache_ttl' => (int) env('AI_CACHE_TTL', 300), // 5 dakika
        'cache_prefix' => env('AI_CACHE_PREFIX', 'ai_tokens'),
        
        // Token hesaplama
        'token_calculation_method' => env('AI_TOKEN_CALCULATION', 'simple'), // simple, accurate
        'characters_per_token' => (int) env('AI_CHARACTERS_PER_TOKEN', 4),
        
        // Güvenlik
        'max_tokens_per_request' => (int) env('AI_MAX_TOKENS_PER_REQUEST', 5000),
        'rate_limit_per_minute' => (int) env('AI_RATE_LIMIT_PER_MINUTE', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Module Integrations
    |--------------------------------------------------------------------------
    |
    | AI entegrasyonu olan modüllerin ayarları. Her modül için
    | özel konfigürasyonlar tanımlanabilir.
    |
    */

    'integrations' => [
        'page' => [
            'enabled' => (bool) env('AI_PAGE_INTEGRATION_ENABLED', true),
            'max_content_length' => (int) env('AI_PAGE_MAX_CONTENT_LENGTH', 10000),
            'supported_languages' => ['tr', 'en'],
            'default_word_count' => (int) env('AI_PAGE_DEFAULT_WORD_COUNT', 500),
            'templates' => [
                'blog_post' => 'Blog Yazısı',
                'product_page' => 'Ürün Sayfası',
                'landing_page' => 'Landing Sayfası',
                'about_page' => 'Hakkımızda Sayfası',
            ],
        ],

        'portfolio' => [
            'enabled' => (bool) env('AI_PORTFOLIO_INTEGRATION_ENABLED', true),
            'max_description_length' => (int) env('AI_PORTFOLIO_MAX_DESC_LENGTH', 2000),
            'supported_languages' => ['tr', 'en'],
        ],

        'announcement' => [
            'enabled' => (bool) env('AI_ANNOUNCEMENT_INTEGRATION_ENABLED', true),
            'max_content_length' => (int) env('AI_ANNOUNCEMENT_MAX_CONTENT_LENGTH', 5000),
            'supported_languages' => ['tr', 'en'],
        ],

        'studio' => [
            'enabled' => (bool) env('AI_STUDIO_INTEGRATION_ENABLED', true),
            'max_elements_per_request' => (int) env('AI_STUDIO_MAX_ELEMENTS', 50),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | AI işlemlerinin loglanması için ayarlar.
    |
    */

    'logging' => [
        'enabled' => (bool) env('AI_LOGGING_ENABLED', true),
        'level' => env('AI_LOG_LEVEL', 'info'), // debug, info, warning, error
        'channel' => env('AI_LOG_CHANNEL', 'daily'),
        'log_requests' => (bool) env('AI_LOG_REQUESTS', true),
        'log_responses' => (bool) env('AI_LOG_RESPONSES', false), // Güvenlik için false
        'log_token_usage' => (bool) env('AI_LOG_TOKEN_USAGE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance
    |--------------------------------------------------------------------------
    |
    | Performans optimizasyonu ayarları.
    |
    */

    'performance' => [
        // Response cache
        'cache_responses' => (bool) env('AI_CACHE_RESPONSES', false),
        'response_cache_ttl' => (int) env('AI_RESPONSE_CACHE_TTL', 3600), // 1 saat
        
        // Queue settings
        'use_queue_for_long_requests' => (bool) env('AI_USE_QUEUE', false),
        'long_request_threshold' => (int) env('AI_LONG_REQUEST_THRESHOLD', 1000), // token
        'queue_connection' => env('AI_QUEUE_CONNECTION', 'redis'),
        
        // Timeout settings
        'request_timeout' => (int) env('AI_REQUEST_TIMEOUT', 300),
        'stream_timeout' => (int) env('AI_STREAM_TIMEOUT', 360),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security
    |--------------------------------------------------------------------------
    |
    | Güvenlik ayarları.
    |
    */

    'security' => [
        // Content filtering
        'enable_content_filter' => (bool) env('AI_ENABLE_CONTENT_FILTER', true),
        'blocked_keywords' => [], // Yasaklı kelimeler listesi
        
        // Rate limiting
        'enable_rate_limiting' => (bool) env('AI_ENABLE_RATE_LIMITING', true),
        'rate_limit_key' => env('AI_RATE_LIMIT_KEY', 'ip'), // ip, user, tenant
        
        // API key rotation
        'api_key_rotation_enabled' => (bool) env('AI_API_KEY_ROTATION', false),
        'api_key_rotation_interval' => (int) env('AI_API_KEY_ROTATION_INTERVAL', 30), // gün
    ],

    /*
    |--------------------------------------------------------------------------
    | Development
    |--------------------------------------------------------------------------
    |
    | Geliştirme ortamı için ayarlar.
    |
    */

    'development' => [
        'debug_mode' => (bool) env('AI_DEBUG_MODE', false),
        'mock_responses' => (bool) env('AI_MOCK_RESPONSES', false),
        'save_debug_logs' => (bool) env('AI_SAVE_DEBUG_LOGS', false),
        'test_mode' => (bool) env('AI_TEST_MODE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Debug Dashboard
    |--------------------------------------------------------------------------
    |
    | AI Debug Dashboard için özel ayarlar.
    |
    */
    
    'debug_logging_enabled' => (bool) env('AI_DEBUG_LOGGING_ENABLED', true),
];