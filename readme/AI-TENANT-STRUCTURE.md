# 🏗️ AI Workflow - Tenant Yapısı Kılavuzu

## 📋 Temel Kural

**⚠️ KRİTİK: Tenant-specific tüm kodlar `Tenant{X}` prefix'li dosyalarda olmalı!**

Global node'lar ve servisler **ASLA** tenant-specific keyword, kategori mapping veya business logic içermemelidir.

---

## 🌍 Global vs Tenant-Specific

### ✅ GLOBAL Dosyalar (Tüm Tenant'lar İçin)

**Konum:** `Modules/AI/app/Services/Workflow/Nodes/`

- `ProductSearchNode.php` - Genel arama node'u (tenant service yükler)
- `StockSorterNode.php` - Genel sıralama logic'i
- `ContextBuilderNode.php` - Markdown context oluşturucu
- `AIResponseNode.php` - AI yanıt üretici
- `CategoryDetectionNode.php` - Genel kategori tespiti (varsayılan: null)

**Kurallar:**
- ❌ Hiçbir tenant keyword'ü yok (transpalet, forklift, vb.)
- ❌ Hiçbir tenant kategori mapping'i yok
- ❌ Hiçbir tenant business rule yok
- ✅ Config-driven çalışır
- ✅ Tenant service'leri kullanır

---

### 🏢 TENANT-SPECIFIC Dosyalar

**Konum:** `Modules/AI/app/Services/Tenant/`

**Naming Convention:** `Tenant{ID}*.php`

#### Örnek: Tenant 2 (iXtif.com)

**Dosya:** `Tenant2ProductSearchService.php`

**İçerir:**
```php
// ✅ TENANT 2 SPECIFIC - Keyword mapping
protected function extractKeywords(string $message): array
{
    $keywords = [];
    $productTypes = [
        'transpalet', 'forklift', 'istif',
        'order picker', 'reach truck', 'otonom'
    ];
    // ...
}

// ✅ TENANT 2 SPECIFIC - Category mapping
protected function detectCategoryId(string $message): ?int
{
    $categoryMap = [
        'forklift' => 1,      // İXTİF kategori ID
        'transpalet' => 2,    // İXTİF kategori ID
        'istif' => 3,         // İXTİF kategori ID
        // ...
    ];
    // ...
}

// ✅ TENANT 2 SPECIFIC - Search logic
public function search(string $userMessage, int $limit, ?int $categoryId): array
{
    // Tenant 2'ye özel arama mantığı
}
```

---

## 📂 Dosya Yapısı

```
Modules/AI/
├── app/
│   ├── Services/
│   │   ├── Workflow/
│   │   │   ├── Nodes/              # 🌍 GLOBAL NODE'LAR
│   │   │   │   ├── ProductSearchNode.php     (tenant service loader)
│   │   │   │   ├── StockSorterNode.php       (genel sıralama)
│   │   │   │   ├── ContextBuilderNode.php    (markdown builder)
│   │   │   │   └── AIResponseNode.php        (AI yanıt)
│   │   │   │
│   │   │   └── FlowExecutor.php    # 🌍 GLOBAL executor
│   │   │
│   │   └── Tenant/                 # 🏢 TENANT-SPECIFIC SERVİSLER
│   │       ├── Tenant2ProductSearchService.php     # iXtif.com (ID: 2)
│   │       ├── Tenant3ProductSearchService.php     # İkinci tenant
│   │       └── Tenant4ProductSearchService.php     # Üçüncü tenant
│   │
│   └── Models/
│       ├── Flow.php                # 🌍 GLOBAL model (tenant_id ile filtreli)
│       └── AITenantDirective.php   # 🌍 GLOBAL model (tenant_id ile filtreli)
```

---

## 🔄 Çalışma Mantığı

### 1. ProductSearchNode (GLOBAL)

```php
public function execute(array $context): array
{
    $tenantId = $context['tenant_id'] ?? tenant('id');

    // Try to load tenant-specific service
    $searchService = $this->getTenantSearchService($tenantId);

    if ($searchService) {
        // ✅ Tenant has custom service - use it
        return $searchService->search($userMessage, $limit, $categoryId);
    }

    // ❌ No custom service - use generic fallback
    return $this->genericSearch($userMessage, $limit);
}

protected function getTenantSearchService(?int $tenantId)
{
    $serviceClass = "\\Modules\\AI\\App\\Services\\Tenant\\Tenant{$tenantId}ProductSearchService";

    if (class_exists($serviceClass)) {
        return app($serviceClass);
    }

    return null; // Fallback to generic
}
```

### 2. Tenant Service Oluşturma

**Yeni tenant için servis oluşturmak:**

```bash
# Dosya oluştur
cp Modules/AI/app/Services/Tenant/Tenant2ProductSearchService.php \
   Modules/AI/app/Services/Tenant/Tenant5ProductSearchService.php
```

```php
<?php

namespace Modules\AI\App\Services\Tenant;

class Tenant5ProductSearchService
{
    /**
     * TENANT 5 SPECIFIC - Keyword extraction
     */
    protected function extractKeywords(string $message): array
    {
        // Tenant 5'e özel keyword'ler
        $keywords = [];
        $productTypes = [
            'product1', 'product2', 'product3' // Tenant 5 ürünleri
        ];
        // ...
    }

    /**
     * TENANT 5 SPECIFIC - Category detection
     */
    protected function detectCategoryId(string $message): ?int
    {
        // Tenant 5 kategori mapping'i
        $categoryMap = [
            'product1' => 10,  // Tenant 5 kategori ID'leri
            'product2' => 11,
            // ...
        ];
        // ...
    }

    /**
     * WORKFLOW V2: Search method (ProductSearchNode çağırır)
     */
    public function search(string $userMessage, int $limit, ?int $categoryId): array
    {
        // Tenant 5'e özel arama
        // ...
    }
}
```

---

## 💾 Database: Directives

### Global Directives (tenant_id = 0)

Tüm tenant'lar için geçerli:

```sql
SELECT * FROM ai_tenant_directives WHERE tenant_id = 0;
```

### Tenant-Specific Directives (tenant_id > 0)

Sadece o tenant için geçerli:

```sql
SELECT * FROM ai_tenant_directives WHERE tenant_id = 2;
```

**Örnek: Tenant 2 Kritik Directive'ler**

```php
[
    'category_keywords' => json_encode([
        'forklift' => ['forklift', 'forklifts', ...],
        'transpalet' => ['transpalet', 'pallet truck', ...],
        // ...
    ]),
    'product_categories' => json_encode([
        ['id' => 1, 'name' => 'Forklift'],
        ['id' => 2, 'name' => 'Transpalet'],
        // ...
    ]),
    'contact_info' => json_encode([
        'phone' => '0216 755 3 555',
        'whatsapp' => '0501 005 67 58',
        // ...
    ]),
    'company_name' => 'İXTİF'
]
```

---

## ✅ Tenant Ekleme Checklist

Yeni tenant eklerken:

1. **Tenant Service Oluştur**
   ```bash
   Modules/AI/app/Services/Tenant/Tenant{X}ProductSearchService.php
   ```

2. **Keyword Mapping Ekle**
   - `extractKeywords()` metodunu düzenle
   - Tenant'a özel ürün keyword'lerini ekle

3. **Category Mapping Ekle**
   - `detectCategoryId()` metodunu düzenle
   - Tenant'ın kategori ID'lerini map et

4. **Directives Ekle (Database)**
   ```php
   AITenantDirective::create([
       'tenant_id' => X,
       'directive_key' => 'category_keywords',
       'directive_value' => json_encode([...]),
       // ...
   ]);
   ```

5. **Flow Oluştur (Database)**
   ```php
   Flow::create([
       'tenant_id' => X,
       'name' => 'Tenant X Shop Assistant Flow',
       'flow_data' => [...],
       // ...
   ]);
   ```

6. **Test Et**
   ```bash
   php artisan tinker --execute="
   \$service = app(\Modules\AI\App\Services\Tenant\Tenant{X}ProductSearchService::class);
   \$result = \$service->search('test query', 10);
   var_dump(\$result);
   "
   ```

---

## 🚫 YAPILMAMASI GEREKENLER

### ❌ Global Dosyalarda Tenant-Specific Kod

**YANLIŞ:**
```php
// ❌ ProductSearchNode.php içinde
protected function extractKeywords(string $message): array
{
    if (str_contains($message, 'transpalet')) {  // ❌ Tenant 2 keyword!
        return ['transpalet'];
    }
    if (str_contains($message, 'forklift')) {   // ❌ Tenant 2 keyword!
        return ['forklift'];
    }
}
```

**DOĞRU:**
```php
// ✅ Tenant2ProductSearchService.php içinde
protected function extractKeywords(string $message): array
{
    if (str_contains($message, 'transpalet')) {  // ✅ Tenant 2 service
        return ['transpalet'];
    }
    // ...
}
```

### ❌ Hard-coded Tenant ID'leri

**YANLIŞ:**
```php
if ($tenantId == 2) {  // ❌ Hard-coded!
    // Tenant 2 logic
}
```

**DOĞRU:**
```php
$searchService = $this->getTenantSearchService($tenantId);  // ✅ Dynamic!
if ($searchService) {
    // Use tenant service
}
```

---

## 📊 Tenant Listesi (Şu An)

| Tenant ID | Domain | Servis Dosyası | Kategori Sayısı |
|-----------|--------|---------------|-----------------|
| 2 | ixtif.com | Tenant2ProductSearchService.php | 6 kategori |
| 3 | ixtif.com.tr | Tenant2ProductSearchService.php (shared) | 6 kategori |

---

## 🔧 Maintenance

### Global Dosya Güncellemesi

Global node'larda değişiklik yapılırsa:

1. **Tenant-specific kod kontrolü yap**
2. **Tüm tenant'larda test et**
3. **Breaking change varsa tenant service'leri güncelle**

### Tenant Service Güncellemesi

Sadece o tenant'ı etkiler:

1. **Sadece ilgili tenant service'i güncelle**
2. **Sadece o tenant'ta test et**
3. **Diğer tenant'lar etkilenmez**

---

## 📚 İlgili Dökümanlar

- `readme/AI-FLOW-MIGRATION.md` - Flow migration guide
- `readme/AI-SHOP-CHAT.md` - Shop AI chat documentation
- `readme/tenant-olusturma.md` - Tenant creation guide

---

**Son Güncelleme:** 2025-11-09
**Versiyon:** 2.0 (Tenant-specific architecture)
