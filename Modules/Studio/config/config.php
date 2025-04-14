<?php

return [
    'name' => 'Studio',
    
    /*
    |--------------------------------------------------------------------------
    | Editör Yapılandırması
    |--------------------------------------------------------------------------
    |
    | Editörün çekirdek davranışını ve görünüşünü yapılandırır
    |
    */
    'editor' => [
        'version' => '0.21.8',   // GrapesJS sürümü
        'use_cdn' => false,      // GrapesJS'i CDN'den yükle/yükleme
        
        // Tema ve görsel yapılandırma
        'theme' => 'light',      // light veya dark tema
        'canvas_styles' => [
            'bootstrap' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
            'fontawesome' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
        ],
        'canvas_scripts' => [
            'bootstrap' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js',
        ],
        
        // Panel yapılandırması
        'panels' => [
            'default' => true,  // Varsayılan panelleri yükle
            'styles' => true,   // Stil panelini göster
            'layers' => true,   // Katmanlar panelini göster
            'blocks' => true,   // Bloklar panelini göster
            'traits' => true,   // Özellikler panelini göster
        ],
        
        // Aktif eklentiler
        'plugins' => [
            'blocks-basic' => true,       // Temel bloklar
            'preset-webpage' => true,     // Sayfa şablonu
            'forms' => true,              // Form bileşenleri
            'custom-code' => true,        // Özel kod
            'touch' => true,              // Dokunmatik destek
        ],
        
        // Cihaz ayarları
        'devices' => [
            'desktop' => [
                'name' => 'Masaüstü',
                'width' => '',
            ],
            'tablet' => [
                'name' => 'Tablet',
                'width' => '768px',
                'widthMedia' => '992px',
            ],
            'mobile' => [
                'name' => 'Mobil',
                'width' => '320px',
                'widthMedia' => '576px',
            ],
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Blok Yapılandırması
    |--------------------------------------------------------------------------
    |
    | Editör bloklarının yapılandırması ve kategorileri
    |
    */
    'blocks' => [
        'categories' => [
            'temel' => 'Temel Bileşenler',
            'mizanpaj' => 'Mizanpaj Bileşenleri',
            'medya' => 'Medya Bileşenleri', 
            'bootstrap' => 'Bootstrap Bileşenleri',
            'özel' => 'Özel Bileşenler',
            'widget' => 'Widgetlar'
        ],
        
        // Varsayılan olarak yüklenecek blok setleri
        'default_sets' => [
            'basic' => true,      // Paragraf, başlık, liste vb.
            'layout' => true,     // Konteyner, satır, sütun vb.
            'media' => true,      // Görsel, video, harita vb.
            'bootstrap' => true,  // Bootstrap bileşenleri
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Widget Yapılandırması
    |--------------------------------------------------------------------------
    |
    | Widget entegrasyon ayarları
    |
    */
    'widgets' => [
        'enable' => true,             // Widget entegrasyonu
        'auto_load' => true,          // Otomatik yükle
        'reload_on_save' => true,     // Kaydetme sonrası yenile
        'cache_ttl' => 60 * 24,       // Önbellek süresi (dakika)
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Tema Yapılandırması
    |--------------------------------------------------------------------------
    |
    | Tema sistemi ayarları
    |
    */
    'themes' => [
        'enable' => true,       // Tema sistemi
        'default' => 'default', // Varsayılan tema
        'auto_load' => true,    // Temaları otomatik yükle
        'cache_ttl' => 60 * 24, // Önbellek süresi (dakika)
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Varlık Yapılandırması
    |--------------------------------------------------------------------------
    |
    | CSS/JS varlıklarını yüklemek için ayarlar
    |
    */
    'assets' => [
        'auto_publish' => true,   // Varlıkları otomatik yayınla
        'minify' => true,         // Production modunda sıkıştır
        'use_cdn' => false,       // CDN kullan/kullanma
        
        // GrapesJS temel varlıkları
        'core' => [
            'css' => [
                'editor' => 'css/grapes.min.css',
                'editor-ui' => 'css/studio-editor-ui.css',
                'editor-overrides' => 'css/studio-grapes-overrides.css',
            ],
            'js' => [
                'editor' => 'js/grapes.min.js',
                'bootstrap' => 'js/studio-bootstrap.js',
                'core' => 'js/core/studio-core.js',
                'blocks' => 'js/blocks/registry.js',
                'actions' => 'js/actions/registry.js',
                'ui' => 'js/ui/registry.js',
            ],
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Önbellek Yapılandırması
    |--------------------------------------------------------------------------
    |
    | Önbellekleme stratejisi ayarları
    |
    */
'cache' => [
    'enable' => false,       // Önbellek devre dışı
    'driver' => 'redis',    // Önbellek sürücüsü
    'ttl' => 60 * 24,       // Varsayılan önbellek süresi (dakika)
    'prefix' => 'studio_',  // Önbellek anahtar öneki
],
];