# 📊 SHOP + AI MODÜL ANALİZ RAPORU

**Tarih:** 2025-04-12 - 19:00
**Durum:** ✅ Analiz Tamamlandı
**Sonuç:** 🎯 Mevcut yapı MÜKEMMEL - Minimal kod değişikliği gerekiyor!

---

## 🎉 İYİ HABERLER!

### ✅ Zaten Var Olan Özellikler:

1. **Session Sistemi** → `ai_conversations.session_id` var!
2. **Metadata Desteği** → `ai_conversations.metadata` (JSON) var!
3. **Varyant Sistemi** → `ShopProduct.parent_product_id`, `childProducts()` var!
4. **Prompt Sistemi** → `AIFeature.buildFinalPrompt()` metodu var!
5. **Conversation Tracking** → `ConversationService` var ve çalışıyor!
6. **JSON Çoklu Dil** → `ShopProduct` ve `ShopCategory` tam destek!
7. **Rate Limiting** → `PublicAIController` zaten var!

---

## 📦 SHOP MODÜLÜ YAPISI

### **ShopProduct Tablosu:**
```sql
product_id (PK)
category_id (FK → shop_categories)
parent_product_id (FK → shop_products) ⭐ VARYANT SİSTEMİ
is_master_product (boolean)
variant_type (string)

-- JSON Alanlar:
title (JSON) - Çoklu dil
slug (JSON) - Çoklu dil
short_description (JSON)
body (JSON)

-- ⭐ AI İÇİN SÜPER ALANLAR:
technical_specs (JSON)
features (JSON)
highlighted_features (JSON)
primary_specs (JSON)
use_cases (JSON)
competitive_advantages (JSON)
target_industries (JSON)
faq_data (JSON)
accessories (JSON)
certifications (JSON)
warranty_info (JSON)

-- Fiyat
base_price (decimal)
price_on_request (boolean)
```

### **ShopCategory Tablosu:**
```sql
category_id (PK)
parent_id (FK → self)
title (JSON) - Çoklu dil
slug (JSON) - Çoklu dil
description (JSON) - Çoklu dil
```

### **ShopProduct Model Relations:**
```php
✅ category() - BelongsTo ShopCategory
✅ parentProduct() - BelongsTo ShopProduct
✅ childProducts() - HasMany ShopProduct (varyantlar)
✅ variants() - HasMany ShopProductVariant
```

---

## 🤖 AI MODÜLÜ YAPISI

### **Conversation Tablosu:**
```sql
id (PK)
title
type (default: 'chat')
feature_name (nullable) ⭐ 'shop-assistant' eklenecek
user_id (nullable)
tenant_id
prompt_id (nullable)
session_id (nullable) ⭐ IP bazlı MD5 ekleyeceğiz
total_tokens_used
metadata (JSON) ⭐ product_id, product_sku ekleyeceğiz
status (default: 'active')
```

### **AIFeature Tablosu:**
```sql
id (PK)
name
slug ⭐ 'shop-assistant' oluşturacağız
description
quick_prompt
response_template (JSON)
custom_prompt (text)
has_custom_prompt (boolean)
has_related_prompts (boolean)
status
is_system
```

### **AIFeature Model Metodları:**
```php
✅ buildFinalPrompt($conditions, $userInput) - Hazır!
✅ prompts() - BelongsToMany Prompt (many-to-many)
✅ getFormattedTemplate() - Response format hazır!
✅ incrementUsage() - Kullanım sayacı
```

### **Mevcut Servisler:**
```
✅ ConversationService - Konuşma yönetimi
✅ ChatServiceV2 - Chat mantığı
✅ AIConversationService - AI konuşma servisi
✅ PublicAIController - Public API (zaten var!)
```

---

## 🎯 ENTEGRASYON STRATEJİSİ

### **YENİ DOSYALAR (Sadece 5 dosya!):**
```
1. app/Services/AI/Integration/ShopAIIntegration.php
   → Context builder (Product + Category data)

2. Modules/AI/resources/views/widgets/shop-product-chat-floating.blade.php
   → Floating widget

3. Modules/AI/resources/views/widgets/shop-product-chat-inline.blade.php
   → Inline widget

4. Modules/AI/resources/views/widgets/shop-category-chat-inline.blade.php
   → Kategori widget

5. Modules/AI/database/seeders/ShopAIFeatureSeeder.php
   → 'shop-assistant' feature seeder
```

### **GÜNCELLENECEK DOSYALAR (Sadece 3 dosya!):**
```
1. Modules/AI/app/Http/Controllers/Api/PublicAIController.php
   → product_id context kontrolü ekle
   → ShopAIIntegration servisi çağır
   → Rate limiting bypass (shop için)

2. Modules/Shop/resources/views/themes/blank/show.blade.php
   → Widget include ekle

3. Modules/Shop/resources/views/themes/blank/index.blade.php
   → Kategori widget include ekle
```

---

## 💡 ENTEGRASYON NOKTALARI

### **1. Session Yönetimi (IP Bazlı):**
```php
// PublicAIController veya Middleware
$sessionId = md5(
    request()->ip() .
    request()->userAgent() .
    tenant('id')
);

// Conversation metadata'ya ekle
$conversation->metadata = [
    'session_id' => $sessionId,
    'product_id' => $product->product_id,
    'product_sku' => $product->sku,
    'product_url' => $product->url,
    'category_id' => $product->category_id,
    'page_type' => 'product', // veya 'category'
];
```

### **2. Context Builder:**
```php
// ShopAIIntegration::buildContext()
public function buildContext(ShopProduct $product): array
{
    return [
        'page_type' => 'product',
        'current_product' => [
            'id' => $product->product_id,
            'title' => $product->getTranslated('title', $locale),
            'sku' => $product->sku,
            'url' => $this->getProductUrl($product),
            'price' => $product->base_price ? $product->base_price . ' TL' : null,
            'price_on_request' => $product->price_on_request,
            'technical_specs' => $product->technical_specs,
            'features' => $product->features,
            'highlighted_features' => $product->highlighted_features,
            'primary_specs' => $product->primary_specs,
            'use_cases' => $product->use_cases,
            'competitive_advantages' => $product->competitive_advantages,
            'faq_data' => $product->faq_data,
            'category' => [
                'id' => $product->category_id,
                'name' => $product->category->getTranslated('title', $locale),
                'url' => $this->getCategoryUrl($product->category),
            ]
        ],
        'variants' => $product->childProducts()->get()->map(function($variant) {
            return [
                'id' => $variant->product_id,
                'title' => $variant->getTranslated('title', $locale),
                'sku' => $variant->sku,
                'url' => $this->getProductUrl($variant),
                'variant_type' => $variant->variant_type,
                'key_differences' => $this->extractKeyDifferences($variant),
                'price' => $variant->base_price ? $variant->base_price . ' TL' : null,
            ];
        })->toArray(),
    ];
}
```

### **3. Prompt Oluşturma:**
```php
// AIFeature slug: 'shop-assistant'
$feature = AIFeature::where('slug', 'shop-assistant')->first();

// Prompt builder (mevcut metod kullanılacak)
$prompt = $feature->buildFinalPrompt([
    'product_context' => $context,
    'language' => $locale,
], [
    'content' => $userMessage,
]);
```

### **4. PublicAIController Güncellemesi:**
```php
public function publicChat(Request $request)
{
    // ... mevcut kod ...

    // SHOP CONTEXT KONTROLÜ
    $context = $request->input('context', []);

    if (isset($context['product_id'])) {
        // Product context builder
        $product = ShopProduct::find($context['product_id']);
        if ($product) {
            $shopIntegration = app(ShopAIIntegration::class);
            $productContext = $shopIntegration->buildContext($product);

            // Context'i zenginleştir
            $context = array_merge($context, $productContext);

            // Feature slug override
            $featureSlug = 'shop-assistant';

            // ⭐ SHOP İÇİN ÖZEL: Rate limit ve credit bypass
            $skipRateLimit = true;
            $skipCreditCheck = true;
        }
    }

    // Session ID (IP bazlı)
    $sessionId = md5(
        $request->ip() .
        $request->userAgent() .
        tenant('id')
    );

    // Conversation oluştur/bul
    $conversation = Conversation::firstOrCreate([
        'session_id' => $sessionId,
        'feature_name' => 'shop-assistant',
        'status' => 'active',
    ], [
        'title' => 'Shop AI Assistant',
        'type' => 'shop_chat',
        'user_id' => auth()->id(),
        'tenant_id' => tenant('id'),
        'metadata' => $context,
    ]);

    // ... mevcut conversation logic ...
}
```

---

## 📋 AVANTAJLAR

### ✅ **Mevcut Yapıyı Kullanma:**
1. **Conversation tablosu** → Session tracking için hazır
2. **metadata (JSON)** → Product context için hazır
3. **AIFeature sistem** → Prompt management için hazır
4. **PublicAIController** → API endpoint hazır
5. **ConversationService** → Chat logic hazır
6. **Rate limiting** → Zaten var (bypass ekleyeceğiz)

### ✅ **Minimal Kod Değişikliği:**
- Sadece 5 yeni dosya
- Sadece 3 dosya güncelleme
- Mevcut servisler kullanılacak
- Mevcut modeller kullanılacak

### ✅ **Güçlü Veri Yapısı:**
- ShopProduct'ta 10+ JSON alan var
- Varyant sistemi zaten hazır
- Kategori ilişkileri hazır
- Çoklu dil desteği tam

---

## 🚀 SONRAKİ ADIMLAR

### **Kodlama Sırası:**
```
1. ShopAIIntegration servisi (Context builder)
2. ShopAIFeatureSeeder (AIFeature oluştur)
3. PublicAIController güncellemesi
4. Widget blade dosyaları (3 adet)
5. Shop sayfalarına widget entegrasyonu
6. Test
```

### **Tahmini Süre:**
- Backend: ~2 saat
- Frontend: ~1 saat
- Test: ~30 dk
- **TOPLAM: ~3.5 saat**

---

## 🎯 KRİTİK KARARLAR

### **✅ ONAYLANAN:**
1. Mevcut `ai_conversations` tablosu kullanılacak
2. `metadata` JSON alanına product_id eklenecek
3. `session_id` alanına IP bazlı hash eklenecek
4. Mevcut `PublicAIController` güncellenecek
5. AIFeature slug: `shop-assistant` oluşturulacak
6. Rate limiting BYPASS (shop için)
7. Credit sistemi BYPASS (shop için)

### **✅ TASARIM KARARLARI:**
1. **2 Widget Tipi:** Floating + Inline
2. **Kategori Desteği:** Evet (3. widget)
3. **Link Paylaşımı:** Evet (tıklanabilir)
4. **Varyant Karşılaştırma:** Evet
5. **Fiyat Kontrolü:** Evet (base_price varsa göster)
6. **Session Kalıcılığı:** Evet (IP bazlı)
7. **Temizle Butonu:** Frontend clear, backend keep

---

## 📊 VERİ AKIŞI

```
┌─────────────────────────────────────────────────────┐
│ 1. KULLANICI → Widget'a tıklar                     │
└───────────────────────┬─────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│ 2. FRONTEND → API'ye istek gönderir                 │
│    POST /api/ai/v1/chat                             │
│    {                                                │
│      message: "Bu ürünün fiyatı nedir?",           │
│      context: {                                     │
│        product_id: 123,                             │
│        product_sku: "SKU123",                       │
│        widget_version: "3.0",                       │
│        mode: "floating"                             │
│      }                                              │
│    }                                                │
└───────────────────────┬─────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│ 3. BACKEND → PublicAIController.publicChat()       │
│    - IP bazlı session_id oluştur                   │
│    - Product_id kontrol et                          │
│    - ShopAIIntegration::buildContext() çağır       │
│    - Context'i zenginleştir                         │
│    - Rate limit BYPASS (shop için)                 │
└───────────────────────┬─────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│ 4. ShopAIIntegration → Context builder              │
│    - Product datasını al (JSON alanlar)            │
│    - Varyantları al (childProducts())              │
│    - Kategori bilgisini al                          │
│    - URL'leri oluştur                               │
│    - Context JSON hazırla                           │
└───────────────────────┬─────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│ 5. ConversationService → Conversation yönetimi     │
│    - session_id ile conversation bul/oluştur       │
│    - metadata'ya product context ekle              │
│    - Message ekle (user + assistant)               │
│    - Database'e kaydet                              │
└───────────────────────┬─────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│ 6. AIFeature → Prompt builder                      │
│    - 'shop-assistant' feature bul                  │
│    - buildFinalPrompt() çağır                      │
│    - Product context'i prompt'a ekle               │
│    - AI provider'a gönder                           │
└───────────────────────┬─────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│ 7. AI PROVIDER → Yanıt üret                        │
│    - Prompt'u işle                                  │
│    - Product bilgilerini kullan                    │
│    - Satış odaklı yanıt oluştur                    │
│    - Link'ler ekle                                  │
└───────────────────────┬─────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│ 8. BACKEND → Yanıtı kaydet                         │
│    - Message olarak kaydet (role: assistant)       │
│    - Token kullanımını kaydet (bypass credit)     │
│    - Conversation güncelle                          │
└───────────────────────┬─────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│ 9. FRONTEND → Yanıtı göster                        │
│    - Widget'ta mesajı render et                    │
│    - Link'leri tıklanabilir yap                    │
│    - localStorage'a kaydet (session kalıcılığı)    │
└─────────────────────────────────────────────────────┘
```

---

## 🎉 SONUÇ

**MEVCUT YAPI MÜ KEMMEL!**

Minimal kod değişikliği ile güçlü bir Shop AI sistemi kurabiliriz:
- ✅ 5 yeni dosya
- ✅ 3 dosya güncelleme
- ✅ Mevcut servisler kullanılacak
- ✅ ~3.5 saat geliştirme süresi

**Hazırız! "BAŞLA" dediğinde kodlamaya geçelim!** 🚀
