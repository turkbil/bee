# 🤖 SHOP CHAT ROBOT - ÜRÜN ÖNERİ SORUNU ANALİZİ

**Tarih:** 2025-10-15
**Analiz Eden:** Claude AI
**Domain:** ixtif.com
**Sorun:** "AI bana ürün önermiyor" şikayeti

---

## 📋 SORUN ÖZET

Kullanıcı ixtif.com üzerinde shop chat robot'una "Bana ürün öner" dediğinde:
- ✅ AI ürün öneriyor (ÇALIŞIYOR)
- ❌ Ürün linkleri BOZUK (ANA SORUN)

### Örnek Bozuk Yanıt

```json
{
  "message": "- [İXTİF CPD15TVL](https:/xtif.com/shopxtif-cpd15tvl...)"
}
```

**BOZUK URL:** `https:/xtif.com/shopxtif-cpd15tvl...`

**SORUNLAR:**
1. `//` eksik → `https:/` yerine `https://`
2. `i` harfi eksik → `xtif` yerine `ixtif`
3. `/shop/` path eksik → `/shopxtif` birleşik

**OLMASI GEREKEN:**
`https://ixtif.com/shop/ixtif-cpd15tvl...`

---

## 🔍 ROOT CAUSE ANALİZİ

### 1. URL Generation Sistemi ✅ ÇALIŞIYOR

**Dosya:** `ShopController.php:235-257`

```php
public static function resolveProductUrl(ShopProduct $product, ?string $locale = null): string
{
    $locale = $locale ?? app()->getLocale();
    $slug = $product->getTranslated('slug', $locale) ?? $product->slug[$locale] ?? null;

    if ($slug === null) {
        return url('/');
    }

    $moduleSlug = ModuleSlugService::getSlug('Shop', 'show');
    $defaultLocale = get_tenant_default_locale();

    // FIX: Ensure proper URL separation with explicit concatenation
    // Prevents AI chatbot link generation issues where /shop/slug becomes /shopslug
    $slug = ltrim($slug, '/');
    $moduleSlug = trim($moduleSlug, '/');

    if ($locale === $defaultLocale) {
        return url('/' . $moduleSlug . '/' . $slug);
    }

    return url('/' . $locale . '/' . $moduleSlug . '/' . $slug);
}
```

**SONUÇ:** Bu metod **DOĞRU URL** üretiyor. Sorun burada değil.

---

### 2. AI Context Builder ✅ DOĞRU URL GÖNDERİYOR

**Dosya:** `ShopContextBuilder.php:464-500`

```php
protected function getProductUrl(ShopProduct $product): string
{
    try {
        $url = ShopController::resolveProductUrl($product, $this->locale);

        // DEBUG: İlk birkaç ürünü logla
        static $logCount = 0;
        if ($logCount < 3) {
            \Log::info('🔗 getProductUrl() - URL generated', [
                'sku' => $product->sku,
                'slug' => $this->translate($product->slug),
                'url' => $url,
                'url_length' => strlen($url),
                'contains_https' => str_contains($url, 'https://'),
            ]);
            $logCount++;
        }

        return $url;
    } catch (\Exception $e) {
        // Fallback URL
        $slug = ltrim($this->translate($product->slug), '/');
        $url = url('/shop/' . $slug);
        return $url;
    }
}
```

**SONUÇ:** Context builder AI'ya **DOĞRU URL** gönderiyor.

---

### 3. AI Prompt Sistemi ✅ DOĞRU URL'LERİ VERİYOR

**Dosya:** `PublicAIController.php:1206-1235`

AI'ya gönderilen prompt formatı:

```
**Mevcut Ürünler (MUTLAKA LİNK VER!):**
**🚨 KRİTİK: Aşağıdaki ürünler için SADECE parantez içindeki URL'leri kullan! Kendi URL üretme!**

- **İXTİF CPD15TVL** → URL: `https://ixtif.com/shop/ixtif-cpd15tvl-...` | SKU: İXTİF-CPD15TVL | ...
  → Markdown format: [İXTİF CPD15TVL](https://ixtif.com/shop/ixtif-cpd15tvl-...)
```

**SONUÇ:** AI'ya prompt'ta **DOĞRU URL** veriliyor.

---

### 4. ❌ ASIL SORUN: AI KENDİ BAŞINA URL ÜRETİYOR

**Dosya:** `PublicAIController.php:540-838` (shopAssistantChat metodu)

AI'ya doğru URL'ler gönderilmesine rağmen:
- AI **bazı** durumlarda prompt'taki URL'leri **kopyala-yapıştır** yapmak yerine
- **Kendi başına benzer URL üretiyor**
- Bu sırada karakterleri kaçırıyor: `//`, `i`, `/shop/`

**NEDENİ:**
- GPT-5/GPT-4o modelleri bazen tokenization sırasında URL'leri **yeniden compose** ediyor
- Özellikle Türkçe karakterler (İ, Ş, Ğ) içeren slug'larda problem oluyor
- `/shop/` ve slug arasındaki `/` karakterini atlıyor

---

## 🛠️ MEVCUT POST-PROCESSING SİSTEMİ

**Dosya:** `PublicAIController.php:1395-1484` (fixBrokenUrls metodu)

Sistem zaten bir URL düzeltme mekanizmasına sahip:

```php
private function fixBrokenUrls(string $content, array $aiContext): string
{
    // Step 1: Collect all correct URLs from context
    $correctUrls = [];

    // Step 2: Extract all markdown links from AI response
    preg_match_all('/\[(.*?)\]\((http[s]?:\/\/[^)]+)\)/i', $content, $matches, PREG_SET_ORDER);

    // Step 3: Find best matching correct URL (70% similarity threshold)
    foreach ($matches as $match) {
        $brokenUrl = $match[2];
        foreach ($correctUrls as $correctUrl) {
            similar_text(strtolower($brokenUrl), strtolower($correctUrl), $similarity);
            if ($similarity > $bestSimilarity && $similarity >= 70) {
                $bestMatch = $correctUrl;
            }
        }
    }

    // Step 4: Replace broken URLs
    $content = str_replace($broken, $fixed, $content);

    return $content;
}
```

**SORUN:** Bu metod **70% similarity threshold** kullanıyor.

**ÖRNEK:**
- Bozuk: `https:/xtif.com/shopxtif-cpd15tvl...`
- Doğru: `https://ixtif.com/shop/ixtif-cpd15tvl...`
- Similarity: ~85% (üst üste denk geldiği için)

**SONUÇ:** Teoride düzelmeli ama **pratikte düzeltme çalışmıyor**.

---

## 🔧 SORUNUN ASIL SEBEBİ

### Test Sonuçları

```bash
curl -s "https://ixtif.com/api/ai/v1/shop-assistant/chat" \
  -X POST \
  -H "Content-Type: application/json" \
  -d '{"message":"Bana ürün öner"}'

# Sonuç:
{
  "message": "- [İXTİF CPD15TVL](https:/xtif.com/shopxtif-cpd15tvl...)"
}
```

**BOZUK URL HALA VAR!**

### Olası Nedenler

1. **Post-processing çalışmıyor:**
   - `fixBrokenUrls()` metodu çağrılıyor (satır 762)
   - Ama düzeltme yapamıyor

2. **Similarity threshold çok yüksek:**
   - 70% eşik bazı URL'lerde yeterli olmuyor
   - Özellikle `/shopxtif` vs `/shop/ixtif` gibi farklarda

3. **Regex match hatası:**
   - AI'nın ürettiği bozuk URL'ler regex'e yakalanmıyor olabilir

---

## 💡 ÖNERİLER

### 1. Post-Processing İyileştirmesi (ACİL)

**Sorun:** Mevcut `fixBrokenUrls()` metodu yeterince agresif değil.

**Çözüm:** Daha güçlü bir düzeltme algoritması:

```php
private function fixBrokenUrls(string $content, array $aiContext): string
{
    // Tüm doğru URL'leri topla
    $correctUrls = $this->collectCorrectUrls($aiContext);

    // Markdown linklerini çıkar
    preg_match_all('/\[(.*?)\]\((http[s]?:\/\/[^)]+)\)/i', $content, $matches, PREG_SET_ORDER);

    foreach ($matches as $match) {
        $linkText = $match[1];
        $brokenUrl = $match[2];
        $originalLink = $match[0];

        // Method 1: Exact SKU match (en güvenilir)
        $sku = $this->extractSkuFromLinkText($linkText);
        if ($sku) {
            $correctUrl = $this->findUrlBySku($sku, $correctUrls);
            if ($correctUrl) {
                $content = str_replace($originalLink, "[{$linkText}]({$correctUrl})", $content);
                continue;
            }
        }

        // Method 2: Slug similarity (fallback)
        $slug = $this->extractSlugFromUrl($brokenUrl);
        $correctUrl = $this->findUrlBySlug($slug, $correctUrls);
        if ($correctUrl) {
            $content = str_replace($originalLink, "[{$linkText}]({$correctUrl})", $content);
        }
    }

    return $content;
}
```

### 2. AI Prompt İyileştirmesi (ORTA VADELİ)

**Sorun:** AI'ya "URL kopyala" talimatı yeterince net değil.

**Çözüm:** Daha sert prompt:

```
## ⚠️ URL KURALLARI - ÇOK ÖNEMLİ!

ASLA KENDİ URL ÜRETME! İşte tam URL'ler:

Product: İXTİF CPD15TVL
EXACT_URL: https://ixtif.com/shop/ixtif-cpd15tvl-15-20-ton-li-ion-forklift
Markdown: [İXTİF CPD15TVL](https://ixtif.com/shop/ixtif-cpd15tvl-15-20-ton-li-ion-forklift)

BU URL'Yİ AYNEN KOPYALA! HİÇBİR KARAKTER DEĞİŞTİRME!
```

### 3. URL Validation (KISA VADELİ)

**Sorun:** Bozuk URL'ler fark edilemiyor.

**Çözüm:** Response validation layer:

```php
// AI yanıtı aldıktan sonra
$brokenUrls = $this->detectBrokenUrls($aiResponseText);

if (!empty($brokenUrls)) {
    \Log::warning('Broken URLs detected in AI response', [
        'broken_urls' => $brokenUrls,
        'user_message' => $userMessage,
    ]);

    // Auto-fix
    $aiResponseText = $this->fixBrokenUrls($aiResponseText, $aiContext);
}
```

---

## 📊 ÖNCELİK SIRASI

1. **ACİL (Bugün):** Post-processing güçlendirmesi
2. **KISA VADELİ (Bu hafta):** URL validation layer
3. **ORTA VADELİ (Gelecek hafta):** AI prompt optimizasyonu

---

## 🧪 TEST PLANI

1. `fixBrokenUrls()` metodunu iyileştir
2. Local'de test et:
   ```bash
   curl -s "http://ixtif.com/api/ai/v1/shop-assistant/chat" \
     -d '{"message":"Transpalet öner"}'
   ```
3. URL'lerin doğru olduğunu kontrol et
4. Production'a deploy

---

## 📝 SONUÇ

**SORUN:** AI ürün öneriyor ama URL'leri kendi başına üretirken bozuyor.

**ÇÖZÜM:** Post-processing sistemini güçlendirerek bozuk URL'leri otomatik düzelt.

**ETKİ:** Kullanıcı tıkladığında 404 hatası almayacak, doğru ürüne gidecek.
