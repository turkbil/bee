# 🤖 AI BLOG OTOMASYON SİSTEMİ - GENEL BAKIŞ

> **Otomatik Blog + Görsel Üretim Sistemi - 2025 SEO Standartları**

---

## 📋 SİSTEM AMAÇ VE KAPSAM

### 🎯 Ana Hedef
**Günün belirli saatlerinde, belirlenen konular + mevcut ürün/kategori verilerini kullanarak:**
- ✅ SEO-optimizasyonlu blog içerikleri
- ✅ Görsel/thumbnail üretimi (v2)
- ✅ Otomatik yayınlama
- ✅ Schema markup + metadata
- ✅ Social media entegrasyonu

### 🔄 Sistem Akışı

```
[Cron Job] → [Konu Seçici] → [AI Content Generator] → [Görsel Generator] → [SEO Optimizer] → [Yayınlayıcı]
     ↓              ↓                   ↓                      ↓                    ↓                ↓
  Zamanlama    Ürün/Kat.          OpenAI API           Stability AI         Schema + Meta     Database
  (6:00 AM)    Seçimi            GPT-4 Turbo          DALL-E 3 (v2)         E-E-A-T           + Cache
```

---

## 🏗️ SİSTEM MİMARİSİ

### Modül Yapısı

```
Modules/
├── AI/                          # Mevcut AI modülü (kullanılacak)
│   ├── Services/
│   │   ├── Chat/
│   │   │   └── ChatServiceV2.php
│   │   └── Prompts/
│   └── Models/
│       ├── AIFeature.php
│       └── AICreditUsage.php
│
├── Blog/                        # Mevcut Blog modülü (kullanılacak)
│   ├── Models/
│   │   ├── Blog.php
│   │   └── BlogCategory.php
│   └── Services/
│       ├── BlogService.php
│       └── BlogCategoryService.php
│
└── BlogAutomation/              # YENİ MODÜL (oluşturulacak)
    ├── app/
    │   ├── Console/
    │   │   └── Commands/
    │   │       ├── GenerateDailyBlogCommand.php
    │   │       ├── GenerateWeeklyBlogsCommand.php
    │   │       └── AnalyzeBlogPerformanceCommand.php
    │   │
    │   ├── Services/
    │   │   ├── BlogAutomationService.php
    │   │   ├── ContentStrategyService.php
    │   │   ├── TopicSelectorService.php
    │   │   ├── AIBlogGeneratorService.php
    │   │   ├── ImageGeneratorService.php (v2)
    │   │   ├── SEOOptimizerService.php
    │   │   └── PublishingService.php
    │   │
    │   ├── Models/
    │   │   ├── BlogAutomationSchedule.php
    │   │   ├── BlogAutomationLog.php
    │   │   ├── ContentStrategy.php
    │   │   └── BlogPerformanceMetric.php
    │   │
    │   └── Jobs/
    │       ├── GenerateBlogJob.php
    │       └── OptimizeBlogSEOJob.php
    │
    ├── database/
    │   └── migrations/
    │       ├── create_blog_automation_schedules_table.php
    │       ├── create_blog_automation_logs_table.php
    │       ├── create_content_strategies_table.php
    │       └── create_blog_performance_metrics_table.php
    │
    └── config/
        └── blog-automation.php
```

---

## 📊 VERİTABANI YAPISI

### Yeni Tablolar

#### 1. `blog_automation_schedules`
```sql
- id
- tenant_id
- schedule_type (daily, weekly, monthly)
- run_time (06:00, 14:00, 20:00)
- topic_source (manual, product_based, category_based, trending)
- topic_config (JSON: hangi kategoriler, hangi ürünler)
- is_active
- last_run_at
- next_run_at
- created_at, updated_at
```

#### 2. `blog_automation_logs`
```sql
- id
- schedule_id
- blog_id (nullable)
- status (pending, processing, completed, failed)
- topic
- ai_provider
- ai_model
- credits_used
- generation_time_seconds
- error_message (nullable)
- metadata (JSON: prompt, response, stats)
- created_at, updated_at
```

#### 3. `content_strategies`
```sql
- id
- tenant_id
- name
- description
- target_keywords (JSON array)
- target_audience
- content_tone (professional, casual, technical)
- content_length (short: 1000-1500, medium: 1500-2500, long: 2500+)
- include_faq (boolean)
- include_cta (boolean)
- seo_priority (1-10)
- is_active
- created_at, updated_at
```

#### 4. `blog_performance_metrics`
```sql
- id
- blog_id
- date
- views
- unique_visitors
- avg_time_on_page
- bounce_rate
- organic_traffic_percentage
- keyword_rankings (JSON: {keyword: rank})
- social_shares
- backlinks_count
- ctr
- conversions
- created_at, updated_at
```

---

## 🎨 2025 SEO STANDARTLARI (Sisteme Entegre)

### ✅ E-E-A-T (Experience, Expertise, Authoritativeness, Trustworthiness)
```php
// Her blog için otomatik eklenecek
- Author Bio (organizasyon bilgisi)
- Yayın tarihi + güncelleme tarihi
- Fact-checking kaynak linkleri
- İstatistik + veri kaynaklarını belirtme
```

### ✅ Core Web Vitals
```php
- LCP (Largest Contentful Paint) < 2.5s
- FID (First Input Delay) < 100ms
- CLS (Cumulative Layout Shift) < 0.1
- Optimizasyonlar:
  * WebP görsel formatı
  * Lazy loading
  * Minified CSS/JS
  * Critical CSS inline
```

### ✅ Yapısal Veri (Schema.org)
```json
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Article",
      "headline": "...",
      "author": {...},
      "publisher": {...},
      "datePublished": "...",
      "dateModified": "...",
      "image": {...},
      "articleBody": "..."
    },
    {
      "@type": "FAQPage",
      "mainEntity": [...]
    },
    {
      "@type": "BreadcrumbList",
      "itemListElement": [...]
    }
  ]
}
```

### ✅ İçerik Kalitesi (Google Helpful Content Update)
```php
// Sistemin kontrol edeceği kriterler:
- Özgün içerik (AI detection bypass)
- User intent odaklı
- Pratik bilgi + actionable insights
- Gerçek deneyim + case study
- Güncel veriler (2025)
- Mobile-first yazım
```

---

## 🔧 KONFIGÜRASYON ÖRNEKLERİ

### 1. Günlük Otomasyonlar
```yaml
Schedule 1: Sabah Blog (06:00)
  - Konu: Trending keywords + yeni ürünler
  - Uzunluk: 2000-2500 kelime
  - Strateji: SEO-focused, tutorial tarzı
  - Hedef: Organic traffic

Schedule 2: Öğle Blog (14:00)
  - Konu: Kategori bazlı rehber
  - Uzunluk: 1500-2000 kelime
  - Strateji: Product-focused, karşılaştırma
  - Hedef: Conversion

Schedule 3: Akşam Blog (20:00)
  - Konu: FAQ + problem solving
  - Uzunluk: 1000-1500 kelime
  - Strateji: Quick answer, how-to
  - Hedef: Featured snippet
```

### 2. Konu Belirleme Stratejileri

#### A. Ürün Bazlı
```php
// En çok görüntülenen ürünler
$products = Product::orderBy('view_count', 'desc')
    ->limit(10)
    ->get();

// Konu: "[Ürün Adı] Nedir? Özellikleri ve Kullanım Alanları"
```

#### B. Kategori Bazlı
```php
// En popüler kategoriler
$categories = Category::withCount('products')
    ->orderBy('products_count', 'desc')
    ->limit(5)
    ->get();

// Konu: "[Kategori] Rehberi 2025: Seçim Kriterleri"
```

#### C. Keyword Bazlı (Manuel Girdi)
```php
// Admin panelden belirlenen keyword listesi
$keywords = [
    'transpalet nedir',
    'forklift çeşitleri',
    'akülü istif makinesi fiyatları'
];
```

---

## 🚀 FARKLI VERSİYONLAR

### v1.0 - Blog Otomasyon (İlk Hedef)
- ✅ Cron job sistemi
- ✅ AI blog içerik üretimi
- ✅ SEO otomatik optimizasyon
- ✅ Otomatik yayınlama
- ❌ Görsel üretimi (manuel/placeholder)

### v2.0 - Görsel + Gelişmiş SEO
- ✅ AI görsel üretimi (DALL-E 3 / Stability AI)
- ✅ Thumbnail otomasyonu
- ✅ Video script üretimi (opsiyonel)
- ✅ Gelişmiş A/B testing

### v3.0 - Social Media + Analytics
- ✅ Otomatik social media paylaşımı
- ✅ Performance tracking + auto-optimization
- ✅ Content recommendation engine
- ✅ Multi-language auto-translation

---

## 📈 BEKLENEN KAZANIMLAR

### İş Gücü Tasarrufu
```
Manuel Blog Yazımı: 4-6 saat/blog
Otomatik Sistem: 5-10 dakika/blog
→ %95 zaman tasarrufu
```

### SEO Performans
```
Hedef:
- 50+ blog/ay üretimi
- Organic traffic %300 artış (6 ay)
- Featured snippet kazanma oranı %20
- Keyword ranking Top 10: %40
```

### Maliyet
```
AI API Maliyeti: ~$0.50-1.00/blog
Manuel İçerik Yazarı: ~$50-100/blog
→ %98 maliyet tasarrufu
```

---

## 📂 DOKÜMANTASYON YAPISI

```
readme/blog-prompt/sistem/
├── 00-SISTEM-GENEL-BAKIS.md (Bu dosya)
├── 01-VERITABANI-TASARIM.md
├── 02-CRON-JOB-KURULUM.md
├── 03-AI-PROMPT-SABLONLARI.md
├── 04-SEO-OPTIMIZASYON-KURALLARI.md
├── 05-GORSEL-URETIM-SISTEMI.md (v2)
├── 06-ADMIN-PANEL-ENTEGRASYON.md
├── 07-TEST-VE-MONITORING.md
└── 99-SORUN-GIDERME.md
```

---

## 🔐 GÜVENLİK VE LİMİTLER

### Rate Limiting
```php
// OpenAI API
- Max 50 request/dakika
- Max 500 request/saat
- Daily limit: 10,000 tokens/tenant

// Veritabanı
- Max 10 blog/saat/tenant
- Max 100 blog/gün/tenant
```

### Fail-Safe Mekanizmaları
```php
1. AI Response Validation
   - Minimum kelime sayısı kontrolü
   - Hakaret/spam filtresi
   - Duplicate content kontrolü

2. Error Handling
   - API fail → Retry (3x)
   - Retry fail → Admin notification
   - Emergency stop switch

3. Content Review (Opsiyonel)
   - Auto-publish: false (default)
   - Admin review before publish
   - Scheduled publish (delayed)
```

---

## 📞 SONRAKİ ADIMLAR

1. ✅ Sistem dokümantasyonu tamamlandı
2. ⏳ Veritabanı tasarımı detaylandırılacak
3. ⏳ Cron job kurulum dökümanı hazırlanacak
4. ⏳ AI prompt şablonları oluşturulacak
5. ⏳ Migration + Service dosyaları kodlanacak

---

**Son Güncelleme:** 2025-11-14
**Versiyon:** 1.0-PLANNING
**Durum:** Planlama Aşaması
