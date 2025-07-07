# AI Service Global Kullanım Örnekleri

## 🚀 Hızlı Başlangıç

```php
// Page modülünde blog yazısı oluşturma
$result = ai_page()->generateContent('Laravel ile API Geliştirme', 'blog_post');

// SEO analizi yapma
$seoResult = ai_page()->analyzeSEO($content, 'laravel api');

// İçerik çevirisi
$translation = ai_page()->translate($content, 'en');
```

## 📦 Facade Kullanımı

### Temel Kullanım
```php
use App\Facades\AI;

// Modül için AI builder
$pageAI = AI::page();
$portfolioAI = AI::portfolio();
$studioAI = AI::studio();

// Action çalıştırma
$result = AI::page()
    ->action('generateContent')
    ->withTitle('Yeni Blog Yazısı')
    ->withParameter('content_type', 'blog_post')
    ->withLanguage('tr')
    ->execute();
```

### Fluent API
```php
$result = AI::page()
    ->action('generateContent')
    ->withTitle('E-ticaret Rehberi')
    ->withParameter('content_type', 'guide')
    ->withParameter('word_count', 1000)
    ->withLanguage('tr')
    ->forTenant('tenant_123')
    ->execute();
```

## 🛠️ Helper Fonksiyonları

### İçerik Oluşturma
```php
// Blog yazısı oluşturma
$result = ai_generate_content('page', 'Laravel Nedir?', 'blog_post', [
    'word_count' => 500,
    'language' => 'tr'
]);

// Ürün sayfası oluşturma
$product = ai_generate_content('page', 'iPhone 15 Pro', 'product_page', [
    'features' => ['A17 Pro chip', '48MP Camera', 'Titanium Design'],
    'price' => '999 USD'
]);
```

### SEO İşlemleri
```php
// SEO analizi
$seoAnalysis = ai_analyze_seo('page', $content, 'laravel framework');

// Meta etiketleri oluşturma
$metaTags = ai_generate_meta_tags('page', $content, 'Laravel Framework Rehberi');

// SEO optimizasyonu
$optimized = ai_for_module('page')
    ->action('optimizeSEO')
    ->withContent($content)
    ->withParameter('target_keyword', 'laravel tutorial')
    ->withParameter('meta_title', 'Komple Laravel Rehberi')
    ->execute();
```

### Çeviri İşlemleri
```php
// Basit çeviri
$translation = ai_translate('page', $content, 'en');

// Detaylı çeviri
$result = AI::page()
    ->action('translateContent')
    ->withContent($content)
    ->withParameter('target_language', 'en')
    ->withParameter('preserve_seo', true)
    ->execute();
```

### Token Yönetimi
```php
// Token durumu kontrolü
$tokenStatus = ai_check_tokens();
echo "Kalan Token: " . $tokenStatus['remaining_tokens'];

// Token kullanım kontrolü
if (ai_can_use_tokens(500)) {
    $result = ai_generate_content('page', 'Blog Yazısı', 'blog_post');
}

// Token tahmini
$estimatedTokens = ai_estimate_tokens('page', 'generateContent', [
    'title' => 'Uzun Blog Yazısı',
    'word_count' => 2000
]);
```

## 📄 Page Modülü Örnekleri

### Blog Yazısı Oluşturma
```php
$blogPost = AI::page()
    ->action('generateContent')
    ->withTitle('WordPress\'den Laravel\'e Geçiş Rehberi')
    ->withParameter('content_type', 'blog_post')
    ->withParameter('word_count', 1500)
    ->withParameter('target_audience', 'developers')
    ->withLanguage('tr')
    ->execute();

if ($blogPost['success']) {
    $content = $blogPost['data']['content'];
    $tokensUsed = $blogPost['tokens_used'];
    
    // İçeriği kaydet
    Page::create([
        'title' => 'WordPress\'den Laravel\'e Geçiş Rehberi',
        'content' => $content,
        'ai_generated' => true,
        'tokens_used' => $tokensUsed
    ]);
}
```

### Şablondan İçerik Oluşturma
```php
$productPage = AI::page()
    ->action('generateFromTemplate')
    ->withParameter('template', 'product_page')
    ->withParameter('variables', [
        'product_name' => 'Akıllı Saat Pro',
        'category' => 'Teknoloji',
        'price' => '2.999 TL',
        'features' => [
            'Su geçirmez',
            '7 gün pil ömrü',
            'GPS tracking',
            'Kalp ritmi ölçümü'
        ]
    ])
    ->execute();
```

### İçerik İyileştirme
```php
// Mevcut içeriği genişletme
$expanded = AI::page()
    ->action('expandContent')
    ->withContent($existingContent)
    ->withParameter('expansion_type', 'detailed_examples')
    ->withParameter('target_length', 2000)
    ->execute();

// İçerik iyileştirme önerileri
$suggestions = AI::page()
    ->action('suggestImprovements')
    ->withContent($content)
    ->execute();

// İçeriği yeniden yazma
$rewritten = AI::page()
    ->action('rewriteContent')
    ->withContent($content)
    ->withParameter('rewrite_style', 'more_professional')
    ->execute();
```

### Okunabilirlik Analizi
```php
$readability = AI::page()
    ->action('analyzeReadability')
    ->withContent($content)
    ->execute();

if ($readability['success']) {
    $score = $readability['data']['readability_score'];
    $suggestions = $readability['data']['improvement_suggestions'];
    
    echo "Okunabilirlik Skoru: $score/100";
    foreach ($suggestions as $suggestion) {
        echo "- $suggestion";
    }
}
```

## 🔄 Toplu İşlemler

### Batch İçerik Oluşturma
```php
$titles = [
    'Laravel Routing Rehberi',
    'Eloquent ORM İpuçları',
    'Laravel Security Best Practices'
];

$results = ai_batch_process('page', $titles, 'generateContent', [
    'content_type' => 'blog_post',
    'word_count' => 800,
    'language' => 'tr'
]);

foreach ($results as $key => $result) {
    if ($result['success']) {
        echo "✅ {$titles[$key]} - {$result['tokens_used']} token kullanıldı";
    } else {
        echo "❌ {$titles[$key]} - Hata: {$result['error']}";
    }
}
```

### Çoklu Çeviri
```php
$languages = ['en', 'es', 'fr', 'de'];
$translations = [];

foreach ($languages as $lang) {
    $result = ai_translate('page', $content, $lang);
    if ($result['success']) {
        $translations[$lang] = $result['data']['content'];
    }
}
```

## 🎯 Gelişmiş Kullanım

### Özel Token Limiti
```php
$result = AI::page()
    ->action('generateContent')
    ->withTitle('Çok Uzun Makale')
    ->withTokenLimit(2000)
    ->execute();
```

### Tenant Bazlı İşlem
```php
$result = AI::page()
    ->forTenant('tenant_456')
    ->action('generateContent')
    ->withTitle('Tenant Özel İçerik')
    ->execute();
```

### Hata Yönetimi
```php
try {
    $result = ai_generate_content('page', 'Test Başlık', 'blog_post');
    
    if (!$result['success']) {
        throw new Exception($result['error']);
    }
    
    $content = $result['data']['content'];
    $tokensUsed = $result['tokens_used'];
    
} catch (Exception $e) {
    Log::error('AI içerik oluşturma hatası', [
        'error' => $e->getMessage(),
        'title' => 'Test Başlık'
    ]);
    
    // Fallback içerik kullan
    $content = 'Varsayılan içerik...';
}
```

### Stream İstekleri
```php
$messages = [
    ['role' => 'user', 'content' => 'Laravel hakkında blog yazısı yaz']
];

foreach (AI::sendStreamRequest($messages, tenant('id'), 'page') as $chunk) {
    if (isset($chunk['content'])) {
        echo $chunk['content']; // Real-time output
        flush();
    }
}
```

## 📊 İstatistik ve Monitoring

### Token Kullanım Takibi
```php
$stats = ai_check_tokens();

echo "Günlük Kullanım: {$stats['daily_usage']}";
echo "Aylık Kullanım: {$stats['monthly_usage']}";
echo "Kalan Token: {$stats['remaining_tokens']}";
echo "Provider: {$stats['provider']}";
```

### Modül Durumu Kontrolü
```php
if (ai_is_module_available('page')) {
    $actions = ai_get_supported_actions('page');
    echo "Desteklenen Action'lar: " . implode(', ', $actions);
} else {
    echo "Page AI entegrasyonu aktif değil";
}
```

### Ham AI İsteği
```php
$response = ai_quick_request(
    'Laravel projemde performans optimizasyonu nasıl yaparım?',
    tenant('id'),
    'page'
);

if ($response['success']) {
    echo $response['data']['content'];
}
```

## 🔧 Configuration Examples

### .env Ayarları
```bash
# AI Provider
AI_DEFAULT_PROVIDER=deepseek
DEEPSEEK_API_KEY=your_api_key_here

# Token Management
AI_DEFAULT_DAILY_LIMIT=1000
AI_DEFAULT_MONTHLY_LIMIT=30000

# Page Integration
AI_PAGE_INTEGRATION_ENABLED=true
AI_PAGE_DEFAULT_WORD_COUNT=500
AI_PAGE_MAX_CONTENT_LENGTH=10000

# Performance
AI_CACHE_RESPONSES=true
AI_RESPONSE_CACHE_TTL=3600
AI_REQUEST_TIMEOUT=60

# Logging
AI_LOGGING_ENABLED=true
AI_LOG_TOKEN_USAGE=true
```

Bu örnekler, AI sisteminin nasıl kullanılacağını göstermektedir. Sistem tamamen modüler olduğu için her modül kendi AI entegrasyonunu tanımlayabilir ve bu örnekler referans alınarak genişletilebilir.