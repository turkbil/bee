<?php

return [
    'name' => 'SeoManagement',

    /*
    |--------------------------------------------------------------------------
    | Universal SEO Configuration
    |--------------------------------------------------------------------------
    |
    | Bu konfigürasyon dosyası universal SEO sisteminin ayarlarını içerir.
    | Tüm modüllerde kullanılacak ortak SEO özellikleri burada tanımlanır.
    |
    */

    'universal_seo' => [
        
        /*
        |--------------------------------------------------------------------------
        | Desteklenen Model Türleri
        |--------------------------------------------------------------------------
        |
        | SEO sistemi tarafından desteklenen model türleri ve konfigürasyonları.
        | Her model türü için özel ayarlar tanımlanabilir.
        |
        */
        'supported_models' => [
            'Page' => [
                'class' => 'Modules\\Page\\app\\Models\\Page',
                'identifier_field' => 'page_id',
                'title_field' => 'title',
                'custom_fields' => [],
                'ai_features' => ['content_analysis', 'keyword_optimization', 'readability_check']
            ],
            'Announcement' => [
                'class' => 'Modules\\Announcement\\App\\Models\\Announcement',
                'identifier_field' => 'announcement_id',
                'title_field' => 'title',
                'custom_fields' => [],
                'ai_features' => ['content_analysis', 'keyword_optimization']
            ],
            'Portfolio' => [
                'class' => 'Modules\\Portfolio\\app\\Models\\Portfolio',
                'identifier_field' => 'portfolio_id',
                'title_field' => 'title',
                'custom_fields' => ['category_id'],
                'ai_features' => ['image_optimization', 'portfolio_seo']
            ],
            'PortfolioCategory' => [
                'class' => 'Modules\\Portfolio\\app\\Models\\PortfolioCategory',
                'identifier_field' => 'portfolio_category_id',
                'title_field' => 'title',
                'custom_fields' => [],
                'ai_features' => ['category_optimization']
            ],
            // Gelecek modüller için genişletilebilir
        ],

        /*
        |--------------------------------------------------------------------------
        | SEO Form Alanları
        |--------------------------------------------------------------------------
        |
        | Universal SEO formunda gösterilecek alanlar ve konfigürasyonları.
        |
        */
        'form_fields' => [
            'meta_title' => [
                'type' => 'text',
                'max_length' => 60,
                'required' => false,
                'ai_optimize' => true,
                'character_counter' => true
            ],
            'meta_description' => [
                'type' => 'textarea',
                'max_length' => 160,
                'required' => false,
                'ai_optimize' => true,
                'character_counter' => true
            ],
            'meta_keywords' => [
                'type' => 'tags',
                'max_items' => 10,
                'required' => false,
                'ai_suggest' => true
            ],
            'og_title' => [
                'type' => 'text',
                'max_length' => 60,
                'required' => false,
                'auto_fill_from' => 'meta_title'
            ],
            'og_description' => [
                'type' => 'textarea',
                'max_length' => 160,
                'required' => false,
                'auto_fill_from' => 'meta_description'
            ],
            'og_image' => [
                'type' => 'image',
                'required' => false,
                'dimensions' => ['width' => 1200, 'height' => 630]
            ],
            'twitter_title' => [
                'type' => 'text',
                'max_length' => 60,
                'required' => false,
                'auto_fill_from' => 'meta_title'
            ],
            'twitter_description' => [
                'type' => 'textarea',
                'max_length' => 160,
                'required' => false,
                'auto_fill_from' => 'meta_description'
            ],
            'schema_markup' => [
                'type' => 'json',
                'required' => false,
                'ai_generate' => true
            ]
        ],

        /*
        |--------------------------------------------------------------------------
        | Tab Konfigürasyonu
        |--------------------------------------------------------------------------
        |
        | Universal SEO formundaki tab yapısı.
        |
        */
        'tabs' => [
            [
                'key' => 'basic_seo',
                'name' => 'Temel SEO',
                'icon' => 'search',
                'fields' => ['meta_title', 'meta_description', 'meta_keywords']
            ],
            [
                'key' => 'social_media',
                'name' => 'Sosyal Medya',
                'icon' => 'share',
                'fields' => ['og_title', 'og_description', 'og_image', 'twitter_title', 'twitter_description']
            ],
            [
                'key' => 'advanced',
                'name' => 'Gelişmiş',
                'icon' => 'settings',
                'fields' => ['schema_markup']
            ],
            [
                'key' => 'ai_analysis',
                'name' => 'AI Analiz',
                'icon' => 'brain',
                'fields' => [],
                'component' => 'seo-ai-analysis'
            ]
        ],

        /*
        |--------------------------------------------------------------------------
        | AI Özellikler Konfigürasyonu
        |--------------------------------------------------------------------------
        |
        | Yapay zeka destekli SEO özellikleri için ayarlar.
        |
        */
        'ai_features' => [
            'seo_score' => [
                'enabled' => true,
                'real_time' => false,
                'criteria' => [
                    'title_length' => 10,
                    'description_length' => 15,
                    'keyword_density' => 20,
                    'readability' => 25,
                    'content_structure' => 30
                ]
            ],
            'content_optimization' => [
                'enabled' => true,
                'suggestions' => true,
                'auto_improve' => false
            ],
            'keyword_research' => [
                'enabled' => true,
                'suggest_related' => true,
                'competition_analysis' => false
            ],
            'schema_generation' => [
                'enabled' => true,
                'auto_detect_type' => true,
                'custom_schemas' => []
            ]
        ],

        /*
        |--------------------------------------------------------------------------
        | Cache Ayarları
        |--------------------------------------------------------------------------
        |
        | SEO verilerinin cache edilmesi için ayarlar.
        |
        */
        'cache' => [
            'enabled' => true,
            'ttl' => 3600, // 1 saat
            'tags' => ['seo', 'universal_seo'],
            'prefix' => 'universal_seo_'
        ],

        /*
        |--------------------------------------------------------------------------
        | Validation Kuralları
        |--------------------------------------------------------------------------
        |
        | Universal SEO form alanları için validation kuralları.
        |
        */
        'validation_rules' => [
            'meta_title.*' => 'nullable|string|max:60',
            'meta_description.*' => 'nullable|string|max:160',
            'meta_keywords.*' => 'nullable|string|max:255',
            'og_title.*' => 'nullable|string|max:60',
            'og_description.*' => 'nullable|string|max:160',
            'og_image' => 'nullable|image|max:2048',
            'twitter_title.*' => 'nullable|string|max:60',
            'twitter_description.*' => 'nullable|string|max:160',
            'schema_markup' => 'nullable|json'
        ],

        /*
        |--------------------------------------------------------------------------
        | Dil Desteği
        |--------------------------------------------------------------------------
        |
        | Çoklu dil desteği için ayarlar.
        |
        */
        'multilingual' => [
            'enabled' => true,
            'default_language' => function() {
                // Dinamik default dil
                try {
                    return \App\Services\TenantLanguageProvider::getDefaultLanguageCode();
                } catch (\Exception $e) {
                    return 'tr'; // Fallback
                }
            },
            'supported_languages' => function() {
                // Dinamik desteklenen diller
                try {
                    return \App\Services\TenantLanguageProvider::getActiveLanguageCodes();
                } catch (\Exception $e) {
                    return ['tr', 'en']; // Fallback
                }
            },
            'field_suffix' => '_multilang'
        ],

        /*
        |--------------------------------------------------------------------------
        | İzinler
        |--------------------------------------------------------------------------
        |
        | SEO yönetimi için gerekli izinler.
        |
        */
        'permissions' => [
            'seo.view' => 'SEO Görüntüle',
            'seo.create' => 'SEO Oluştur',
            'seo.edit' => 'SEO Düzenle',
            'seo.delete' => 'SEO Sil',
            'seo.ai_features' => 'SEO AI Özellikleri',
            'seo.advanced' => 'Gelişmiş SEO'
        ]
    ]
];
