<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Tenant-Specific AI Prompt Services
    |--------------------------------------------------------------------------
    |
    | Her tenant için kullanılacak prompt service class'ını belirtir.
    | Bu mapping sayesinde hangi tenant'ın hangi prompt servisini kullanacağı
    | merkezi bir yerden yönetilir.
    |
    | KEY: Tenant ID
    | VALUE: Tam qualified prompt service class ismi
    |
    */
    'prompt_services' => [
        // Tenant 2 & 3: İxtif (Endüstriyel Ekipman)
        2 => \Modules\AI\App\Services\Tenant2\PromptService::class,
        3 => \Modules\AI\App\Services\Tenant2\PromptService::class,

        // Tenant 1001: Muzibu (Müzik Platformu)
        1001 => \Modules\AI\App\Services\Tenant1001\PromptService::class,

        // Diğer tenant'lar eklenebilir:
        // 1002 => \Modules\AI\App\Services\Tenant\Tenant1002PromptService::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Prompt Validation Rules
    |--------------------------------------------------------------------------
    |
    | AI prompt'larının geçerliliğini kontrol etmek için kullanılan kurallar.
    | Bu kurallar runtime validation sırasında kontrol edilir.
    |
    */
    'validation' => [
        // Minimum prompt uzunluğu (karakter)
        // Çok kısa prompt'lar genellikle yeterince detaylı değildir
        'min_prompt_length' => 1000,

        // Maksimum selamlama cevabı uzunluğu (karakter)
        // Kullanıcı sadece "merhaba" dediğinde AI'ın cevabı bu kadar olmalı
        'max_greeting_response' => 50,

        // Tenant 2/3 için özel kritik kurallar kontrolü
        // Bu tenant'larda prompt içinde mutlaka olması gereken anahtar kelimeler
        'critical_keywords' => [
            2 => ['ULTRA KRİTİK', 'KRİTİK KURAL'],
            3 => ['ULTRA KRİTİK', 'KRİTİK KURAL'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenAI API Parameters
    |--------------------------------------------------------------------------
    |
    | OpenAI API çağrılarında kullanılacak varsayılan parametreler.
    | Tenant bazında override edilebilir.
    |
    */
    'openai' => [
        'default' => [
            'model' => 'gpt-4o-mini',
            'temperature' => 0.5,
            'max_tokens' => 500,
            'top_p' => 1.0,
            'frequency_penalty' => 0.0,
            'presence_penalty' => 0.0,
        ],

        // Tenant-specific overrides
        'overrides' => [
            2 => [
                'temperature' => 0.2, // İxtif için daha deterministik
                'max_tokens' => 500,
            ],
            1001 => [
                'temperature' => 0.7, // Muzibu için daha kreatif
                'max_tokens' => 800,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Module Context Mapping
    |--------------------------------------------------------------------------
    |
    | Hangi modülün hangi tenant'larda aktif olduğunu belirtir.
    | Bu sayede yanlış modül kullanımı önlenir.
    |
    */
    'module_availability' => [
        'shop' => [2, 3], // Ürün modülü sadece İxtif'te
        'music' => [1001], // Müzik modülü sadece Muzibu'da
        'blog' => [1, 2, 3, 1001], // Blog her yerde
        'page' => [1, 2, 3, 1001], // Page her yerde
    ],

    /*
    |--------------------------------------------------------------------------
    | Debug & Logging
    |--------------------------------------------------------------------------
    |
    | AI prompt debug ve log ayarları.
    |
    */
    'debug' => [
        // Prompt'ları log dosyasına yaz
        'log_prompts' => env('AI_LOG_PROMPTS', false),

        // Validation hatalarını log'la
        'log_validation_errors' => true,

        // Wrong service kullanımını detect et ve log'la
        'detect_wrong_service' => true,
    ],
];
