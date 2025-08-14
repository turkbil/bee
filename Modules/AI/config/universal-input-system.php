<?php

declare(strict_types=1);

/**
 * Universal Input System V3 Professional - Main Configuration File
 * Comprehensive configuration for AI-powered form processing and management
 * 
 * @package Modules\AI\Config
 * @version 3.0.0
 * @author AI Universal Input System
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Universal Input System Version
    |--------------------------------------------------------------------------
    |
    | This value represents the version of the Universal Input System.
    | Used for compatibility checks and feature availability.
    |
    */

    'version' => '3.0.0',

    /*
    |--------------------------------------------------------------------------
    | System Status
    |--------------------------------------------------------------------------
    |
    | Enable or disable the Universal Input System globally.
    | When disabled, all UIS features will be unavailable.
    |
    */

    'enabled' => env('UIS_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Debug Mode
    |--------------------------------------------------------------------------
    |
    | Enable debug mode for detailed logging and error reporting.
    | Should be disabled in production environments.
    |
    */

    'debug' => env('UIS_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Performance Configuration
    |--------------------------------------------------------------------------
    |
    | Performance-related settings for the Universal Input System.
    |
    */

    'performance' => [
        // Maximum number of concurrent form submissions
        'max_concurrent_submissions' => env('UIS_MAX_CONCURRENT', 10),
        
        // Request timeout in seconds
        'request_timeout' => env('UIS_REQUEST_TIMEOUT', 30),
        
        // Memory limit for form processing (MB)
        'memory_limit' => env('UIS_MEMORY_LIMIT', 256),
        
        // Maximum input size per field (bytes)
        'max_input_size' => env('UIS_MAX_INPUT_SIZE', 1048576), // 1MB
        
        // Enable performance monitoring
        'monitoring_enabled' => env('UIS_MONITORING', true),
        
        // Performance thresholds
        'thresholds' => [
            'response_time_warning' => 2000, // milliseconds
            'response_time_critical' => 5000, // milliseconds
            'memory_usage_warning' => 80, // percentage
            'memory_usage_critical' => 95, // percentage
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Caching settings for improved performance and reduced database queries.
    |
    */

    'cache' => [
        // Default cache driver (null uses app default)
        'driver' => env('UIS_CACHE_DRIVER', null),
        
        // Cache TTL values (in seconds)
        'ttl' => [
            'form_structure' => env('UIS_CACHE_FORM_TTL', 3600), // 1 hour
            'smart_defaults' => env('UIS_CACHE_DEFAULTS_TTL', 1800), // 30 minutes
            'dynamic_options' => env('UIS_CACHE_OPTIONS_TTL', 900), // 15 minutes
            'analytics' => env('UIS_CACHE_ANALYTICS_TTL', 300), // 5 minutes
            'context_data' => env('UIS_CACHE_CONTEXT_TTL', 600), // 10 minutes
            'validation_rules' => env('UIS_CACHE_VALIDATION_TTL', 7200), // 2 hours
        ],
        
        // Cache tags for organized cache management
        'tags' => [
            'forms' => 'uis-forms',
            'defaults' => 'uis-defaults',
            'options' => 'uis-dynamic-options',
            'analytics' => 'uis-analytics',
            'context' => 'uis-context',
            'validation' => 'uis-validation',
        ],
        
        // Auto-clear cache on model updates
        'auto_clear' => env('UIS_CACHE_AUTO_CLEAR', true),
        
        // Cache warming configuration
        'warming' => [
            'enabled' => env('UIS_CACHE_WARMING', false),
            'popular_features' => [], // Feature IDs to pre-warm
            'schedule' => '0 */6 * * *', // Cron expression for warming
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Configuration
    |--------------------------------------------------------------------------
    |
    | Database-related settings for the Universal Input System.
    |
    */

    'database' => [
        // Connection name (null uses app default)
        'connection' => env('UIS_DB_CONNECTION', null),
        
        // Table prefix for UIS tables
        'table_prefix' => env('UIS_TABLE_PREFIX', 'ai_'),
        
        // Soft delete configuration
        'soft_deletes' => true,
        
        // Audit trail configuration
        'audit_enabled' => env('UIS_AUDIT_ENABLED', true),
        'audit_retention_days' => env('UIS_AUDIT_RETENTION', 90),
        
        // Database optimization
        'indexes' => [
            'auto_create' => true,
            'optimize_queries' => true,
        ],
        
        // Connection pooling
        'pooling' => [
            'enabled' => env('UIS_DB_POOLING', false),
            'min_connections' => env('UIS_DB_POOL_MIN', 2),
            'max_connections' => env('UIS_DB_POOL_MAX', 10),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Integration Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for AI service integration and processing.
    |
    */

    'ai' => [
        // Default AI provider
        'default_provider' => env('UIS_AI_PROVIDER', 'openai'),
        
        // Provider configurations
        'providers' => [
            'openai' => [
                'api_key' => env('OPENAI_API_KEY'),
                'model' => env('UIS_OPENAI_MODEL', 'gpt-4'),
                'max_tokens' => env('UIS_OPENAI_MAX_TOKENS', 4000),
                'temperature' => env('UIS_OPENAI_TEMPERATURE', 0.7),
                'timeout' => env('UIS_OPENAI_TIMEOUT', 30),
                'rate_limit' => [
                    'requests_per_minute' => 60,
                    'tokens_per_minute' => 100000,
                ],
            ],
            'anthropic' => [
                'api_key' => env('ANTHROPIC_API_KEY'),
                'model' => env('UIS_ANTHROPIC_MODEL', 'claude-3-sonnet'),
                'max_tokens' => env('UIS_ANTHROPIC_MAX_TOKENS', 4000),
                'timeout' => env('UIS_ANTHROPIC_TIMEOUT', 30),
            ],
        ],
        
        // Context processing
        'context' => [
            'max_depth' => env('UIS_CONTEXT_DEPTH', 3),
            'auto_enhance' => env('UIS_CONTEXT_AUTO_ENHANCE', true),
            'user_history_limit' => env('UIS_CONTEXT_HISTORY', 10),
            'session_context' => env('UIS_CONTEXT_SESSION', true),
        ],
        
        // Response processing
        'response' => [
            'format_validation' => true,
            'auto_sanitize' => true,
            'quality_checks' => env('UIS_RESPONSE_QUALITY', true),
            'fallback_enabled' => env('UIS_RESPONSE_FALLBACK', true),
        ],
        
        // Retry configuration
        'retry' => [
            'max_attempts' => env('UIS_AI_MAX_RETRIES', 3),
            'delay_seconds' => env('UIS_AI_RETRY_DELAY', 2),
            'backoff_multiplier' => env('UIS_AI_BACKOFF', 2),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Form Processing Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for form structure, validation, and processing.
    |
    */

    'forms' => [
        // Default form configuration
        'defaults' => [
            'theme' => env('UIS_FORM_THEME', 'professional'),
            'layout' => env('UIS_FORM_LAYOUT', 'vertical'),
            'validation_mode' => env('UIS_VALIDATION_MODE', 'realtime'),
            'auto_save' => env('UIS_FORM_AUTOSAVE', true),
            'auto_save_interval' => env('UIS_AUTOSAVE_INTERVAL', 30), // seconds
        ],
        
        // Input type configurations
        'input_types' => [
            'text' => [
                'max_length' => 1000,
                'sanitize' => true,
                'trim' => true,
            ],
            'textarea' => [
                'max_length' => 10000,
                'min_rows' => 3,
                'max_rows' => 20,
                'auto_resize' => true,
            ],
            'select' => [
                'max_options' => 1000,
                'searchable' => true,
                'multiple_limit' => 50,
            ],
            'file' => [
                'max_size' => env('UIS_FILE_MAX_SIZE', 10485760), // 10MB
                'allowed_types' => [
                    'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                    'application/pdf', 'text/plain', 'text/csv',
                    'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                ],
                'virus_scan' => env('UIS_FILE_VIRUS_SCAN', false),
                'storage_disk' => env('UIS_FILE_STORAGE', 'local'),
                'storage_path' => 'uis/uploads',
            ],
        ],
        
        // Validation configuration
        'validation' => [
            'strict_mode' => env('UIS_VALIDATION_STRICT', false),
            'custom_rules' => true,
            'async_validation' => env('UIS_VALIDATION_ASYNC', true),
            'client_side' => env('UIS_VALIDATION_CLIENT', true),
            'error_display' => 'inline', // inline, modal, toast
        ],
        
        // Security settings
        'security' => [
            'csrf_protection' => true,
            'rate_limiting' => [
                'enabled' => env('UIS_RATE_LIMIT', true),
                'max_attempts' => env('UIS_RATE_LIMIT_ATTEMPTS', 10),
                'decay_minutes' => env('UIS_RATE_LIMIT_DECAY', 60),
            ],
            'honeypot' => env('UIS_HONEYPOT', true),
            'spam_detection' => [
                'enabled' => env('UIS_SPAM_DETECTION', true),
                'threshold' => env('UIS_SPAM_THRESHOLD', 0.8),
                'providers' => ['akismet', 'recaptcha'],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Bulk Operations Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for bulk processing and queue management.
    |
    */

    'bulk' => [
        // Queue configuration
        'queue' => [
            'connection' => env('UIS_QUEUE_CONNECTION', 'redis'),
            'queue_name' => env('UIS_QUEUE_NAME', 'uis-bulk'),
            'max_workers' => env('UIS_MAX_WORKERS', 5),
            'timeout' => env('UIS_JOB_TIMEOUT', 300), // 5 minutes
            'retry_after' => env('UIS_JOB_RETRY_AFTER', 600), // 10 minutes
        ],
        
        // Batch processing
        'batch' => [
            'chunk_size' => env('UIS_BATCH_CHUNK_SIZE', 50),
            'max_items' => env('UIS_BATCH_MAX_ITEMS', 10000),
            'parallel_jobs' => env('UIS_BATCH_PARALLEL', 3),
            'memory_limit' => env('UIS_BATCH_MEMORY', 512), // MB
        ],
        
        // Progress tracking
        'progress' => [
            'update_interval' => env('UIS_PROGRESS_INTERVAL', 5), // seconds
            'websocket_enabled' => env('UIS_WEBSOCKET_ENABLED', false),
            'websocket_channel' => 'uis.bulk.progress',
        ],
        
        // Cleanup configuration
        'cleanup' => [
            'completed_jobs_retention' => env('UIS_COMPLETED_RETENTION', 7), // days
            'failed_jobs_retention' => env('UIS_FAILED_RETENTION', 30), // days
            'auto_cleanup' => env('UIS_AUTO_CLEANUP', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Analytics Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for analytics, monitoring, and reporting.
    |
    */

    'analytics' => [
        // Data collection
        'collection' => [
            'enabled' => env('UIS_ANALYTICS_ENABLED', true),
            'anonymous_tracking' => env('UIS_ANONYMOUS_TRACKING', false),
            'user_consent_required' => env('UIS_CONSENT_REQUIRED', true),
            'ip_anonymization' => env('UIS_IP_ANONYMIZE', true),
        ],
        
        // Metrics configuration
        'metrics' => [
            'performance' => true,
            'usage' => true,
            'errors' => true,
            'user_behavior' => env('UIS_BEHAVIOR_TRACKING', false),
            'feature_adoption' => true,
        ],
        
        // Storage configuration
        'storage' => [
            'driver' => env('UIS_ANALYTICS_DRIVER', 'database'),
            'retention_days' => env('UIS_ANALYTICS_RETENTION', 90),
            'aggregation_enabled' => env('UIS_ANALYTICS_AGGREGATION', true),
            'real_time' => env('UIS_ANALYTICS_REALTIME', false),
        ],
        
        // Export configuration
        'export' => [
            'formats' => ['csv', 'json', 'xlsx'],
            'max_records' => env('UIS_EXPORT_MAX_RECORDS', 100000),
            'scheduled_reports' => env('UIS_SCHEDULED_REPORTS', false),
        ],
        
        // Alerting
        'alerts' => [
            'enabled' => env('UIS_ALERTS_ENABLED', true),
            'channels' => ['mail', 'slack', 'webhook'],
            'thresholds' => [
                'error_rate' => env('UIS_ALERT_ERROR_RATE', 5), // percent
                'response_time' => env('UIS_ALERT_RESPONSE_TIME', 5000), // ms
                'queue_depth' => env('UIS_ALERT_QUEUE_DEPTH', 1000),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Localization Configuration
    |--------------------------------------------------------------------------
    |
    | Multi-language support and localization settings.
    |
    */

    'localization' => [
        // Default locale
        'default_locale' => env('UIS_DEFAULT_LOCALE', 'tr'),
        
        // Supported locales
        'supported_locales' => ['tr', 'en', 'de', 'fr', 'es', 'it'],
        
        // Locale detection
        'detection' => [
            'auto_detect' => env('UIS_AUTO_DETECT_LOCALE', true),
            'sources' => ['user_preference', 'session', 'browser', 'default'],
            'fallback_locale' => 'en',
        ],
        
        // Translation configuration
        'translation' => [
            'ai_powered' => env('UIS_AI_TRANSLATION', true),
            'cache_translations' => env('UIS_CACHE_TRANSLATIONS', true),
            'auto_translate' => env('UIS_AUTO_TRANSLATE', false),
            'quality_threshold' => env('UIS_TRANSLATION_QUALITY', 0.8),
        ],
        
        // Content localization
        'content' => [
            'date_format' => 'locale_aware',
            'number_format' => 'locale_aware',
            'currency_format' => 'locale_aware',
            'rtl_support' => env('UIS_RTL_SUPPORT', false),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | Security settings and protective measures.
    |
    */

    'security' => [
        // Encryption
        'encryption' => [
            'sensitive_fields' => env('UIS_ENCRYPT_SENSITIVE', true),
            'algorithm' => env('UIS_ENCRYPTION_ALGO', 'AES-256-GCM'),
            'key_rotation' => env('UIS_KEY_ROTATION', false),
        ],
        
        // Input sanitization
        'sanitization' => [
            'auto_sanitize' => true,
            'html_purifier' => env('UIS_HTML_PURIFIER', true),
            'xss_protection' => true,
            'sql_injection_protection' => true,
        ],
        
        // Access control
        'access_control' => [
            'role_based' => true,
            'permission_caching' => env('UIS_PERMISSION_CACHE', true),
            'session_timeout' => env('UIS_SESSION_TIMEOUT', 3600), // seconds
            'concurrent_sessions' => env('UIS_MAX_SESSIONS', 3),
        ],
        
        // Audit logging
        'audit' => [
            'enabled' => env('UIS_AUDIT_LOGGING', true),
            'events' => [
                'form_submissions' => true,
                'data_access' => true,
                'configuration_changes' => true,
                'user_actions' => false,
            ],
            'retention_days' => env('UIS_AUDIT_RETENTION', 365),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Integration Configuration
    |--------------------------------------------------------------------------
    |
    | External service integrations and API configurations.
    |
    */

    'integrations' => [
        // Webhook configuration
        'webhooks' => [
            'enabled' => env('UIS_WEBHOOKS_ENABLED', false),
            'signature_verification' => env('UIS_WEBHOOK_VERIFY', true),
            'timeout' => env('UIS_WEBHOOK_TIMEOUT', 10), // seconds
            'retry_attempts' => env('UIS_WEBHOOK_RETRIES', 3),
            'events' => [
                'form_submitted',
                'bulk_operation_completed',
                'error_occurred',
                'quota_exceeded',
            ],
        ],
        
        // API configuration
        'api' => [
            'rate_limiting' => [
                'enabled' => env('UIS_API_RATE_LIMIT', true),
                'requests_per_minute' => env('UIS_API_RPM', 60),
                'burst_limit' => env('UIS_API_BURST', 10),
            ],
            'authentication' => [
                'required' => env('UIS_API_AUTH_REQUIRED', true),
                'methods' => ['bearer_token', 'api_key'],
                'token_expiry' => env('UIS_API_TOKEN_EXPIRY', 3600),
            ],
            'versioning' => [
                'strategy' => 'uri', // uri, header, parameter
                'default_version' => 'v3',
                'supported_versions' => ['v3'],
            ],
        ],
        
        // Third-party services
        'services' => [
            'storage' => [
                'aws_s3' => [
                    'enabled' => env('UIS_S3_ENABLED', false),
                    'bucket' => env('UIS_S3_BUCKET'),
                    'region' => env('UIS_S3_REGION', 'us-east-1'),
                ],
                'google_cloud' => [
                    'enabled' => env('UIS_GCS_ENABLED', false),
                    'bucket' => env('UIS_GCS_BUCKET'),
                ],
            ],
            'monitoring' => [
                'sentry' => [
                    'enabled' => env('UIS_SENTRY_ENABLED', false),
                    'dsn' => env('UIS_SENTRY_DSN'),
                ],
                'new_relic' => [
                    'enabled' => env('UIS_NEWRELIC_ENABLED', false),
                    'license_key' => env('UIS_NEWRELIC_KEY'),
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Development Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for development and testing environments.
    |
    */

    'development' => [
        // Debug tools
        'debug_bar' => env('UIS_DEBUG_BAR', false),
        'query_logging' => env('UIS_QUERY_LOG', false),
        'profiling' => env('UIS_PROFILING', false),
        
        // Mock services
        'mock_ai' => env('UIS_MOCK_AI', false),
        'mock_external_apis' => env('UIS_MOCK_APIS', false),
        
        // Testing
        'factory_states' => true,
        'test_data_seeding' => env('UIS_TEST_SEEDING', false),
        'parallel_testing' => env('UIS_PARALLEL_TESTS', false),
        
        // Hot reloading
        'hot_reload' => env('UIS_HOT_RELOAD', false),
        'asset_watching' => env('UIS_WATCH_ASSETS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific features and experimental functionality.
    |
    */

    'features' => [
        // Core features
        'form_builder' => env('UIS_FEATURE_FORM_BUILDER', true),
        'bulk_operations' => env('UIS_FEATURE_BULK_OPS', true),
        'analytics_dashboard' => env('UIS_FEATURE_ANALYTICS', true),
        'context_awareness' => env('UIS_FEATURE_CONTEXT', true),
        
        // Advanced features
        'ai_suggestions' => env('UIS_FEATURE_AI_SUGGESTIONS', true),
        'smart_defaults' => env('UIS_FEATURE_SMART_DEFAULTS', true),
        'dynamic_validation' => env('UIS_FEATURE_DYNAMIC_VALIDATION', true),
        'real_time_collaboration' => env('UIS_FEATURE_COLLABORATION', false),
        
        // Experimental features
        'neural_form_optimization' => env('UIS_FEATURE_NEURAL_OPT', false),
        'predictive_analytics' => env('UIS_FEATURE_PREDICTIVE', false),
        'voice_input' => env('UIS_FEATURE_VOICE', false),
        'gesture_controls' => env('UIS_FEATURE_GESTURES', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Logging settings specific to the Universal Input System.
    |
    */

    'logging' => [
        // Log channels
        'channels' => [
            'default' => env('UIS_LOG_CHANNEL', 'single'),
            'performance' => env('UIS_PERF_LOG_CHANNEL', 'daily'),
            'security' => env('UIS_SEC_LOG_CHANNEL', 'daily'),
            'analytics' => env('UIS_ANALYTICS_LOG_CHANNEL', 'daily'),
        ],
        
        // Log levels
        'levels' => [
            'default' => env('UIS_LOG_LEVEL', 'info'),
            'performance' => env('UIS_PERF_LOG_LEVEL', 'info'),
            'security' => env('UIS_SEC_LOG_LEVEL', 'warning'),
            'analytics' => env('UIS_ANALYTICS_LOG_LEVEL', 'info'),
        ],
        
        // Log formatting
        'format' => [
            'include_context' => true,
            'include_stacktrace' => env('UIS_LOG_STACKTRACE', false),
            'structured_logging' => env('UIS_STRUCTURED_LOGS', true),
        ],
        
        // Log retention
        'retention' => [
            'days' => env('UIS_LOG_RETENTION', 30),
            'max_files' => env('UIS_LOG_MAX_FILES', 10),
            'compress_old' => env('UIS_LOG_COMPRESS', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Module Metadata
    |--------------------------------------------------------------------------
    |
    | Information about the Universal Input System module.
    |
    */

    'metadata' => [
        'name' => 'Universal Input System V3 Professional',
        'description' => 'AI-powered form processing and management system',
        'version' => '3.0.0',
        'author' => 'AI Universal Input System Team',
        'license' => 'MIT',
        'homepage' => 'https://github.com/ai-universal-input-system',
        'documentation' => 'https://docs.ai-universal-input-system.com',
        'support' => 'support@ai-universal-input-system.com',
        'keywords' => [
            'ai', 'forms', 'input', 'processing', 'validation',
            'analytics', 'bulk-operations', 'context-aware'
        ],
        'dependencies' => [
            'php' => '>=8.3',
            'laravel/framework' => '^11.0',
            'guzzlehttp/guzzle' => '^7.0',
            'league/csv' => '^9.0',
            'maatwebsite/excel' => '^3.1',
        ],
    ],
];