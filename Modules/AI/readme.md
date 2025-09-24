# 🌍 GLOBAL AI CONTENT GENERATION SYSTEM

Bu sistem, tüm modüller için AI content generation desteği sağlayan GLOBAL ve MODULE-AGNOSTIC bir yaklaşımdır.

## ✨ Özellikler

- **Module-Agnostic**: Herhangi bir modül kullanabilir
- **GLOBAL Service**: Tek bir AI Content Generator Service tüm modüller için çalışır
- **Queue Support**: Asenkron işlem desteği
- **Theme Integration**: Tema analizi ile uyumlu içerik üretimi
- **Credit Management**: Otomatik kredi hesaplama ve tracking
- **Event System**: Real-time bildirimler
- **Interface Standardı**: Standart interface ile tutarlı implementation

## 🏗️ Mimari

```
Modules/AI/
├── app/
│   ├── Services/Content/
│   │   └── AIContentGeneratorService.php (GLOBAL service)
│   ├── Traits/
│   │   └── HasAIContentGeneration.php (GLOBAL trait)
│   ├── Contracts/
│   │   └── AIContentGeneratable.php (Interface)
│   ├── Jobs/
│   │   └── AIContentGenerationJob.php (Queue job)
│   └── Events/Content/
│       ├── ContentGenerationCompleted.php
│       └── ContentGenerationFailed.php
```

## 🚀 Kullanım

### 1. Modül Component'ine Trait Ekleme

```php
use Modules\AI\app\Traits\HasAIContentGeneration;
use Modules\AI\app\Contracts\AIContentGeneratable;

class YourModuleComponent extends Component implements AIContentGeneratable
{
    use HasAIContentGeneration;

    // Interface metodlarını implement et
    public function getEntityType(): string
    {
        return 'your_entity_type';
    }

    public function getTargetFields(array $params): array
    {
        return [
            'title' => 'string',
            'content' => 'html',
            'description' => 'text'
        ];
    }

    public function getModuleInstructions(): string
    {
        return 'Modül-specific AI talimatları buraya';
    }
}
```

### 2. AI Content Generation Kullanımı

```php
// Basit kullanım
$result = $this->generateAIContent([
    'prompt' => 'Blog yazısı için içerik üret',
    'target_field' => 'content',
    'content_type' => 'blog',
    'length' => 'long'
]);

// Gelişmiş kullanım
$result = $this->generateAIContent([
    'prompt' => 'E-ticaret ürün açıklaması',
    'target_field' => 'description',
    'content_type' => 'product',
    'length' => 'medium',
    'specific_requirements' => 'SEO odaklı, özellikleri vurgula'
]);
```

### 3. Batch Content Generation

```php
$fields = [
    'title' => 'Ürün başlığı üret',
    'description' => 'Ürün açıklaması üret',
    'features' => 'Ürün özelliklerini listele'
];

$results = $this->generateBatchAIContent($fields, [
    'content_type' => 'product',
    'length' => 'medium'
]);
```

## 🎯 Desteklenen Modüller

### Page Modülü
```php
$this->generatePageAIContent('Hizmetler sayfası içeriği', 'body');
```

### Blog Modülü (örnek)
```php
$this->generateBlogAIContent('Teknoloji trendi makalesi', 'content');
```

### Product Modülü (örnek)
```php
$this->generateProductAIContent('Akıllı telefon özellikleri', 'description');
```

## ⚙️ Configuration

### Module Context Parameters

```php
$moduleContext = [
    'module' => 'Blog',                    // Modül adı
    'entity_type' => 'article',            // Entity tipi
    'fields' => ['title', 'content'],      // Hedef alanlar
    'instructions' => 'Blog yazısı üret',  // Modül talimatları
    'specific_requirements' => 'SEO odaklı' // Özel gereksinimler
];
```

### Content Types

- `auto` - Otomatik tespit
- `hero` - Hero section
- `features` - Özellikler
- `about` - Hakkında
- `service` - Hizmet
- `product` - Ürün
- `blog` - Blog yazısı
- `page` - Sayfa içeriği

### Length Options

- `short` - Kısa (≤500 token)
- `medium` - Orta (≤1000 token)
- `long` - Uzun (≤2000 token)
- `ultra_long` - Ultra uzun (≤4000 token)

## 🔧 Özelleştirme

### Custom Post-Processing

```php
protected function postProcessAIContent(array $result, array $params): array
{
    if ($result['success']) {
        // Modül-specific post-processing
        $result['content'] = $this->customizeContent($result['content']);
    }

    return $result;
}
```

### Custom Validation

```php
private function validateModuleContent(string $content, string $fieldType): bool
{
    // Modül-specific validation logic
    return true;
}
```

## 📊 Credit System

AI content generation otomatik olarak kredi hesaplaması yapar:

- **Simple content**: 3 kredi
- **Moderate content**: 5 kredi
- **Complex content**: 10 kredi
- **Template content**: 2 kredi

Uzunluk multiplikatörleri:
- Short: 0.7x
- Medium: 1.0x
- Long: 1.5x
- Ultra Long: 2.0x

## 🎉 Events

### ContentGenerationCompleted
```php
event(new ContentGenerationCompleted(
    $sessionId,
    $component,
    $result,
    $tenantId,
    $userId
));
```

### ContentGenerationFailed
```php
event(new ContentGenerationFailed(
    $sessionId,
    $component,
    $error,
    $tenantId,
    $userId
));
```

## 🚦 Queue System

Büyük içerik üretimleri için queue sistemi kullanılır:

```php
AIContentGenerationJob::dispatch($params, $sessionId, 'YourComponent');
```

Progress tracking:
```php
$progress = Cache::get("ai_content_progress_{$sessionId}");
// { percentage: 75, message: 'İçerik işleniyor...', status: 'processing' }
```

## 🔍 Logging

Tüm AI operations detaylı loglanır:

```php
Log::info('🌍 GLOBAL AI Content Generation başlatıldı', [
    'module' => 'Page',
    'entity_type' => 'page',
    'target_field' => 'body'
]);
```

## 🛡️ Security

- XSS koruması (script tag filtering)
- Content sanitization
- Input validation
- Rate limiting (kredi sistemi ile)

## 📝 Best Practices

1. **Interface Implementation**: Her modül AIContentGeneratable interface'ini implement etmeli
2. **Module Context**: Module-specific context bilgilerini doğru şekilde tanımla
3. **Error Handling**: Trait'in error handling metodlarını kullan
4. **Validation**: Generated content'i validate et
5. **Performance**: Büyük işlemler için queue kullan

## 🎯 Migration Guide

Eski ThemeManagement service'den yeni GLOBAL service'e geçiş:

```php
// ESKİ
use Modules\ThemeManagement\app\Services\AIContentGeneratorService;
$service = new AIContentGeneratorService();

// YENİ
use Modules\AI\app\Traits\HasAIContentGeneration;
class YourComponent implements AIContentGeneratable {
    use HasAIContentGeneration;

    $result = $this->generateAIContent($params);
}
```