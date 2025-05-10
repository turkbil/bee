<?php

return [
    'name' => 'AI',
    
    'deepseek' => [
        // API Anahtarı - .env dosyasından alınır
        'api_key' => env('DEEPSEEK_API_KEY', ''),

        // API URL
        'api_url' => env('DEEPSEEK_API_URL', 'https://api.deepseek.com/v1'),

        // Kullanılacak Model
        'model' => env('DEEPSEEK_MODEL', 'deepseek-chat'),

        // Sistem Mesajı
        'system_message' => env('DEEPSEEK_SYSTEM_MESSAGE', 'Sen bir asistansın. Sorulara kapsamlı ve doğru cevaplar verirsin. Türkçe olarak cevap ver.'),

        // Sıcaklık parametresi (0.0 - 1.0 arası)
        'temperature' => env('DEEPSEEK_TEMPERATURE', 0.7),

        // Maksimum token sayısı
        'max_tokens' => env('DEEPSEEK_MAX_TOKENS', 2000),
        
        // Loglamayı etkinleştir/devre dışı bırak
        'enable_logging' => env('DEEPSEEK_ENABLE_LOGGING', true),
        
        // Konuşma geçmişinin önbellekte kalma süresi (saniye)
        'cache_ttl' => env('DEEPSEEK_CACHE_TTL', 86400), // 24 saat
    ],
    
    'cache_prefix' => 'ai_',
    'redis_enabled' => env('AI_REDIS_ENABLED', true),
    'default_daily_limit' => env('AI_DEFAULT_DAILY_LIMIT', 100),
    'default_monthly_limit' => env('AI_DEFAULT_MONTHLY_LIMIT', 3000),
];