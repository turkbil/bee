<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI System Configuration - Simplified Single Table Approach
    |--------------------------------------------------------------------------
    |
    | Global AI system settings - tek tablo yaklaşımı için config
    | Providers ai_providers tablosundan, global settings buradan
    |
    */

    // Global AI system settings
    'enabled' => env('AI_ENABLED', true),
    'debug' => env('AI_DEBUG', false),
    'cache_duration' => env('AI_CACHE_DURATION', 60), // minutes
    
    // Default provider (fallback from database)
    'default_provider' => env('AI_DEFAULT_PROVIDER', 'openai'),
    'default_model' => env('AI_DEFAULT_MODEL', 'gpt-4o-mini'),
    'fallback_provider' => env('AI_FALLBACK_PROVIDER', 'deepseek'),

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
    | Credit & Pricing Management
    |--------------------------------------------------------------------------
    |
    | Credit yönetimi ve fiyatlandırma ayarları. Aracı kurum kommisyonu dahil.
    |
    */

    'credit_management' => [
        // Model bazlı aracı kurum çarpanları - UCUZ MODEL = YÜKSEK ÇARPAN
        'model_multipliers' => [
            // OpenAI Modelleri
            'gpt-4o-mini' => (float) env('AI_GPT4O_MINI_MULTIPLIER', 5.0), // En ucuz model, en yüksek çarpan
            'gpt-3.5-turbo' => (float) env('AI_GPT35_TURBO_MULTIPLIER', 4.0), // Ucuz model, yüksek çarpan
            'gpt-4o' => (float) env('AI_GPT4O_MULTIPLIER', 1.8), // Pahalı model, düşük çarpan
            
            // DeepSeek Modelleri
            'deepseek-chat' => (float) env('AI_DEEPSEEK_CHAT_MULTIPLIER', 4.5), // Ucuz, yüksek çarpan
            
            // Claude Modelleri  
            'claude-3-haiku-20240307' => (float) env('AI_CLAUDE_HAIKU_MULTIPLIER', 4.8), // En ucuz Claude, yüksek çarpan
            'claude-3-sonnet-20240229' => (float) env('AI_CLAUDE_SONNET_MULTIPLIER', 1.5), // En pahalı, en düşük çarpan
        ],
        
        // Provider bazlı fallback çarpanları (model bulunamazsa)
        'provider_multipliers' => [
            'openai' => (float) env('AI_OPENAI_MULTIPLIER', 3.0),
            'deepseek' => (float) env('AI_DEEPSEEK_MULTIPLIER', 4.5),
            'claude' => (float) env('AI_CLAUDE_MULTIPLIER', 3.0),
            'default' => (float) env('AI_DEFAULT_MULTIPLIER', 3.0),
        ],
        
        // Feature bazlı çarpanlar (opsiyonel ek komisyon)
        'feature_multipliers' => [
            'seo-content-generation' => (float) env('AI_SEO_FEATURE_MULTIPLIER', 1.2), // SEO premium
            'content-translation' => (float) env('AI_TRANSLATION_FEATURE_MULTIPLIER', 1.1), // Çeviri normal
            'chat' => (float) env('AI_CHAT_FEATURE_MULTIPLIER', 1.0), // Chat standart
            'default' => (float) env('AI_DEFAULT_FEATURE_MULTIPLIER', 1.0),
        ],
        
        // Varsayılan credit limitleri
        'default_daily_limit' => (float) env('AI_DEFAULT_DAILY_CREDIT_LIMIT', 1.0), // $1 USD
        'default_monthly_limit' => (float) env('AI_DEFAULT_MONTHLY_CREDIT_LIMIT', 20.0), // $20 USD
        
        // Cache ayarları
        'cache_ttl' => (int) env('AI_CACHE_TTL', 300), // 5 dakika
        'cache_prefix' => env('AI_CACHE_PREFIX', 'ai_credits'),
        
        // Credit hesaplama
        'credit_calculation_method' => env('AI_CREDIT_CALCULATION', 'accurate'), // simple, accurate
        'base_cost_per_1k_tokens' => (float) env('AI_BASE_COST_PER_1K_TOKENS', 0.0015), // $0.0015 per 1K tokens average
        
        // Güvenlik
        'max_credit_per_request' => (float) env('AI_MAX_CREDIT_PER_REQUEST', 2.0), // $2 max per request
        'rate_limit_per_minute' => (int) env('AI_RATE_LIMIT_PER_MINUTE', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Legacy Token Management (Backward Compatibility)
    |--------------------------------------------------------------------------
    |
    | Eski token sistemi ile uyumluluk için ayarlar.
    |
    */

    'token_management' => [
        // Legacy token limitleri
        'default_daily_limit' => (int) env('AI_DEFAULT_DAILY_LIMIT', 100),
        'default_monthly_limit' => (int) env('AI_DEFAULT_MONTHLY_LIMIT', 3000),
        
        // Token hesaplama
        'token_calculation_method' => env('AI_TOKEN_CALCULATION', 'simple'), // simple, accurate
        'characters_per_token' => (int) env('AI_CHARACTERS_PER_TOKEN', 4),
        
        // Token to credit conversion rate
        'token_to_credit_rate' => (float) env('AI_TOKEN_TO_CREDIT_RATE', 0.0005), // 1 token = $0.0005
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