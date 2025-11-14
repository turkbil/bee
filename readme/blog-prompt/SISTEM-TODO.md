# 🤖 BLOG AI SİSTEMİ - YAPILACAKLAR

**Tarih**: 2025-11-14
**Durum**: Hazırlık Tamamlandı - Kod Geliştirme Aşaması

---

## ✅ TAMAMLANANLAR

### 1. Ayarlar ve Veritabanı
- [x] SettingGroup 18 oluşturuldu (Blog - Yapay Zeka)
- [x] 6 ayar oluşturuldu (blog_ai_* settings)
- [x] Layout JSON tasarlandı (heading, paragraph, divider ile)
- [x] Admin panel ayarlar sayfası hazır (/admin/settingmanagement/values/18)

### 2. Blog Kategorileri
- [x] 13 kategori oluşturuldu (ixtif.com tenant)
  - 6 ana kategori (genel)
  - 7 ürün kategorisi bazlı (Shop kategorileri ile uyumlu)
- [x] Featured kategoriler işaretlendi
- [x] Kategori seçim algoritması tasarlandı (MD'de belgelenmiş)

### 3. Dokümantasyon
- [x] BLOG-AI-AYARLAR-ULTRA-SIMPLE.md (master döküman)
- [x] /public/readme/blog-prompt/basit-anlatim/index.html (clean dark mode)
- [x] Kategori seçim mantığı pseudo-code ile yazıldı
- [x] Workflow diagram oluşturuldu

---

## 🔨 DEVAM EDEN İŞLER

### 0. Tenant-Specific Prompt Customization (ÖNCE!)

**🎯 Amaç:** Her tenant için özelleştirilebilir AI prompt sistemi

**Klasör Yapısı**:
```
Modules/Blog/app/Services/TenantPrompts/
├── TenantPromptLoader.php       # Ana loader servisi
├── DefaultPrompts.php           # Default prompt'lar
└── Tenants/
    ├── Tenant2Prompts.php       # ixtif.com (shop odaklı)
    └── Tenant3Prompts.php       # Gelecekteki tenant'lar
```

**Görevler**:
- [ ] TenantPromptLoader servisi oluştur
  - [ ] getDraftPrompt() → Tenant ID'ye göre dinamik prompt
  - [ ] getBlogContentPrompt() → Tenant ID'ye göre dinamik prompt
  - [ ] getTenantContext() → Tenant'a özel ayarlar (modules, categories)
- [ ] DefaultPrompts servisi oluştur (fallback)
- [ ] Tenant2Prompts servisi oluştur (ixtif.com için shop odaklı)
  - [ ] Shop kategorilerini context'e ekle
  - [ ] Referanslar/Hizmetler modül bilgilerini ekle
  - [ ] Forklift/Transpalet odaklı prompt

**Avantajlar**:
- ✅ Tenant 2 (ixtif): Shop, ürünler, kategoriler odaklı blog
- ✅ Tenant 3: Farklı sektör, farklı prompt
- ✅ Yeni tenant: Default kullanır, sorun çıkmaz
- ✅ Kod değişikliği olmadan özelleştirme

---

### 1. Blog AI Servis Geliştirme

**Dosya**: `app/Services/BlogAI/BlogAIService.php` (oluşturulacak)

**Görevler**:
- [ ] AI provider entegrasyonu (mevcut System AI kullan)
- [ ] **TenantPromptLoader entegrasyonu ekle** (ÖNCE!)
- [ ] Konu genişletme servisi
  - [ ] Manuel konuları al
  - [ ] Ürün/kategori analizi yap (otomatik)
  - [ ] Sınırsız başlık üret (sektör boyutuna göre)
  - [ ] **DUPLICATE CHECK - KRİTİK:**
    - [ ] Mevcut blog başlıklarını çek: `Blog::pluck('titles')`
    - [ ] Mevcut draft'ları çek: `BlogAIDraft::pluck('topic_keyword')`
    - [ ] AI'a "bunları tekrarlama" listesi gönder
- [ ] Kategori seçim algoritması
  - [ ] Ürün kategorisi tespit (öncelikli)
  - [ ] İçerik analizi ile genel kategori belirleme
  - [ ] Multi-kategori desteği (primary + secondary)
- [ ] Blog içerik üretimi
  - [ ] 2000-2500 kelime otomatik
  - [ ] SEO optimizasyon (2025 standartları)
  - [ ] Stil rotasyonu (professional_only ayarına göre)
- [ ] Queue entegrasyonu
- [ ] **BATCH PROCESSING:**
  - [ ] `BlogAIBatchProcessor` servisi oluştur
  - [ ] Çoklu seçim için toplu işlem
  - [ ] Progress tracking: `['total' => 10, 'completed' => 3]`
- [ ] **ERROR HANDLING:**
  - [ ] Job retry logic: 3 deneme, 60sn backoff
  - [ ] Failed drafts tracking
  - [ ] Error mesajları ve retry button

### 1.5. Real-time Progress & Polling

**Görevler**:
- [ ] Livewire polling: `wire:poll.3s="checkBatchProgress"`
- [ ] Progress bar UI komponenti
- [ ] Failed items section
- [ ] Retry mechanism için UI

### 2. Cron Job Kurulumu

**Dosya**: `app/Console/Commands/BlogAIGenerate.php` (oluşturulacak)

**Görevler**:
- [ ] Artisan command oluştur
- [ ] Her 2 saatte bir çalışacak şekilde cron ayarla
- [ ] Ayarları kontrol et (blog_ai_enabled)
- [ ] Günlük limit kontrol et (blog_ai_daily_count)
- [ ] Queue'ya job gönder

### 3. Database Migration (Gerekirse)

**Görevler**:
- [ ] Blog tablosuna category_id_secondary ekle (multi-kategori için)
- [ ] Blog tablosuna ai_generated boolean ekle
- [ ] Blog tablosuna style enum ekle (professional, friendly, expert)

### 4. Admin Panel Geliştirme

**Görevler**:
- [ ] **AI Draft Sayfası** (`/admin/blog/ai-drafts`)
  - [ ] Taslak listesi (DataTable)
  - [ ] Checkbox seçim sistemi
  - [ ] Toplu işlem butonları
  - [ ] Progress bar ve real-time update
  - [ ] Error handling section
- [ ] Blog listesinde AI üretilmiş badge göster
- [ ] Kategori bazlı filtreleme
- [ ] AI status dashboard (bugün kaç blog üretildi?)
- [ ] **Settings Kontrolü** (`/admin/settingmanagement/values/18`)
  - [ ] blog_ai_enabled kontrolü
  - [ ] Günlük limit kontrolü
  - [ ] Manuel konular kontrolü

---

## ⚙️ KONFIGÜRASYON

### OpenAI API Settings:
```php
// config/modules/blog.php
'openai' => [
    'api_key' => env('OPENAI_API_KEY'),
    'model' => 'gpt-4-turbo-preview',
    'draft_temperature' => 0.7,    // Taslak için
    'blog_temperature' => 0.8,     // Blog içeriği için
    'draft_max_tokens' => 3000,    // Taslak token limiti
    'blog_max_tokens' => 8000      // Blog token limiti
]
```

### Queue Configuration:
- Queue name: `blog-ai`
- Worker: `php artisan queue:work --queue=blog-ai`
- Retry: 3 attempts
- Timeout: 300 seconds (5 dakika)

---

## 📋 ÖNCELİKLENDİRME (Sıralı)

0. **TenantPromptLoader** oluştur (öncelik 0 - EN ÖNCE!)
1. **BlogAIService** oluştur (öncelik 1)
2. **AI Draft Sayfası** ekle (öncelik 2)
3. **Manuel üretim butonu + modal** ekle (öncelik 3)
4. **Cron job** kur (öncelik 4)
5. **Test et** - Manuel konu ekle, blog üret (öncelik 5)

---

## 🎯 YENİ ÖZELLİK: TASLAK SEÇİM SİSTEMİ

### 💡 KONSEPT

**Sorun:** AI 100 blog üretirse hepsi gereksiz olabilir, kredi israfı!
**Çözüm:** Önce 100 **taslak başlık** üret, admin seçsin, sonra sadece seçilenleri yaz!

### 📋 AKIŞ

1. **AI Taslak Üretimi (AŞAMA 1)**
   - AI 100 blog başlığı + SEO meta + outline üretir
   - Kredi: 0.01/taslak = **1.0 kredi** (çok ucuz!)
   - `blog_ai_drafts` tablosuna kaydedilir

2. **Admin Seçim Yapar**
   - Admin 100 başlıktan istediğini checkbox ile seçer (örn: 10 tane)
   - `is_selected = true` olarak işaretlenir

3. **Tam Blog Yazımı (AŞAMA 2)**
   - Sadece seçilen 10 taslak için AŞAMA 2 çalışır
   - Kredi: 1.0/blog = **10.0 kredi**
   - `blogs` tablosuna kaydedilir, `status='draft'`

### 💰 MALIYET ANALİZİ (GÜNCELLENDI)

```
Araştırma (100 Taslak):   1.0 kredi (TOPLAM - adet fark etmez!)
10 Seçili Blog:          10.0 kredi (1.0 × 10)
───────────────────────────────────────────────────
TOPLAM:                  11.0 kredi
```

**NOT:** Araştırma maliyeti sabittir! 50 taslak da olsa, 100 taslak da olsa = 1.0 kredi

**Avantajlar:**
- ✅ Net maliyet (1 blog = 1 kredi, basit hesaplama)
- ✅ Kalite kontrolü (admin gereksiz içerikleri elemiş olur)
- ✅ Verimlilik (sadece seçilen taslaklar için kredi harcanır)
- ✅ Esneklik (istediğini seç, istemediğini sil)

### 🗄️ YENİ TABLO: blog_ai_drafts

```sql
CREATE TABLE blog_ai_drafts (
    id BIGINT PRIMARY KEY,
    title JSON COMMENT 'Çoklu dil başlık',
    slug JSON COMMENT 'Auto-generated slug',
    seo_meta JSON COMMENT 'SEO title, desc, keywords (AŞAMA 1)',
    content_outline JSON COMMENT 'H2/H3 yapısı, kelime sayısı',
    faq_questions JSON COMMENT '10 FAQ sorusu',
    schema_plan JSON COMMENT 'Schema.org plan (Article, FAQPage)',
    blog_category_id BIGINT FOREIGN KEY,
    topic_source ENUM('manual', 'product_analysis', 'category_analysis'),
    is_selected BOOLEAN DEFAULT false INDEX,
    is_generated BOOLEAN DEFAULT false INDEX,
    generated_blog_id BIGINT FOREIGN KEY NULLABLE,
    ai_cost DECIMAL(10,4) DEFAULT 0.01,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    INDEX (is_selected, is_generated),
    INDEX (blog_category_id)
);
```

### 📝 YENİ GÖREVLER

**Öncelik 1: Database**
- [ ] Migration oluştur: `blog_ai_drafts` tablosu (central + tenant)
- [ ] Model oluştur: `app/Models/BlogAIDraft.php`

**Öncelik 2: Taslak Üretimi**
- [ ] Service: `BlogAIDraftGenerator.php` (100 taslak üret)
- [ ] Konu toplama + genişletme
- [ ] AŞAMA 1 AI prompt çağrısı
- [ ] Kredi yönetimi (1.0 kredi düş)

**Öncelik 3: Admin UI**
- [ ] Livewire: `BlogAIDraftComponent.php` (taslak listesi)
- [ ] View: DataTable + Checkbox + Önizleme modal
- [ ] Filtre: Kategori, Kaynak, Seçim durumu
- [ ] "100 Taslak Üret" butonu
- [ ] "Seçilenleri Yaz" butonu

**Öncelik 4: Blog Yazımı**
- [ ] Job: `GenerateBlogFromDraft.php` (AŞAMA 2)
- [ ] Seçili taslakları al
- [ ] AŞAMA 2 AI prompt çağrısı (2000-2500 kelime)
- [ ] `blogs` tablosuna kaydet
- [ ] ⚠️ **SEO bilgilerini `seo_settings` tablosuna kaydet** (polymorphic ilişki, `HasSeo` trait)
- [ ] ⚠️ **Media'ları `media` tablosuna kaydet** (Spatie Media Library, `HasMediaManagement` trait)
- [ ] `generated_blog_id` ilişkisini güncelle
- [ ] Kredi yönetimi (1.0/blog düş)

**Öncelik 5: Test**
- [ ] 100 taslak üret, kontrol et
- [ ] 10 taslak seç, checkbox test
- [ ] Seçilenleri yaz, kredi düşüşü kontrol et
- [ ] Blog listesinde AI badge göster

### 📚 DÖKÜMANLAR

**Detaylı Tasarım:** `/public/readme/blog-prompt/taslak-secim-sistemi.html`
- Tablo yapısı
- Workflow diagramı
- Admin UI mockup
- Kod örnekleri (Migration, Model, Service, Livewire)

---

## 🎯 SONRAKİ ADIMLAR

### ⚡ KREDİ SİSTEMİ - MEVCUT ALTYAPI KULLANILACAK

**NOT:** Yeni kredi sistemi migration'ına gerek yok! Sistem zaten hazır:
- ✅ `tenants.ai_credits_balance` kolonu mevcut
- ✅ `ai_use_credits()` helper fonksiyonu hazır
- ✅ `Modules\AI\App\Models\AICreditUsage` tablosu var
- ✅ AI modülünde kredi yönetimi çalışıyor

**Blog + Fotoğraf Kredi Maliyeti:**
- Taslak üretimi: **0.01 kredi/taslak** (100 taslak = 1.0 kredi)
- Blog yazımı: **1.0 kredi/blog** (net ve basit)
- Fotoğraf gelecekte eklenecek (ayrı düşecek)

### Hemen Yapılacaklar

1. **BlogAIService.php** oluştur
   - Namespace: `App\Services\BlogAI`
   - Methods:
     - `generateTopics()` - Konu toplama ve genişletme
     - `determineCategory($topic, $content)` - Kategori seçimi (fallback: "Genel")
     - `generateBlogContent($topic, $category)` - Blog içeriği üret
     - `checkDuplicate($title)` - Başlık duplicate kontrolü
   - **Kredi Yönetimi:**
     - `ai_can_use_credits(1.0)` - Yeterli kredi var mı kontrol (1 blog = 1 kredi)
     - `ai_use_credits(1.0, null, ['usage_type' => 'blog_generation'])` - Kredi düş
     - Metadata: operation_type, word_count, provider_name

2. **BlogAITopicExpander.php** oluştur
   - Namespace: `App\Services\BlogAI`
   - Methods:
     - `expandTopic($baseTopic)` - Tek konu genişlet
     - `detectSectorSize()` - Ürün/kategori sayısını tespit et
     - `calculateExpandLimit($sectorSize)` - Kaç başlık üretilecek?

3. **BlogAICategorySelector.php** oluştur
   - Namespace: `App\Services\BlogAI`
   - Methods:
     - `selectCategory($topic, $content)` - Kategori seç
     - `detectProductCategory($topic)` - Ürün kategorisi tespit
     - `analyzeContentKeywords($content)` - İçerik anahtar kelime analizi
     - `getFallbackCategory()` - "Genel" kategorisini döndür (ID: 14)

4. **Manuel Üretim - Livewire Component** oluştur
   - `Modules/Blog/app/Http/Livewire/Admin/BlogAIGenerateComponent.php`
   - Modal açar
   - Konu input (opsiyonel)
   - "Blog Oluştur" butonu
   - Kredi kontrolü (`ai_can_use_credits()` ile)
   - Blog üret ve redirect
   - Kredi bakiyesi modal'da göster

5. **Manuel Üretim - Buton Ekle**
   - `/admin/blog` sayfasına "AI ile Oluştur" butonu ekle
   - Modal trigger
   - Mevcut kredi bakiyesini badge olarak göster (AI modülünden çek)

6. **Cron Job** kur
   - `php artisan make:command BlogAIGenerate`
   - Kredi kontrolü ekle (`ai_can_use_credits()`)
   - Schedule: `$schedule->command('blog:ai-generate')->everyTwoHours()`

---

## 🔧 TEKNİK DETAYLAR

### AI Prompt Template

```
Sen bir endüstriyel ekipman blog yazarısın.

Konu: {$topic}
Kategori: {$category}
Stil: {$style} (professional/friendly/expert)
Kelime Sayısı: 2000-2500 kelime

Blog yazarken:
- SEO uyumlu başlık ve meta description oluştur
- H1, H2, H3 başlıkları kullan
- 2025 SEO standartlarına uy (E-E-A-T)
- İçerik özgün ve bilgilendirici olsun
- Türkçe dilbilgisi kurallarına dikkat et
- {$productInfo} (varsa ürün bilgilerini içer)
```

### Kategori Seçim Pseudo-code

```php
function selectCategory($topic, $content) {
    // 1. Ürün kategorisi tespit (öncelikli)
    $productKeywords = [
        'forklift' => 'Forklift İncelemeleri',
        'transpalet' => 'Transpalet İncelemeleri',
        'istif' => 'İstif Makinesi İncelemeleri',
        // ...
    ];

    foreach ($productKeywords as $keyword => $categoryName) {
        if (str_contains(strtolower($topic), $keyword)) {
            return BlogCategory::where('title->tr', $categoryName)->first();
        }
    }

    // 2. İçerik analizi
    if (str_contains_any($content, ['nasıl', 'kullanım', 'kurulum'])) {
        return BlogCategory::where('slug', 'kullanim-kilavuzlari')->first();
    }

    // ...

    // 3. Default
    return BlogCategory::where('slug', 'karsilastirma-ve-secim')->first();
}
```

### Sınırsız Genişletme Logic

```php
function calculateExpandLimit() {
    $productCount = Product::count();

    if ($productCount < 10) return 30;      // Dar sektör
    if ($productCount < 100) return 100;    // Orta sektör
    return 200;                             // Geniş sektör
}
```

---

## 📊 BAŞARI KRİTERLERİ

- [ ] Manuel konu "transpalet" → 30+ başlık üret
- [ ] Otomatik kategori seçimi çalışıyor (Transpalet İncelemeleri seçilir)
- [ ] Blog içeriği 2000-2500 kelime
- [ ] Duplicate başlık yok
- [ ] Stil rotasyonu çalışıyor
- [ ] Cron job her 2 saatte çalışıyor
- [ ] Günlük limit uygulanıyor (blog_ai_daily_count)

---

## 🚨 DİKKAT EDİLECEKLER

1. **TENANT SİSTEMİ - ÇOK KRİTİK:**
   - ⚠️ Bu bir **multi-tenant sistem**
   - ⚠️ Her tenant'ın **AYRI DATABASE'i** var (tenant-specific)
   - ⚠️ **AI kredi** ve merkezi veriler **CENTRAL database'de** (central)
   - ⚠️ `blog_ai_drafts` tablosu **TENANT database'inde** olmalı
   - ⚠️ Migration: Hem `database/migrations/` hem `database/migrations/tenant/` oluştur
   - ⚠️ Her zaman `tenant()` context'inde çalış
2. **Queue Kullan**: Senkron işlem yapma, queue'ya gönder
3. **Error Handling**: AI çağrısı başarısız olursa retry yap (max 3)
4. **Rate Limiting**: AI provider rate limit'e dikkat et
5. **Database Transaction**: Blog + kategori ilişkisi atomik olmalı
6. ⚠️ **SİSTEM MİMARİSİNİ BOZMA:**
   - **SEO bilgileri** → `seo_settings` tablosuna (polymorphic ilişki ile)
   - **SEO'da site adı manuel ekleme!** Sistem otomatik ekliyor (`site_title` setting'den)
   - **Media dosyaları** → `media` tablosuna (Spatie Media Library ile)
   - Blog modeli zaten `HasSeo` ve `HasMediaManagement` trait'lerini kullanıyor
   - Mevcut sistem mimarisine uygun kod yaz!
7. **AI Blog İçeriğinde Kullanılabilecek Ayarlar:**
   - Setting Group 6: Site genel ayarları (site bilgileri, iletişim)
   - Setting Group 10: Ek ayarlar (markalaşma, özelleştirme)
   - AI blog yazarken bu ayarları içeriğe dahil edebilir
   - Kullanım: `setting('key_name')` helper ile erişilebilir

---

## 📁 DOSYA YAPISI

```
app/
├── Models/
│   └── BlogAIDraft.php                 # YENİ: Taslak modeli
│
├── Services/
│   └── BlogAI/
│       ├── BlogAIService.php
│       ├── BlogAIDraftGenerator.php    # YENİ: Taslak üretimi
│       ├── BlogAITopicExpander.php
│       ├── BlogAICategorySelector.php
│       └── BlogAIContentGenerator.php
│
├── Console/
│   └── Commands/
│       └── BlogAIGenerate.php
│
└── Jobs/
    ├── GenerateBlogPost.php
    └── GenerateBlogFromDraft.php       # YENİ: Taslaktan blog yaz

Modules/Blog/
├── app/
│   ├── Models/
│   │   ├── Blog.php
│   │   └── BlogCategory.php
│   │
│   └── Http/
│       └── Livewire/
│           └── Admin/
│               └── BlogAIDraftComponent.php  # YENİ: Taslak listesi UI
│
└── database/
    └── migrations/
        └── tenant/
            ├── YYYY_MM_DD_create_blog_ai_drafts_table.php  # YENİ
            └── YYYY_MM_DD_add_ai_fields_to_blogs.php
```

---

## 🔗 REFERANSLAR

**Ayarlar**: https://ixtif.com/admin/settingmanagement/values/18
**Kategoriler**: https://ixtif.com/admin/blog/category
**Master Döküman**: /var/www/vhosts/tuufi.com/httpdocs/readme/blog-prompt/BLOG-AI-AYARLAR-ULTRA-SIMPLE.md
**HTML Genel Bakış**: https://ixtif.com/readme/blog-prompt/basit-anlatim/index.html
**HTML Taslak Seçim Sistemi**: https://ixtif.com/readme/blog-prompt/taslak-secim-sistemi.html

---

**Son Güncelleme**: 2025-11-14 (23:50)
**Değişiklikler**:
- Mevcut AI kredi sistemi kullanılacak, yeni migration gerekmiyor
- **YENİ:** Taslak seçim sistemi eklendi (`blog_ai_drafts` tablosu)
- 100 taslak üret → Admin seçsin → Sadece seçilenleri yaz
- **Maliyet güncellendi:** 1.0 kredi (taslaklar) + 1.0 kredi/blog (yazım) = 11.0 kredi (10 blog için)
