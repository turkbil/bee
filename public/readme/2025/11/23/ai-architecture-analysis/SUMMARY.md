# AI Modülü Mimarisi - Tenant-Aware Analizi

## Yönetici Özeti

AI modülü şu anda üç temel problem ile karşı karşıyadır:

1. **Hardcoded Tenant Kontrolleri** - Tenant 2/3 ID'leri 8+ yerde kodda yazılı
2. **Çift Mimarı Patern** - Tenant-specific ve global kurallar karışık
3. **Ölçeklenebilirlik Riski** - 1000+ tenant desteklemesi imkansız

### Geçerli Durum

```
Tenant-Aware Services (iyi):
├── Tenant2PromptService.php (1.132 satır)
├── Tenant2ProductSearchService.php (998 satır)
└── TenantAwareCacheService.php

Global Services (karışık):
├── AIResponseNode.php (hardcoded [2,3] ✗)
├── PublicAIController.php (hardcoded [2,3] ✗)
├── ShopSearchService.php (hardcoded [2,3] ✗3)
├── OptimizedPromptService.php (hardcoded [2,3] ✗)
└── ProductSearchNode.php (factory kullanıyor ✓)
```

## Bulunulan Hardcoded Tenant Kontrolleri

| Dosya | Satırlar | Tip | Çözüm |
|-------|----------|-----|-------|
| AIResponseNode.php | 85, 126 | `in_array([2,3])` | Factory Pattern |
| AIResponseNode.php | 125-170 | İXTİF kuralları (hardcode) | Service'e taşı |
| PublicAIController.php | ~55 | `in_array([2,3])` | Factory Pattern |
| ShopSearchService.php | Çok | `in_array([2,3])` ×3 | Factory Pattern |
| OptimizedPromptService.php | 84, +1 | `in_array([2,3])` | Factory Pattern |

## Ana Sorun: AIResponseNode.php

### Satır 85-94: Tenant2PromptService Yükleme (Hardcoded)

```php
$tenantId = tenant('id') ?? null;
if (in_array($tenantId, [2, 3])) {
    try {
        $tenant2Service = new \Modules\AI\App\Services\Tenant\Tenant2PromptService();
        $tenantPrompt = implode("\n", $tenant2Service->buildPrompt());
        $systemPrompt = $tenantPrompt . "\n\n" . $systemPrompt;
    } catch (\Exception $e) {
        // ...
    }
}
```

**Neden Problem?**
- Yeni tenant eklenirken kod değiştirilmesi gerekir
- Tenant 4, 5, 1001 için ayrı if bloğu yazılmalı
- 1000+ tenant ile bu imkansız

### Satır 125-170: Tenant-Specific Kurallar (ASLA BURAYA YAZILMAMALI!)

```php
// 🏭 TENANT 2 (İXTİF) ÖZEL KURALLARI
if (in_array($tenantId, [2, 3])) {
    $ixtifRules = <<<'IXTIF'
    
## İXTİF ÖZEL KURALLARI:
- Olumsuz kelimeler yasak
- Transpalet isteyince tonnaj sor
- ...
    IXTIF;
    $systemPrompt = $ixtifRules . "\n\n" . $systemPrompt;
}
```

**Neden Hata?**
- Bu kurallar `Tenant2PromptService.php` dosyasında olmalı
- Global Node'de tenant-specific kurallar ASLA olmamalı
- Müzik (Tenant 1001) kurallarını eklerken başka developer bunu hatırlayacak mı?

## Mimarinin Güçlü Tarafları

✓ Tenant-Aware Cache Service
✓ Dinamik Tenant Service Yükleme (Factory Pattern in ProductSearchNode)
✓ Workflow Engine mimarisi (genişletilebilir)
✓ Database Directive Sistemi (yapılandırılabilir)
✓ Hybrid Search entegrasyonu

## Çözüm: TenantServiceFactory

```php
// app/Services/AI/TenantServiceFactory.php
class TenantServiceFactory {
    
    public static function getPromptService(?int $tenantId = null): TenantPromptServiceInterface {
        $tenantId = $tenantId ?? tenant('id');
        
        $serviceClass = "\\App\\Services\\Tenant\\Prompt\\Tenant{$tenantId}PromptService";
        
        if (class_exists($serviceClass)) {
            return app($serviceClass);
        }
        
        return app(DefaultPromptService::class);
    }
    
    public static function getSearchService(?int $tenantId = null) {
        $tenantId = $tenantId ?? tenant('id');
        
        $serviceClass = "\\App\\Services\\Tenant\\Search\\Tenant{$tenantId}ProductSearchService";
        
        if (class_exists($serviceClass)) {
            return app($serviceClass);
        }
        
        return app(DefaultProductSearchService::class);
    }
}
```

### AIResponseNode.php'de Kullanım

```php
// Eski (Hardcoded):
$tenantId = tenant('id') ?? null;
if (in_array($tenantId, [2, 3])) {
    $tenant2Service = new \Modules\AI\App\Services\Tenant\Tenant2PromptService();
    $tenantPrompt = implode("\n", $tenant2Service->buildPrompt());
}

// Yeni (Factory - Ölçeklenebilir):
$promptService = TenantServiceFactory::getPromptService();
$tenantRules = $promptService->getTenantSpecificRules();
$systemPrompt = implode("\n", $tenantRules) . "\n\n" . $systemPrompt;
```

**Avantajlar:**
- Tenant 1001 eklenirse kod değişmez!
- Service yoksa DefaultPromptService kullanılır
- Tenant-specific kurallar hep serviste kalır
- AIResponseNode saf kalır (tenant-neutral)

## Uygulama Planı (4 Faza)

### FAZA 1: Foundation (3-4 saat)
- TenantServiceFactory.php oluştur
- TenantPromptServiceInterface tanımla
- TenantSearchServiceInterface tanımla
- DefaultPromptService (fallback)
- DefaultSearchService (fallback)

### FAZA 2: Mevcut Kodları Refactor (4-5 saat)
- AIResponseNode.php güncelle (factory kullan)
- ProductSearchNode.php güncelle (interface uygun hale getir)
- PublicAIController.php temizle
- Tenant2 servisleri interface implement ettir

### FAZA 3: Testing & Validation (2-3 saat)
- Tenant 2 (ixtif.com) test
- Tenant 2 (ixtif.com.tr) test
- Tenant 1001 (muzibu) test
- Unit test yazma
- Production deployment

### FAZA 4: Tenant 1001 (Müzik) Hazırlanması (8-10 saat)
- Tenant1001PromptService.php oluştur
- Tenant1001SearchService.php oluştur
- Database directives (müzik kuralları)
- Test & Deploy

**Toplam Zaman:** ~17-22 saat
**Benefit:** 1000+ tenant için ölçeklenebilir mimarı

## Tenant-Specific Servisler (Geçerli)

### Tenant2PromptService.php (1.132 satır)
**Tenant:** 2 (ixtif.com), 3 (ixtif.com.tr)

İçeriği:
- Belirsiz istek kuralları (tonnaj, tip sorma)
- Ürün gösterme kriterleri
- Olumsuz kelime yasağı (maalesef, bulunmamaktadır, vb.)
- İletişim bilgisi stratejisi
- Fiyat ve stok politikası

### Tenant2ProductSearchService.php (998 satır)
**Tenant:** 2 (ixtif.com), 3 (ixtif.com.tr)

İçeriği:
- Forklift, transpalet, reach truck kategorileri (ID: 1-6)
- Model numarası çıkarma (F4, EPL153, CPD15)
- Fiyat-bazlı sorgular (en ucuz, en pahalı)
- Yedek parça filtreleme
- HybridSearch entegrasyonu

### Tenant 1001 (Muzibu) - HAZIR DEĞİL
- Tenant1001PromptService.php yok
- Tenant1001SearchService.php yok
- Default kurallar kullanılıyor

## Risk Analizi

### Risk 1: Tenant 2/3 Kuralları Kırılabilir
- **Azaltma:** Kapsamlı unit test, staging test, Tenant 2 ile başlama

### Risk 2: Service Yükleme Başarısızlığı
- **Azaltma:** Try-catch kodu, log kontrol, robust default service

### Risk 3: Performance
- **Azaltma:** Service container caching, TenantAwareCacheService

## Sonuç

Sistem şu anda **"iki mimarı"** kullanıyor:
- **Factory Pattern** (ProductSearchNode) ✓
- **Hardcoded Controls** (AIResponseNode, PublicAIController) ✗

Tutarlı hale getirmek için Factory Pattern'i tüm servislere yaygınlaştır ve 1000+ tenant desteklemesi sağla.

---

**Detaylı Rapor:** https://ixtif.com/readme/2025/11/23/ai-architecture-analysis/
