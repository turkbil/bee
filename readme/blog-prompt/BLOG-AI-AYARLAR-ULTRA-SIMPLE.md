# 🤖 BLOG AI AYARLARI - ULTRA SADELİK

**Tarih**: 2025-11-14
**Revizyon**: v3 - Ultra Basitleştirme
**Lokasyon**: `/admin/settingmanagement/values/18`

---

## ⚡ SADECE 6 AYAR - GERÇEKTEN GEREKLİ OLANLAR

### ❌ KALDIRILAN GEREKSIZLER (Artık Otomatik):

1. ~~blog_ai_topic_expand_enabled~~ → Her zaman açık
2. ~~blog_ai_topic_expand_count~~ → Limit yok! Dar sektörlerde sorun çıkar
3. ~~blog_ai_duplicate_check~~ → Her zaman açık
4. ~~blog_ai_auto_source_products~~ → Otomatik açık
5. ~~blog_ai_auto_source_categories~~ → Otomatik açık
6. ~~blog_ai_auto_priority~~ → Otomatik belirle
7. ~~blog_ai_style_rotation~~ → Her zaman açık
8. ~~blog_ai_queue_enabled~~ → Sistem direkt ekler

**Sebep:** Müşteri bunları bilmez, zaten hep açık olmalı!

---

## 📊 FİNAL AYAR LİSTESİ (6 AYAR)

### 1️⃣ TEMEL KONTROL (3 Ayar)

#### `blog_ai_enabled` (checkbox)
- **Label**: Blog AI Sistemi Aktif
- **Default**: `0` (Kapalı)
- **Açıklama**: Sistemi aç/kapat

#### `blog_ai_daily_count` (number)
- **Label**: Günlük Blog Sayısı
- **Default**: `10`
- **Min**: 1, **Max**: 100
- **Açıklama**: Her gün kaç blog yazılsın?

#### `blog_ai_auto_publish` (checkbox)
- **Label**: Otomatik Yayınlama
- **Default**: `1` (Açık)
- **Açıklama**: Blog yazılınca otomatik yayınlansın mı?

---

### 2️⃣ KONU KAYNAKLARI (2 Ayar)

#### `blog_ai_topic_source` (select)
- **Label**: Konu Kaynağı
- **Choices**:
  - `manual`: Manuel (Sadece aşağıdaki listeden)
  - `auto`: Otomatik (Ürün/Kategori analizi)
  - `mixed`: Karma (Önce manuel, sonra otomatik)
- **Default**: `mixed`

#### `blog_ai_manual_topics` (textarea)
- **Label**: Ana Konular (Manuel Liste)
- **Rows**: 15
- **Placeholder**:
```
transpalet
forklift
akülü istif makinesi
```
- **Default**: `null` (Boş)
- **Açıklama**: Her satıra bir ana konu. Sistem sınırsız genişletir.

---

### 3️⃣ YAZIM STİLİ (1 Ayar)

#### `blog_ai_professional_only` (checkbox)
- **Label**: Sadece Profesyonel/Uzman Stil (Samimi Yok)
- **Default**: `0` (Kapalı = Tüm stiller kullanılır)
- **Açıklama**:
  - **Kapalı (0)**: Profesyonel → Samimi → Uzman (Çeşitli)
  - **Açık (1)**: Sadece Profesyonel + Uzman (Samimi yok)

---

## 🔧 PROMPT İÇİNDE OTOMATİK OLANLAR

**Bu ayarlar müşteriye sorulmaz, kodda sabit:**

### Konu Genişletme
- **Genişletme**: Her zaman aktif
- **Başlık limiti**: YOK! Sistem otomatik karar verir
  - 1 ürünlü sektör → Az başlık üret
  - 1000 ürünlü sektör → Çok başlık üret
- **Duplicate kontrol**: Her zaman aktif

### Otomatik Konu Bulma
- **Ürünlerden bul**: Her zaman aktif
- **Kategorilerden bul**: Her zaman aktif
- **Önceliklendirme**: Otomatik belirle
  - En çok görüntülenen
  - Blogu olmayan
  - En yeni

### Stil Rotasyonu
- **Rotasyon**: Her zaman aktif
- **Sıralama**: `blog_ai_professional_only` ayarına göre
  - OFF → Profesyonel → Samimi → Uzman
  - ON → Profesyonel → Uzman (Samimi atla)

### SEO & İçerik
- **Kelime sayısı**: 2000-2500 kelime (otomatik)
- **Dil**: Tenant'ın dili (auto-detect)
- **SEO 2025**: Her zaman aktif

### AI Provider
- **Provider**: Sistem AI
- **Temperature**: 0.7
- **Retry**: 3

### Zamanlama
- **Cron**: Her 2 saatte bir
- **Dağılım**: Günlük sayıya göre otomatik

### Sistem
- **Queue**: Her zaman aktif

---

## 🎨 LAYOUT JSON (Ultra Basit)

```json
{
  "elements": [
    {
      "type": "section",
      "title": "Temel Kontrol",
      "subtitle": "Sistemi aç/kapat ve günlük sayıyı belirle",
      "width": 12,
      "elements": [
        {"type": "field", "setting_key": "blog_ai_enabled", "width": 4},
        {"type": "field", "setting_key": "blog_ai_daily_count", "width": 4},
        {"type": "field", "setting_key": "blog_ai_auto_publish", "width": 4}
      ]
    },
    {
      "type": "section",
      "title": "Konu Kaynakları",
      "subtitle": "Blog konularını nereden alacak?",
      "width": 12,
      "elements": [
        {"type": "field", "setting_key": "blog_ai_topic_source", "width": 12},
        {"type": "field", "setting_key": "blog_ai_manual_topics", "width": 12},
        {"type": "alert", "variant": "info", "content": "💡 Her satıra bir ana konu yaz. Sistem SINIRSIZ genişletir (dar sektör/geniş sektör otomatik adapt olur).", "width": 12}
      ]
    },
    {
      "type": "section",
      "title": "Yazım Stili",
      "subtitle": "Blog yazma stili ayarı",
      "width": 12,
      "elements": [
        {"type": "field", "setting_key": "blog_ai_professional_only", "width": 12},
        {"type": "alert", "variant": "warning", "content": "⚠️ Bu ayar KAPALI ise: Profesyonel → Samimi → Uzman (çeşitli)<br>Bu ayar AÇIK ise: Sadece Profesyonel + Uzman (samimi yok)", "width": 12}
      ]
    }
  ]
}
```

---

## 📋 KARŞILAŞTIRMA

| v1 (İlk) | v2 (Temizlik) | v3 (Ultra) | Neden? |
|----------|---------------|------------|--------|
| 22 ayar | 14 ayar | **6 ayar** | Müşteri sadece bunları anlayabilir |
| 9 kategori | 6 kategori | **3 kategori** | Daha basit |
| Limit var | Limit var | **Limit yok** | Dar sektörlerde sorun çıkar |
| Genişletme sor | Genişletme sor | **Otomatik** | Her zaman açık olmalı |
| Duplicate sor | Duplicate sor | **Otomatik** | Her zaman açık olmalı |
| Stil karmaşık | Stil 4 seçenek | **Basit ON/OFF** | Samimi istiyor mu? |

---

## ✅ SONUÇ

**SADECE 6 AYAR:**

1. ✅ Sistemi aç/kapat
2. ✅ Günlük blog sayısı
3. ✅ Otomatik yayın
4. ✅ Konu kaynağı (manuel/oto/karma)
5. ✅ Manuel konu listesi (textarea)
6. ✅ Sadece profesyonel/uzman (checkbox)

**Geri kalan 16+ ayar:** Prompt'ta otomatik!

---

## 💡 AKILLI KONU GENİŞLETME

**Limit yok! Sistem akıllı:**

```php
// Pseudo-code
$productCount = Product::count();
$categoryCount = Category::count();

if ($productCount < 10) {
    // Dar sektör: Her konudan 20-30 başlık üret
    $expandLimit = 30;
} elseif ($productCount < 100) {
    // Orta sektör: Her konudan 50-100 başlık üret
    $expandLimit = 100;
} else {
    // Geniş sektör: Her konudan 200+ başlık üret
    $expandLimit = 200;
}

// Sistem otomatik adapt olur!
```

**Örnek:**
- **1 ürünlü site**: "transpalet" → 30 başlık üretir
- **1000 ürünlü site**: "transpalet" → 200 başlık üretir

**Müşteri hiçbir şey yapmaz, sistem halleder! 🎯**

---

## 📂 OTOMATİK KATEGORİ SEÇİMİ

**Sistem blog kategorisini OTOMATIK belirler!**

### 🎯 Kategori Yapısı

**Ana Kategoriler (Genel - 6 adet):**
1. **Kullanım Kılavuzları** - Nasıl kullanılır, ayarlar, kurulum
2. **Karşılaştırma ve Seçim** - Model karşılaştırmaları, seçim kriterleri
3. **Güvenlik ve Mevzuat** - İş güvenliği, sertifikalar, yasal düzenlemeler
4. **Sektör ve Teknoloji** - Yenilikler, trendler, gelişmeler
5. **İpuçları ve Püf Noktaları** - Verimlilik, pratik bilgiler
6. **Bakım ve Onarım** - Bakım rehberleri, arıza giderme

**Ürün Kategorisi Bazlı (7 adet):**
7. **Forklift İncelemeleri** - Forklift modelleri ve özellikleri
8. **Transpalet İncelemeleri** - Manuel/elektrikli transpalet modelleri
9. **İstif Makinesi İncelemeleri** - İstif makinesi çeşitleri
10. **Order Picker İncelemeleri** - Sipariş toplama makineleri
11. **Otonom Sistemler** - AGV, AMR, otonom depo sistemleri
12. **Reach Truck İncelemeleri** - Dar koridor forkliftleri
13. **Yedek Parça Rehberi** - Doğru yedek parça seçimi

**Toplam: 13 kategori**

### 🤖 AI Kategori Seçim Mantığı

```php
// Pseudo-code
function determineCategory($topic, $content) {
    // 1️⃣ Ürün kategorisi tespit et
    $productCategories = [
        'forklift' => 'Forklift İncelemeleri',
        'transpalet' => 'Transpalet İncelemeleri',
        'istif' => 'İstif Makinesi İncelemeleri',
        'order picker' => 'Order Picker İncelemeleri',
        'agv|amr|otonom' => 'Otonom Sistemler',
        'reach truck' => 'Reach Truck İncelemeleri',
        'yedek parça' => 'Yedek Parça Rehberi'
    ];

    foreach ($productCategories as $keyword => $category) {
        if (str_contains_any($topic, $keyword)) {
            return $category; // Ürün kategorisi öncelikli!
        }
    }

    // 2️⃣ İçerik analizi ile genel kategori belirle
    if (contains_keywords($content, ['nasıl', 'kullanım', 'adım', 'kurulum'])) {
        return 'Kullanım Kılavuzları';
    }

    if (contains_keywords($content, ['karşılaştırma', 'hangisi', 'seçim', 'vs', 'fark'])) {
        return 'Karşılaştırma ve Seçim';
    }

    if (contains_keywords($content, ['güvenlik', 'sertifika', 'mevzuat', 'yasa', 'iş güvenliği'])) {
        return 'Güvenlik ve Mevzuat';
    }

    if (contains_keywords($content, ['teknoloji', 'yenilik', 'trend', '2025', 'gelişme'])) {
        return 'Sektör ve Teknoloji';
    }

    if (contains_keywords($content, ['ipucu', 'püf nokta', 'trick', 'verimli', 'pratik'])) {
        return 'İpuçları ve Püf Noktaları';
    }

    if (contains_keywords($content, ['bakım', 'onarım', 'arıza', 'temizlik', 'servis'])) {
        return 'Bakım ve Onarım';
    }

    // 3️⃣ Default: En popüler genel kategori
    return 'Karşılaştırma ve Seçim';
}
```

### 📋 Kategori Seçim Örnekleri

| Konu | AI Çıkarımı | Kategori |
|------|-------------|----------|
| "transpalet nedir" | Ürün: transpalet | **Transpalet İncelemeleri** |
| "forklift nasıl kullanılır" | Ürün: forklift + nasıl | **Forklift İncelemeleri** |
| "forklift bakımı nasıl yapılır" | Ürün: forklift + bakım | **Bakım ve Onarım** |
| "elektrikli transpalet vs manuel" | Ürün: transpalet + karşılaştırma | **Transpalet İncelemeleri** |
| "forklift operatör sertifikası" | Ürün: forklift + sertifika | **Güvenlik ve Mevzuat** |
| "depo otomasyonu 2025" | Otomasyon + trend | **Otonom Sistemler** |
| "istif makinesi bakımı" | Ürün: istif + bakım | **Bakım ve Onarım** |
| "en iyi reach truck markaları" | Ürün: reach truck | **Reach Truck İncelemeleri** |

### 🎯 Önceliklendirme Kuralları

1. **Ürün kategorisi öncelikli!** - Eğer konu bir ürün içeriyorsa, önce o ürün kategorisine git
2. **İçerik analizi ikincil** - Ürün yoksa, içerik anahtar kelimelerinden kategori belirle
3. **Multi-kategori durumu** - Blog birden fazla kategoriye ait olabilir (primary + secondary)
4. **Featured kategoriler boost** - Featured kategoriler daha sık kullanılır

### 🔄 Dinamik Kategori Yönetimi

**Sistem otomatik:**
- ✅ Shop kategorilerini takip eder
- ✅ Yeni ürün kategorisi eklenirse, otomatik blog kategorisi oluşturulabilir
- ✅ Her tenant için farklı kategori seti olabilir
- ✅ Kategori bazlı blog dağılımı dengelenir

**Örnek:**
- Transpalet kategorisinde 50 blog var, Forklift'te 10 var
- Sistem önceliği Forklift'e verir → Denge sağlar

---

## 🚀 WORKFLOW ÖZETİ

```
1️⃣ Cron çalışır (her 2 saatte bir)
   ↓
2️⃣ Ayarları kontrol et (blog_ai_enabled = 1?)
   ↓
3️⃣ Konu kaynağını belirle (manuel/auto/mixed)
   ↓
4️⃣ Konuları topla ve sınırsız genişlet
   ↓
5️⃣ Her konu için:
   - Duplicate check (aynı başlık var mı?)
   - Kategori belirle (OTOMATIK - yukarıdaki mantık)
   - Blog içeriği üret (AI)
   - SEO optimize et
   - Stil uygula (professional_only ayarına göre)
   ↓
6️⃣ Queue'ya ekle (async)
   ↓
7️⃣ Blog yayınla (blog_ai_auto_publish = 1 ise)
   ↓
8️⃣ Günlük limit kontrol et (blog_ai_daily_count)
```

**Müşteri sadece 6 ayarı yapar, geri kalan herşey otomatik! 🎉**

---

## 🎛️ MANUEL ÜRETIM SİSTEMİ

**Admin panelde "Blog Oluştur" butonu!**

### 🚀 Nasıl Çalışır?

**Buton Konumu:**
- `/admin/blog` sayfasının sağ üst köşesi
- "Blog Oluştur" butonu yanında **"AI ile Oluştur"** butonu

**Modal Açılır:**
```
┌──────────────────────────────────────┐
│  🤖 AI ile Blog Oluştur              │
├──────────────────────────────────────┤
│                                      │
│  Ana Konu (Opsiyonel):               │
│  ┌────────────────────────────────┐ │
│  │ transpalet                     │ │
│  └────────────────────────────────┘ │
│                                      │
│  ℹ️ Boş bırakırsanız otomatik      │
│     ürün/kategori analizi yapar     │
│                                      │
│  [ İptal ]  [ Blog Oluştur ]        │
└──────────────────────────────────────┘
```

**Senaryolar:**

**1️⃣ Konu Yazıldı:**
```
Input: "transpalet"
→ Konu genişlet (30-200 başlık)
→ İlk başlığı seç
→ Kategori belirle (Transpalet İncelemeleri)
→ Blog oluştur
→ Kredi düş
```

**2️⃣ Konu Boş:**
```
Input: "" (boş)
→ En az blogu olan ürünü/kategoriyi bul
→ Otomatik konu belirle
→ Blog oluştur
→ Kredi düş
```

### 📋 İşlem Akışı

```php
// Pseudo-code
function manualGenerate($topic = null) {
    // 1. Kredi kontrolü
    if (!hasSufficientCredit()) {
        throw new InsufficientCreditException('Yeterli kredi yok!');
    }

    // 2. Konu belirle
    if (empty($topic)) {
        $topic = findLeastCoveredProduct(); // En az blogu olan
    }

    // 3. Kategori belirle
    $category = determineCategoryAI($topic);

    if (!$category) {
        $category = BlogCategory::where('slug', 'genel')->first(); // Fallback
    }

    // 4. Blog oluştur
    $blog = generateBlogContent($topic, $category);

    // 5. Kredi düş
    deductCredit(1); // Her blog için 1 kredi

    // 6. Kaydet
    $blog->save();

    return $blog;
}
```

---

## 💳 KREDİ SİSTEMİ - MEVCUT ALTYAPI

**Sistem zaten hazır! Yeni migration gerekmiyor!**

### ✅ Mevcut Altyapı

**Tenant Model:**
- ✅ `tenants.ai_credits_balance` kolonu (float) - MEVCUT
- ✅ `hasEnoughCredits()` method - MEVCUT
- ✅ `useCredits()` method - MEVCUT

**AI Modülü:**
- ✅ `ai_use_credits($amount, $tenantId, $metadata)` - Helper fonksiyonu
- ✅ `ai_can_use_credits($amount, $tenantId)` - Kontrol fonksiyonu
- ✅ `ai_get_credit_balance($tenantId)` - Bakiye sorgulama
- ✅ `Modules\AI\App\Models\AICreditUsage` - Kullanım log'u

### 💰 Blog Kredi Maliyeti

**NET MALİYET: 1 blog = 1 kredi**

```php
// Her blog üretimi (AŞAMA 2)
$creditCost = 1.0; // Net ve basit hesaplama!

// Taslak üretimi (AŞAMA 1)
$draftCost = 0.01; // 100 taslak = 1.0 kredi

// Fotoğraf ekleme (gelecekte)
$photoCost = 0.10; // Ayrı hesaplanacak

// TOPLAM ÖRNEK: 100 taslak + 10 blog + 10 fotoğraf = 1.0 + 10.0 + 1.0 = 12.0 kredi
```

**Sebep:** Basit ve net hesaplama - 1 blog = 1 kredi!

### 📊 Kredi Kontrolü ve Kullanımı

**Kod Örneği:**
```php
use function ai_can_use_credits;
use function ai_use_credits;

// 1. Kredi kontrolü (1 blog = 1 kredi)
if (!ai_can_use_credits(1.0)) {
    throw new InsufficientCreditException('Kredi yetersiz!');
}

// 2. Blog oluştur
$blog = $this->generateBlogContent($topic, $category);

// 3. Kredi düş
ai_use_credits(1.0, null, [
    'usage_type' => 'blog_generation',
    'operation_type' => 'content_generation',
    'word_count' => str_word_count($blog->content),
    'reference_id' => $blog->id
]);

// 4. Blog kaydet
$blog->save();
```

**Otomatik Log:**
- `ai_credit_usage` tablosuna otomatik kayıt
- Tenant ID, kredi miktarı, işlem türü, metadata
- Bakiye güncelleme otomatik

### 🔔 Kredi Uyarıları (AI Modülünde Mevcut)

**Bakiye Kontrolü:**
```php
$balance = ai_get_credit_balance();

if ($balance < 10) {
    // Sarı uyarı
}

if ($balance < 5) {
    // Kırmızı uyarı
}

if ($balance <= 0) {
    // Blog üretimi durdur
    throw new InsufficientCreditException();
}
```

**Admin Panelde:**
- AI modülünde mevcut kredi widget kullanılabilir
- Tenant'ın `ai_credits_balance` kolonunu göster
- "Kredi Satın Al" butonu zaten var

---

## 📝 2 AŞAMALI PROMPT SİSTEMİ

**Blog üretek sistemi 2 aşamalı çalışır!**

### 🔵 AŞAMA 1: BLOG TASLAĞI OLUŞTUR

**Amaç:** İçerik yapısını planla, SEO stratejisini belirle

**AI Prompt Detayları:**
```markdown
Rol: 25 yıllık deneyimli SEO uzmanı
Hedef: Endüstriyel ürün satışı için blog taslağı

İstenenler:
1. SEO Meta Bilgileri
   - Title (50-60 karakter, anahtar kelime başta)
   - Meta description (155-160 karakter, CTA ile)
   - URL slug
   - Focus keyword + Secondary keywords

2. Schema.org Planı
   - Article Schema (zorunlu)
   - FAQPage Schema (5-10 soru)
   - BreadcrumbList Schema
   - Product Schema (ürün içeriklerinde)
   - HowTo Schema (rehber içeriklerinde)

3. İçerik Yapısı (2000-2500 kelime)
   - H1 başlık
   - 4-6 H2 başlık
   - Her bölüm için H3 alt başlıklar
   - Her bölüm için kelime sayısı + içerik notu

4. Her Bölüm İçin
   - Hangi anahtar kelimeler kullanılacak
   - Görsel/tablo/liste gereksinimi
   - Dahili link fırsatı
   - Dış kaynak önerisi

5. FAQ Soruları (10 adet long-tail)
   - "X nedir?" formatında
   - "Nasıl...?" formatında
   - "Hangisi daha iyi?" formatında

6. Dahili Bağlantı Stratejisi
   - 5-10 dahili link önerisi
   - Semantic anchor text
   - Hedef sayfa URL

7. Görsel & Medya Planı
   - Öne çıkan görsel + alt text
   - İnfografik önerileri
   - Karşılaştırma tabloları
```

**Çıktı Örneği:**
```markdown
## SEO Meta
Title: Transpalet Nedir? Çeşitleri ve Fiyatları [2025]
Meta: Transpalet nedir, nasıl çalışır? ✓ Manuel ve elektrikli...
URL: /transpalet-nedir
Focus: transpalet nedir
Secondary: manuel transpalet, elektrikli transpalet, fiyatları

## Schema Planı
- Article ✓
- FAQPage ✓ (10 soru)
- BreadcrumbList ✓

## İçerik Yapısı
H1: Transpalet Nedir? Çeşitleri ve Özellikleri [2025]

H2: Transpalet Nedir? (250 kelime)
  - Tanım paragrafı
  - Çalışma prensibi
  - Keywords: "transpalet nedir", "palet taşıma"
  - Görsel: Transpalet anatomisi
  - Dahili link: → /depo-ekipmanlari

H2: Transpalet Çeşitleri (500 kelime)
  H3: Manuel Transpalet (200 kelime)
  H3: Elektrikli Transpalet (200 kelime)
  H3: Özel Modeller (100 kelime)
  - Karşılaştırma tablosu ekle
  - Keywords: "manuel transpalet", "elektrikli transpalet"

[devam...]

## FAQ (10 Soru)
S1: Transpalet ne kadar yük kaldırır?
S2: Manuel mi elektrikli transpalet mi daha iyi?
[devam...]
```

---

### 🟢 AŞAMA 2: BLOG İÇERİĞİNİ YAZ

**Amaç:** Taslağa göre bölüm bölüm blog içeriğini oluştur

**AI Prompt Detayları:**
```markdown
Rol: 25 yıllık AI-SEO editörü
Hedef: Endüstriyel ürün satışı için teknik blog yazımı

Hedef Kitle:
- B2B kullanıcılar (25-65 yaş)
- Depo yöneticileri, satın alma müdürleri
- Lojistik sorumlular, teknik ekipler

Ton & Stil:
- Profesyonel, teknik, güvenilir
- Marka adı kullanma (context gerektirmedikçe)
- Sade, kesin, gereksiz sözcük yok
- Cümle ≤ 20 kelime
- Paragraf ≤ 150 kelime

SEO Kuralları:
- Keyword density: %1-2
- LSI terimleri kullan
- Semantic SEO (entity'ler: markalar, standartlar, kategoriler)
- TF-IDF analizi uygula
- Featured snippet optimizasyonu

İçerik Yapısı:
- H2/H3 başlıklar (anahtar kelime optimizasyonlu)
- Madde listesi veya tablo (gerekirse)
- Her bölüm sonunda 1-2 otoriter kaynak
  - Resmi endüstri standartları (ISO, CE, TSE)
  - Üretici teknik dökümanları
  - Sektör otoriteleri
  - Akademik/teknik yayınlar

Dahili Bağlantı:
- Metne doğal yerleştir
- Semantik anchor text kullan
- 5-10 dahili link

Schema Uyumluluk:
- FAQPage: Her soru-cevap schema uyumlu (50-100 kelime)
- HowTo: Numaralı adımlar
- Product: Teknik özellikler tablo formatında

Görsel:
- Her bölüm için görsel önerisi
- Alt text formatı: "[Anahtar kelime] + [açıklayıcı kelime]"
```

**Çıktı Örneği:**
```markdown
## Transpalet Nedir?

Endüstriyel malzeme taşımada kullanılan transpalet, paletin yerden kalkmasını sağlayan hidrolik ekipmandır. Depo ve üretim tesislerinde yük taşıma işlemlerini kolaylaştırır. Modern lojistik operasyonlarının vazgeçilmez parçasıdır.

Transpalet, hidrolik pompa sistemi ile çalışır. Operatör kolu çekerek pompayı aktive eder. Pompa, çatallardaki hidrolik silindiri harekete geçirir. Silindir paletin altına girer ve yükü 85-200 mm yüksekliğe kaldırır.

[Kaynak: ISO 3691-1 Standardı](URL)
[Daha fazla bilgi için: Depo ekipman rehberi](/depo-ekipmanlari)

## Transpalet Çeşitleri

### Manuel Transpalet

Manuel transpaletler, hidrolik pompayla çalışan ekonomik çözümlerdir. Günlük kullanım sıklığı düşük işletmeler için idealdir. İlk yatırım maliyeti düşüktür.

**Teknik Özellikler:**
- Kapasite: 2.000-3.000 kg
- Çatal uzunluğu: 800-2.000 mm
- Kaldırma yüksekliği: 85-200 mm

| Avantajlar | Dezavantajlar |
|------------|---------------|
| Düşük maliyet | Yorucu |
| Bakım minimal | Yavaş |
| Elektrik gerektirmez | Eğimde zor |

[Kaynak: Transpalet Teknik Döküman](URL)

[devam...]
```

---

### 📊 İKİ YÖNTEM

**Yöntem A: Tek Seferde Tüm Blog (Hızlı)**
- Tüm taslağı AI'ya ver
- 2000-2500 kelimelik blog al
- Süre: ~30-45 dakika

**Yöntem B: Bölüm Bölüm Yaz (Detaylı)**
- Her H2 bölümünü ayrı yazdır
- Sonra birleştir
- Daha kontrollü, daha kaliteli
- Süre: ~1-2 saat

---

### 🎯 BLOG YAZDIRMA KURALLARI

**SEO & Anahtar Kelime:**
- İlk 100 kelimede ana anahtar kelime
- Her H2'de en az 1 LSI kelime
- Uzun kuyruklu KWs başlıklara dağıt
- Keyword stuffing yapma

**İçerik Yapısı:**
- Cümle ≤ 20 kelime (okunabilirlik)
- Paragraf ≤ 150 kelime (mobil uyumluluk)
- Transition words kullan (ancak, dolayısıyla, örneğin)
- Pasif cümle minimize et

**Kaynaklar:**
- Her iddia için kanıt/referans (E-A-T)
- Inline format: `[Kaynak adı](URL)`
- ISO standartları, teknik dökümanlar, sektör otoriteleri

**Schema:**
- FAQ: Her soru-cevap schema uyumlu
- HowTo: Numaralı adımlar
- Product: Teknik özellikler tablo formatında

**Featured Snippet:**
- Tanım paragrafı: İlk 50-60 kelimede net tanım
- Liste formatı: Madde işaretli veya numaralı
- Tablo formatı: Karşılaştırma, fiyat aralıkları
- Soru formatı: "X Nasıl Çalışır?" gibi

---

## 🔧 SİSTEM MİMARİSİ NOTLARI

### 🚨 TENANT SİSTEMİ - ÇOK KRİTİK!

**Multi-Tenant Mimari:**
- ⚠️ Bu bir **multi-tenant sistem**
- ⚠️ Her tenant'ın **AYRI DATABASE'i** var (tenant-specific)
- ⚠️ **AI kredi** ve merkezi veriler **CENTRAL database'de** (central)
- ⚠️ `blog_ai_drafts` tablosu **TENANT database'inde** olmalı
- ⚠️ Migration: **İKİ YER** oluştur:
  - `database/migrations/YYYY_MM_DD_create_blog_ai_drafts_table.php` (central)
  - `database/migrations/tenant/YYYY_MM_DD_create_blog_ai_drafts_table.php` (tenant)

**Database Dağılımı:**
```
CENTRAL DATABASE:
├── tenants (tenant listesi)
├── ai_credits_balance (kredi bakiyeleri)
└── ai_credit_usage (kredi kullanım logları)

TENANT DATABASE (Her tenant için ayrı):
├── blog_ai_drafts (AI taslakları)
├── blogs (blog içerikleri)
├── blog_categories (kategoriler)
├── seo_settings (SEO bilgileri - polymorphic)
└── media (medya dosyaları - Spatie)
```

### ⚠️ SEO Bilgileri (seo_settings Tablosu)

**KRİTİK:**
- SEO bilgileri **polymorphic ilişki** ile `seo_settings` tablosuna kaydedilmeli
- Blog modeli zaten `HasSeo` trait'i kullanıyor
- **SEO'da site adını MANUEL EKLEME!** Sistem otomatik ekliyor (`site_title` setting'den)

**Kod Örneği:**
```php
// Blog SEO kaydı (HasSeo trait ile)
$blog->seoSetting()->create([
    'titles' => ['tr' => 'Blog Başlığı', 'en' => 'Blog Title'],
    'descriptions' => ['tr' => 'Açıklama...', 'en' => 'Description...'],
    'keywords' => ['transpalet', 'forklift'],
    'status' => 'active'
]);
// ✓ Site adı otomatik eklenir, manuel ekleme!
```

### 🖼️ Media Dosyaları (media Tablosu)

**KRİTİK:**
- Media dosyaları **Spatie Media Library** ile `media` tablosuna kaydedilmeli
- Blog modeli zaten `HasMediaManagement` trait'i kullanıyor
- Collection'lar: `featured_image`, `gallery`

**Kod Örneği:**
```php
// Featured image ekle
$blog->addMediaFromUrl($imageUrl)->toMediaCollection('featured_image');

// Galeri ekle
$blog->addMediaFromUrl($galleryImage)->toMediaCollection('gallery');
```

### ⚙️ AI Blog İçeriğinde Kullanılabilecek Ayarlar

**AI blog yazarken kullanılabilir:**
- **Setting Group 6**: https://ixtif.com/admin/settingmanagement/values/6
  - Site genel ayarları (site_title, site_description, contact vb.)
- **Setting Group 10**: https://ixtif.com/admin/settingmanagement/values/10
  - Ek tenant ayarları (markalaşma, özelleştirme)

**Kullanım:**
```php
$siteTitle = setting('site_title'); // Site adını al
$contactEmail = setting('contact_email'); // İletişim emailini al
// AI prompt'a ekle: "Bu blog {$siteTitle} için yazılıyor..."
```

---

## 📂 FALLBACK KATEGORİ: "GENEL"

**Kategori bulunamazsa → "Genel" kategorisine at!**

### 🎯 Genel Kategori Bilgileri

**Kategori:**
- **ID**: 14
- **Slug**: `genel`
- **Başlık**: "Genel"
- **Açıklama**: "Kategorize edilemeyen veya genel içerikler"
- **Featured**: Hayır

**Kullanım Senaryoları:**

1. **AI kategori bulamadı** → Genel'e at
2. **Multi-match** (birden fazla kategori uyuyor) → Genel'e at
3. **Belirsiz konu** → Genel'e at

**Örnek:**
```
Konu: "depo yönetimi stratejileri"
→ Ürün kategorisi yok (forklift/transpalet değil)
→ Genel kategori anahtar kelimeleri yok
→ Fallback: "Genel" kategorisi (ID: 14)
```

### 📊 Kategori Dağılımı (Güncel)

**Toplam: 14 kategori**

**Ana Kategoriler (6):**
1. Kullanım Kılavuzları
2. Karşılaştırma ve Seçim ⭐
3. Güvenlik ve Mevzuat
4. Sektör ve Teknoloji ⭐
5. İpuçları ve Püf Noktaları
6. Bakım ve Onarım

**Ürün Kategorileri (7):**
7. Forklift İncelemeleri ⭐
8. Transpalet İncelemeleri ⭐
9. İstif Makinesi İncelemeleri
10. Order Picker İncelemeleri
11. Otonom Sistemler ⭐
12. Reach Truck İncelemeleri
13. Yedek Parça Rehberi

**Fallback (1):**
14. **Genel** ← Kategori bulunamazsa buraya!

---

## 🔄 GÜNCELLENMİŞ WORKFLOW

```
MANUEL ÜRETİM:
1️⃣ Admin "AI ile Oluştur" butonuna tıklar
   ↓
2️⃣ Modal açılır (konu gir veya boş bırak)
   ↓
3️⃣ Kredi kontrolü (1 kredi var mı?)
   ↓
4️⃣ Konu belirle (manuel veya otomatik)
   ↓
5️⃣ Kategori belirle (AI analizi)
   ├─ Bulursa → İlgili kategori
   └─ Bulamazsa → "Genel" kategorisi (ID: 14)
   ↓
6️⃣ Blog içeriği üret (AI)
   ↓
7️⃣ Kredi düş (1 kredi)
   ↓
8️⃣ Blog kaydet ve göster
   ↓
9️⃣ Kredi log tut

OTOMATİK ÜRETİM (CRON):
1️⃣ Cron her 2 saatte bir çalışır
   ↓
2️⃣ Kredi kontrolü (yeterli mi?)
   ↓
3️⃣ Ayarları kontrol et (blog_ai_enabled = 1?)
   ↓
4️⃣ Konuları topla ve genişlet
   ↓
5️⃣ Her konu için:
   - Kategori belirle (bulamazsa → Genel)
   - Blog üret
   - Kredi düş (1 kredi)
   ↓
6️⃣ Günlük limit kontrol et (blog_ai_daily_count)
```

---

**Son Güncelleme**: 2025-11-14 (23:55)
**Değişiklikler:**
- ✅ 2 Aşamalı Prompt Sistemi eklendi (Taslak + İçerik)
- ✅ Kredi sistemi güncellendi (mevcut ai_credits_balance kullanılacak)
- ✅ **Kredi maliyeti güncellendi: 1 blog = 1 kredi (net ve basit!)**
- ✅ Taslak seçim sistemi eklendi (100 taslak üret → Seç → Yaz)
- ✅ B2B hedef kitle özellikleri eklendi
- ✅ SEO optimizasyon kuralları detaylandırıldı
- ✅ Schema markup gereksinimleri eklendi
- ✅ Manuel üretim + Kredi sistemi + Genel kategori
