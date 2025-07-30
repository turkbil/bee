# SEO Ayarları - Gelişmiş SEO Yönetim Sistemi

## 📋 Proje Kapsamı ve Gereksinimler

### Temel Gereksinimler
- [ ] **Polymorphic SEO Sistemi**: Tüm modüller için tek SEO tablosu
- [ ] **Dinamik Dil Desteği**: Tenant bazlı aktif diller (ekleme/silme kusursuz)
- [ ] **AI Entegrasyon**: Her SEO alanı için AI önerileri
- [ ] **Gelişmiş Analytics**: SEO skoru, analiz, performans tracking
- [ ] **Satış Odaklı**: Marketing ve conversion optimization
- [ ] **100+ Modül Desteği**: Gelecekteki modüller için scalable

---

## 🗄️ Database Tasarımı

### SEO SETTINGS TABLOSU OLUŞTURMA
- [ ] **Dosya**: `database/migrations/2025_07_19_000001_create_seo_settings_table.php`
- [ ] **Amaç**: Tüm modüller için polymorphic SEO verisi tutmak
- [ ] **Temel Alanlar**:
  ```sql
  id, seoable_type, seoable_id (polymorphic)
  title (JSON), description (JSON), keywords (JSON)
  og_title (JSON), og_description (JSON), og_image (varchar)
  twitter_title (JSON), twitter_description (JSON), twitter_image (varchar)
  canonical_url (varchar), robots_meta (varchar), schema_markup (JSON)
  seo_score (integer 0-100), ai_recommendations (JSON)
  sitemap_priority (decimal), sitemap_changefreq (enum)
  is_active (boolean), created_at, updated_at
  ```
- [ ] **Index'ler**: 
  ```sql
  INDEX(seoable_type, seoable_id)
  INDEX(seo_score)
  INDEX(is_active)
  JSON INDEX(title->"$.tr")
  ```
- [ ] **Test**: Migration çalıştır, 1000 dummy record ekle, query performance ölç

### SEO SETTING MODELİ OLUŞTURMA  
- [ ] **Dosya**: `app/Models/SeoSetting.php`
- [ ] **Amaç**: SEO verilerini manage eden ana model
- [ ] **İlişkiler**:
  ```php
  public function seoable(): MorphTo
  protected $casts = ['title' => 'array', 'description' => 'array']
  ```
- [ ] **Helper Metodlar**:
  ```php
  getTitle(string $locale = null): ?string
  getDescription(string $locale = null): ?string  
  getRobotsMetaTag(): string
  generateSchemaMarkup(): array
  needsAiAnalysis(): bool
  ```
- [ ] **Test**: Her metod için unit test, dil fallback logic test et

### HAS SEO TRAIT OLUŞTURMA
- [ ] **Dosya**: `app/Traits/HasSeo.php`
- [ ] **Amaç**: Tüm modüllere SEO ilişkisi eklemek
- [ ] **Kod**:
  ```php
  public function seoSetting(): MorphOne
  public function getSeoTitle(string $locale = null): string
  public function getSeoDescription(string $locale = null): string
  public function updateSeo(array $data): void
  public function getOrCreateSeo(): SeoSetting
  ```
- [ ] **Kullanım**: Page, Portfolio, Announcement modellerine ekle
- [ ] **Test**: Her modülde trait çalışıyor mu test et

### CACHE SİSTEMİ KURMA
- [ ] **Dosya**: `app/Services/SeoCacheService.php`  
- [ ] **Amaç**: SEO verilerini cache'leyip performance artırmak
- [ ] **Cache Keys**:
  ```
  seo_settings:{type}:{id}:{locale} (1 saat)
  seo_active_languages (5 dakika)
  seo_schema:{type}:{id} (2 saat)
  ```
- [ ] **Metodlar**:
  ```php
  getCachedSeoSettings(string $type, int $id, string $locale)
  invalidateSeoCache(string $type, int $id)
  clearAllSeoCache()
  ```
- [ ] **Test**: Cache hit/miss oranı ölç, invalidation test et

---

## 🤖 AI Entegrasyon Sistemi

### AI SEO SERVİSİ OLUŞTURMA
- [ ] **Dosya**: `app/Services/SeoAIService.php`
- [ ] **Amaç**: AI powered SEO önerileri ve analiz
- [ ] **AI Provider Integration**:
  ```php
  analyzeContent(string $content, string $type): array
  generateTitle(string $content, string $locale): array // 3-5 öneri
  generateDescription(string $content, string $locale): array // 3-5 öneri
  extractKeywords(string $content, int $limit = 10): array
  calculateSeoScore(array $seoData): int // 0-100
  ```
- [ ] **Input**: Page content, mevcut SEO data, target language
- [ ] **Output**: JSON format öneriler, score, analysis
- [ ] **Test**: Her metod için mock AI response test et

### İÇERİK ANALİZ SİSTEMİ KURMA
- [ ] **Dosya**: `app/Services/ContentAnalyzer.php`
- [ ] **Amaç**: Page content'ini SEO için analiz etmek
- [ ] **Analiz Metodları**:
  ```php
  getWordCount(string $content): int
  getReadabilityScore(string $content): float // 0-100
  extractHeadings(string $content): array // H1, H2, H3...
  findImages(string $content): array // src, alt texts
  checkInternalLinks(string $content): array
  detectLanguage(string $content): string
  ```
- [ ] **Content Parsing**: HTML → text extraction + structure analysis
- [ ] **Test**: Farklı content tiplerinde (blog, sayfa, portfolio) test et

### KEYWORD ÇIKARTMA SİSTEMİ
- [ ] **Dosya**: `app/Services/KeywordExtractor.php`  
- [ ] **Amaç**: Content'ten semantic keywords çıkarmak
- [ ] **Extraction Logic**:
  ```php
  extractFromContent(string $content, string $locale): array
  calculateKeywordDensity(string $content, array $keywords): array
  findRelatedKeywords(string $keyword, string $locale): array
  analyzeCompetitorKeywords(string $url): array // gelecekte
  ```
- [ ] **AI Integration**: OpenAI/Claude API kullanarak semantic analysis
- [ ] **Output**: Keywords + density + search volume estimate
- [ ] **Test**: Türkçe ve İngilizce content ile keyword extraction test et

### SEO SKOR HESAPLAMA ALGORİTMASI
- [ ] **Dosya**: `app/Services/SeoScoreCalculator.php`
- [ ] **Amaç**: SEO settings için 0-100 skor hesaplamak
- [ ] **Scoring Factors**:
  ```php
  // Title (25 puan): uzunluk, keywords, clickbait check
  // Description (20 puan): uzunluk, keywords, call-to-action
  // Keywords (15 puan): sayı, relevance, density
  // Content Quality (25 puan): readability, structure, length
  // Technical SEO (15 puan): schema, canonical, robots
  ```
- [ ] **Hesaplama**:
  ```php
  calculateTitleScore(array $titleData): float
  calculateDescriptionScore(array $descData): float  
  calculateContentScore(array $contentData): float
  calculateTechnicalScore(array $techData): float
  getFinalScore(): int // toplam 0-100
  ```
- [ ] **Test**: Manuel scores ile AI scores karşılaştır

### GÖRSEL ANALİZ SİSTEMİ
- [ ] **Dosya**: `app/Services/ImageAnalyzer.php`
- [ ] **Amaç**: Content'teki görselleri analiz edip meta image öner
- [ ] **Analysis Methods**:
  ```php
  analyzeContentImages(string $content): array
  getImageDimensions(string $imagePath): array // width, height
  generateAltText(string $imagePath): string // AI vision
  selectBestMetaImage(array $images): string
  optimizeImageForSeo(string $imagePath): array
  ```
- [ ] **AI Vision Integration**: Görselleri analiz edip alt text ve description öner
- [ ] **Test**: Farklı image tiplerinde (foto, grafik, logo) test et

---

## 🎨 Admin Panel UI/UX

### SEO Management Interface
- [ ] **Page Edit Entegrasyonu**: SEO tab in page edit
- [ ] **Standalone SEO Manager**: Ayrı SEO yönetim sayfası
- [ ] **AI Suggestion Panel**: Live AI recommendations
- [ ] **Score Dashboard**: Visual SEO performance metrics
- [ ] **Bulk Operations**: Çoklu SEO optimization
- [ ] **Preview System**: Meta tag preview + Google SERP preview
- [ ] **Language Switcher**: Dil bazlı SEO field'lar
- [ ] **Advanced Settings**: Technical SEO configurations

### Responsive Components
- [ ] **SEO Score Widget**: Real-time score display
- [ ] **AI Recommendation Cards**: Actionable suggestions
- [ ] **Meta Preview**: Live SERP preview
- [ ] **Image Selector**: AI-suggested meta images
- [ ] **Keyword Manager**: Tag-based keyword input
- [ ] **Schema Editor**: Visual schema markup editor

---

## 🔧 Technical Implementation

### Core Services
- [ ] **SeoService**: Ana SEO logic servisi
- [ ] **SeoAnalyzer**: Performance analysis
- [ ] **SeoOptimizer**: Auto-optimization features
- [ ] **SitemapIntegration**: Mevcut sitemap service entegrasyonu
- [ ] **SchemaGenerator**: Advanced schema.org markup
- [ ] **MetaTagGenerator**: Dynamic meta tag generation
- [ ] **CanonicalManager**: Canonical URL optimization

### Frontend Integration
- [ ] **Meta Tag Injection**: Dynamic head tag generation
- [ ] **Schema Injection**: JSON-LD structured data
- [ ] **Sitemap Updates**: Real-time sitemap regeneration
- [ ] **OG Tag Generation**: Social media optimization
- [ ] **Twitter Card Generation**: Platform-specific optimization

---

## 🌐 Dil Yönetimi (Kritik) - Bulletproof JSON System

Bu sistem, tenant bazlı dinamik dil ekleme/çıkarma işlemlerinde SEO verilerinin tutarlılığını garanti eden çok katmanlı bir güvenlik sistemidir.

### 1. SeoLanguageManager Service - Merkezi Dil Yönetimi
- [ ] **Tenant Language Detection**: TenantLanguage modeli üzerinden aktif dilleri cache'li olarak algılar. Eğer tablo yoksa fallback olarak ['tr', 'en'] döner.
- [ ] **Multi-Level Fallback Strategy**: 
  - 1. İstenen dil (örn: 'de')
  - 2. Belirtilen fallback dil (örn: 'en') 
  - 3. Sistem varsayılan dili (tenant_default_locale)
  - 4. İlk boş olmayan değer
  Bu sayede hiçbir durumda SEO verisi kaybolmaz.

- [ ] **Chunk-Based Processing**: Büyük veri setlerini (100+ bin SEO kaydı) performanslı işlemek için chunk'lı güncelleme yapar. Memory overflow riski olmaz.

- [ ] **Transaction Safety**: Tüm dil ekleme/silme işlemleri database transaction içinde yapılır, yarım işlem riski yoktur.

### 2. JSON Structure Management - Nested JSON Yaklaşımı
Tercih edilen yapı: `{"title": {"tr": "başlık", "en": "title", "de": "titel"}}`

**Avantajları:**
- Temiz ve organize JSON yapısı
- Dil bazlı null değer yönetimi
- Kolay validation ve manipulation
- IDE autocomplete desteği

**Dezavantajları:**
- SQL query'lerde JSON path kullanımı gerekir: `JSON_EXTRACT(title, '$.tr')`
- Indexleme biraz daha kompleks olur

- [ ] **Dynamic Field Generation**: TenantLanguage'dan aktif diller alınır, her SEO JSON field'ına bu diller için placeholder değerler eklenir.

- [ ] **Data Consistency Guarantee**: 
  - Yeni dil ekleme: Tüm mevcut SEO kayıtlarına `{"new_lang": null}` eklenir
  - Dil silme: İlgili dil anahtarları tüm kayıtlardan kaldırılır
  - Orphaned language cleanup: Artık aktif olmayan dil verilerini temizler

- [ ] **Automatic Cleanup**: Kullanılmayan dil anahtarlarını otomatik tespit eder ve siler. Örn: 'fr' dili sistem pasifse ama JSON'da hala var ise temizler.

### 3. Observer Pattern - Real-time Sync
- [ ] **TenantLanguage Model Events**:
  - `created`: Yeni dil ekleme → SeoLanguageManager::addLanguage() tetiklenir
  - `updated`: is_active değişimi → Aktifse ekle, pasifse sil
  - `deleted`: Dil silme → Tüm SEO kayıtlarından temizle
  
Bu sistem sayede admin dil ekler/siler, SEO verileri anında otomatik senkronize olur.

- [ ] **Exception Handling**: Observer'lar fail edilirse sistem crash olmaz, log'a yazılır ve işleme devam eder.

### 4. Command Line Tools - Manuel Yönetim ve Acil Durum
- [ ] **seo:sync-languages Command**:
  - `--stats`: Sistem sağlık durumu, kaç kayıt sync bekliyor, hangi orphaned diller var
  - `--dry-run`: Test modu, ne yapılacağını gösterir değişiklik yapmaz  
  - `--force`: Onay almadan çalıştırır (cron job için uygun)
  
Bu komut acil durumlarda, migration sonrası veya data corruption durumunda sistemi onarır.

- [ ] **Health Check System**: 
  - Aktif diller vs JSON'daki diller karşılaştırması
  - Eksik/fazla dil tespiti  
  - Senkronizasyon ihtiyacı olan kayıt sayısı
  - Performance metrikleri

### 5. Cache Stratejisi - Performance Optimization
- [ ] **Multi-Layer Caching**:
  - Active languages: 5 dakika cache (sık değişmez)
  - Default language: 5 dakika cache
  - SEO settings: 1 saat cache (record bazlı)
  
- [ ] **Cache Invalidation**: 
  - Dil değişikliğinde: pattern-based cache clearing
  - SEO update'de: specific record cache clear
  - Bulk operation'da: tüm SEO cache clear

### 6. Validation ve Error Handling
- [ ] **JSON Structure Validation**: 
  - Her JSON field'ın doğru formatında olduğunu kontrol eder
  - Geçersiz dil kodlarını tespit eder
  - Corrupted JSON'ları repair eder

- [ ] **Graceful Degradation**: Sistem hiçbir durumda crash olmaz:
  - TenantLanguage tablosu yoksa: fallback diller
  - JSON corrupted ise: empty array'den rebuild
  - Service down ise: static fallback values

### 7. Migration Strategy - Backward Compatibility  
- [ ] **Legacy Data Support**: Eski string-based SEO verilerini JSON'a convert eder
- [ ] **Incremental Migration**: Büyük sistemlerde chunk'lı migration yapar
- [ ] **Rollback Support**: Migration geri alınabilir, original data korunur

### Örnek Kullanım Senaryoları:

**Senaryo 1: Yeni Dil Ekleme**
```
Admin "Almanca (de)" dili ekler
→ TenantLanguage::create(['code' => 'de']) 
→ Observer tetiklenir
→ SeoLanguageManager::addLanguage('de')
→ 50,000 SEO kaydında {"de": null} eklenir (chunk'lı)
→ Cache temizlenir
→ Sistem hazır, admin Almanca SEO girmeye başlayabilir
```

**Senaryo 2: Dil Silme**  
```
Admin "Fransızca (fr)" dilini siler
→ TenantLanguage::find('fr')->delete()
→ Observer tetiklenir  
→ SeoLanguageManager::removeLanguage('fr')
→ Tüm JSON field'lardan "fr" anahtarları kaldırılır
→ Cache temizlenir
→ FR verisi tamamen temizlenir
```

**Senaryo 3: Data Corruption Recovery**
```
Sistem yöneticisi şüpheli durum tespit eder
→ php artisan seo:sync-languages --stats
→ "150 kayıt sync bekliyor, orphaned: ['it', 'es']" 
→ php artisan seo:sync-languages --dry-run (test)
→ php artisan seo:sync-languages --force (onar)
→ Sistem restore edilir
```

Bu sistem sayede 100+ modül, binlerce SEO kaydı ve dinamik dil eklemelerinde hiçbir veri kaybı yaşanmaz.

---

## 📊 Analytics ve Monitoring

### Performance Tracking
- [ ] **SEO Score History**: Skor değişim grafiği
- [ ] **Keyword Rankings**: Anahtar kelime performansı
- [ ] **Page Performance**: Sayfa bazlı SEO metrikleri
- [ ] **AI Suggestion Success**: Öneri başarı oranları
- [ ] **Content Optimization**: İçerik optimization tracking
- [ ] **Technical SEO Health**: Site sağlık kontrolü

### Reporting System
- [ ] **SEO Dashboard**: Genel performans overview
- [ ] **Weekly Reports**: Otomatik SEO raporları
- [ ] **Improvement Suggestions**: Gelişim önerileri
- [ ] **Competitive Analysis**: Rakip analiz raporları
- [ ] **ROI Tracking**: SEO yatırım geri dönüşü

---

## 🚀 Advanced Features

### Automation Features
- [ ] **Auto-Optimization**: Scheduled SEO improvements
- [ ] **Smart Notifications**: SEO alert system
- [ ] **Bulk Import/Export**: SEO data management
- [ ] **API Integration**: External SEO tools integration
- [ ] **A/B Testing**: Meta tag testing framework
- [ ] **Content Suggestions**: AI-powered content recommendations

### Integration Features
- [ ] **Google Analytics**: GA4 integration
- [ ] **Search Console**: GSC data integration
- [ ] **Social Media APIs**: Platform-specific optimization
- [ ] **CDN Integration**: Image optimization for meta
- [ ] **Caching Strategy**: Advanced caching for performance

---

## 🔒 Security ve Performance

### Security Measures
- [ ] **Input Validation**: XSS ve injection protection
- [ ] **Rate Limiting**: AI API rate limiting
- [ ] **Permission System**: Role-based SEO access
- [ ] **Audit Logging**: SEO changes tracking

### Performance Optimization
- [ ] **Database Indexing**: Optimized query performance
- [ ] **Cache Strategy**: Multi-layer caching
- [ ] **Lazy Loading**: Resource optimization
- [ ] **Background Jobs**: Heavy operations queuing

---

## 📝 Implementation Roadmap

### Phase 1: Foundation (Hafta 1)
- [ ] Database migration ve model setup
- [ ] Basic polymorphic relationships
- [ ] Core SEO service development
- [ ] Admin panel basic UI

### Phase 2: AI Integration (Hafta 2)
- [ ] AI service layer development
- [ ] Content analysis features
- [ ] Recommendation engine
- [ ] Score calculation algorithm

### Phase 3: Advanced Features (Hafta 3)
- [ ] Advanced analytics
- [ ] Performance monitoring
- [ ] Bulk operations
- [ ] API integrations

### Phase 4: Polish & Optimization (Hafta 4)
- [ ] UI/UX improvements
- [ ] Performance optimization
- [ ] Testing ve bug fixes
- [ ] Documentation

---

## 🎯 Success Metrics

### Technical Metrics
- [ ] **Query Performance**: <100ms average response time
- [ ] **Cache Hit Rate**: >90% cache efficiency
- [ ] **AI Response Time**: <2s AI recommendations
- [ ] **Database Efficiency**: Optimized index usage

### Business Metrics
- [ ] **SEO Score Improvement**: %30+ average score increase
- [ ] **Time Savings**: %50+ SEO workflow efficiency
- [ ] **User Adoption**: %80+ feature usage rate
- [ ] **Content Quality**: Improved content metrics

---

## 🛠️ Technical Stack ve Mevcut Entegrasyonlar

### ✅ MEVCUT GÜÇLÜ KÜTÜPHANELER (Kullanacağız)
- [ ] **spatie/laravel-sitemap**: TenantSitemapService'e SEO integration
- [ ] **spatie/schema-org**: SEOService'e gelişmiş schema markup
- [ ] **spatie/laravel-responsecache**: SEO cache optimization
- [ ] **Modules\\AI\\App\\**: Mevcut AI sistemini SEO'ya entegre
- [ ] **intervention/image**: Meta image optimization
- [ ] **livewire/livewire v3.5**: SEO admin components
- [ ] **wire-elements/modal**: AI recommendation popup'lar
- [ ] **cviebrock/eloquent-sluggable**: SEO-friendly URL generation

### 🔧 EKLENMESİ GEREKEN MİNİMAL KÜTÜPHANELER
- [ ] **league/html-to-markdown**: Content parsing için
- [ ] **symfony/dom-crawler**: HTML analysis için  
- [ ] **opis/json-schema**: SEO JSON validation
- [ ] **openai-php/client**: AI provider integration (opsiyonel)

### 🎯 MEVCUT SİSTEM ENTEGRASYONLARI

#### SPATIE SITEMAP ENTEGRASYONU
- [ ] **Dosya**: `app/Services/TenantSitemapService.php` (mevcut)
- [ ] **Geliştirme**: SEO priority ve changefreq entegrasyonu
- [ ] **Kod Ekleme**:
  ```php
  // TenantSitemapService'e ekleme
  private static function addSeoOptimizedPages($sitemap) {
      SeoSetting::with('seoable')->active()->chunk(100, function($settings) use ($sitemap) {
          foreach($settings as $seo) {
              $sitemap->add(Url::create($seo->canonical_url)
                  ->setPriority($seo->sitemap_priority)
                  ->setChangeFrequency($seo->sitemap_changefreq));
          }
      });
  }
  ```

#### SPATIE SCHEMA ENTEGRASYONU  
- [ ] **Dosya**: `app/Services/SEOService.php` (mevcut)
- [ ] **Geliştirme**: Dynamic schema generation
- [ ] **Kod Ekleme**:
  ```php
  // SEOService'e ekleme
  public static function generateDynamicSchema(SeoSetting $seo): string {
      $schema = Schema::{$seo->schema_type}()
          ->name($seo->getTitle())
          ->description($seo->getDescription());
      
      if ($seo->schema_markup) {
          // Custom schema merge
      }
      return $schema->toScript();
  }
  ```

#### AI MODULE ENTEGRASYONU
- [ ] **Dosya**: `Modules/AI/app/Services/AIService.php` (mevcut)
- [ ] **Geliştirme**: SEO-specific AI prompts
- [ ] **Kod Ekleme**:
  ```php
  // AIService'e SEO metodları ekleme
  public function generateSeoTitle(string $content, string $locale): array {
      return $this->ask("SEO title oluştur: {$content}", [
          'type' => 'seo_title',
          'locale' => $locale,
          'format' => 'multiple_suggestions'
      ]);
  }
  ```

#### RESPONSE CACHE OPTİMİZASYONU
- [ ] **Dosya**: `config/responsecache.php` (mevcut)
- [ ] **Geliştirme**: SEO-aware cache keys
- [ ] **Kod Ekleme**:
  ```php
  // Cache profil güncellemesi
  'cache_profile' => App\Http\Middleware\CacheProfile::class,
  
  // SEO-specific cache tags
  'tags' => ['seo', 'meta_tags', 'schema']
  ```

### Backend Technologies (Mevcut)
- ✅ **Laravel 11**: Core framework
- ✅ **Eloquent ORM**: Database operations  
- ✅ **Redis/Predis**: Caching layer
- ✅ **Queue System**: Background processing
- ✅ **AI Services**: Modules/AI sistemi

### Frontend Technologies (Mevcut)
- ✅ **Livewire 3.5**: Interactive components
- ✅ **Alpine.js**: Frontend interactions
- ✅ **Tabler.io**: Admin UI framework (assumption)
- ⚠️ **Chart.js**: SEO analytics için eklenecek
- ⚠️ **TinyMCE**: Content editing için

---

## 📚 Documentation Requirements

### Technical Documentation
- [ ] **API Documentation**: SEO service endpoints
- [ ] **Database Schema**: Detailed table documentation
- [ ] **Integration Guide**: Third-party integrations
- [ ] **Deployment Guide**: Production setup instructions

### User Documentation
- [ ] **Admin Guide**: SEO management tutorial
- [ ] **Best Practices**: SEO optimization tips
- [ ] **Troubleshooting**: Common issues ve solutions
- [ ] **Video Tutorials**: Step-by-step guides

---

*Bu dokümandaki her checkbox, sistemin tamamlanma durumunu track etmek için kullanılacaktır. Geliştirme süreci boyunca düzenli olarak güncellenecektir.*