# CLAUDE MODÜL PATTERN REHBERİ - PAGE PATTERN SİSTEMİ

## 🎯 MODÜL MODERNLEŞTIRME PATTERN'I - PAGE PATTERN UYGULAMASI

**Kural**: Page modülü artık bizim standart pattern'imiz. Yeni modül geliştirme veya modernleştirme yaparken Page'i baz al.

### 📖 PATTERN KAVRAMLARI:
- **"Pattern uygula"** = Hem kod hem tasarım pattern'ı birlikte uygulanır
- **"Kod pattern'ı uygula"** = Sadece backend/service layer pattern'ı
- **"Tasarım pattern'ı uygula"** = Sadece frontend/UI pattern'ı
- **"Page pattern'ı"** = Page modülünün tüm yapısını (kod+tasarım) baz alma

### ✅ ANNOUNCEMENT MODÜLÜ MODERNLEŞTIRME ÇALIŞMASI (BAŞARILI)

**Kılavuz İlkeler:**
1. **Kod ve Tasarım Pattern'ini Birlikte Uygula** - "Pattern" dendiğinde hem kod yapısı hem UI/UX tasarımı dahil
2. **JSON Multi-Language Desteği** - HasTranslations trait ile `{"tr":"text","en":"text"}` formatı
3. **Modern Laravel 12 + PHP 8.3+** - declare(strict_types=1), readonly classes, SOLID principles
4. **Global Service Integration** - GlobalSeoService, GlobalTabService entegrasyonu
5. **Module-Specific Config** - Her modülün kendi tab/seo konfigürasyonu (config/tabs.php)

### 🏗️ KOD PATTERN'I TAŞINAN ÖĞELER:

#### Backend Architecture:
```
✅ Migration yapısı: JSON multi-language columns (title, slug, body)
✅ Model yapısı: HasTranslations trait, SEO relationships
✅ Service Layer: Readonly classes, SOLID principles, modern PHP 8.3+
✅ Repository Pattern: Interface binding, dependency injection
✅ DTO Classes: PageOperationResult → AnnouncementOperationResult
✅ Exception Classes: Custom module exceptions
✅ Cache Strategy: Smart caching enums
```

#### Component Architecture:
```php
✅ Livewire 3.5+ patterns:
   - Computed properties (#[Computed])
   - Modern dependency injection (boot method)
   - Multi-language state management
   - SEO data caching sistem (seoDataCache, allLanguagesSeoData)
   - Tab completion tracking
   - Language switching logic
```

#### Validation & Language:
```php
✅ Validation Rules: Multi-language field validation
✅ SlugHelper Integration: Automatic slug generation
✅ Language File Structure: 
   - Module-specific lang files
   - Global admin.php key additions
   - Validation.php attributes for nested fields
```

#### Configuration:
```php
✅ Module Config: config/tabs.php structure
✅ Service Provider: Modern binding patterns
✅ Global Service Integration: TabService, SeoService
```

### 🎨 TASARIM PATTERN'I TAŞINAN ÖĞELER:

#### UI/UX Components:
```html
✅ Form Layout Pattern (Page/manage → Announcement/manage):
   - Floating label inputs
   - Pretty checkbox/switch components  
   - Tab-based organization (basic, seo)
   - Language switcher tabs
   - Responsive grid layout (col-md-6, col-lg-4 patterns)
```

#### Form Elements:
```html
✅ Input Components:
   - Form floating labels: <div class="form-floating">
   - Pretty switches: class="form-check form-switch"
   - TinyMCE integration: standardized editor setup
   - Choices.js selectboxes: standardized dropdown styling
```

#### Language System UI:
```html
✅ Multi-Language Tabs:
   - Bootstrap nav-tabs structure
   - Language flag icons (if available)
   - Active language highlighting
   - Seamless language switching UX
```

#### SEO Panel Design:
```html
✅ SEO Tab Structure:
   - SEO title input with character counter
   - Meta description textarea with character limit
   - Keywords input with tag-like styling
   - Canonical URL input
   - SEO score indicators (if available)
```

#### Button & Action Patterns:
```html
✅ Action Buttons:
   - Save button styling: btn btn-primary
   - Save & Continue: btn btn-success  
   - Cancel button: btn btn-secondary
   - Studio Editor integration button
   - Consistent button positioning and spacing
```

#### Layout & Spacing:
```html
✅ Page Structure:
   - Card-based layout: class="card"
   - Consistent padding: p-3, p-4 patterns
   - Proper spacing: mb-3, mt-2 utilities
   - Responsive breakpoints
```

#### JavaScript Integration:
```javascript
✅ Frontend Interactions:
   - Language switching JavaScript
   - TinyMCE content synchronization
   - Form validation feedback
   - Tab switching animations
   - Auto-save functionality patterns
```

### 🔧 ÖZELLEŞTIRMELER (Module-Specific):

#### Announcement'a Özel Değişiklikler:
```
❌ Homepage alanı kaldırıldı (announcements homepage olamaz)
❌ Code tab kaldırıldı (announcements'ta kod alanı yok)
✅ Announcement-specific validation rules
✅ Announcement-specific language keys
✅ Module-specific tab configuration
```

### 📋 PATTERN UYGULAMA REHBERİ:

#### 1. Tasarım Pattern'ı Uygularken:
```bash
# Page modülünün Blade dosyalarını incele:
- resources/views/admin/livewire/page-manage-component.blade.php
- Form yapısını, CSS class'larını, JavaScript entegrasyonlarını kopyala
- Module-specific customization'ları yap (homepage kaldır vs.)
```

#### 2. Kod Pattern'ı Uygularken:
```bash
# Page modülünün PHP dosyalarını incele:
- app/Models/Page.php → HasTranslations, SEO relationship
- app/Services/PageService.php → Readonly, SOLID principles  
- app/Http/Livewire/Admin/PageManageComponent.php → Modern Livewire
- config/tabs.php → Tab configuration
```

#### 3. Her İkisini Birlikte Uygularken:
```bash
# Announcement örneğindeki gibi:
1. Migration'ı düzenle (JSON columns)
2. Model'i güncelle (HasTranslations trait)
3. Service layer'ı modernleştir
4. Component'i yeniden yaz (Page pattern)
5. Blade template'ini Page'den kopyala ve uyarla
6. Config dosyalarını oluştur
7. Language dosyalarını güncelle
```

### 🔧 Teknik Uygulamalar:

#### Migration Pattern:
```php
// Announcement için homepage kolonu kaldırıldı (çünkü announcements homepage olamaz)
// Multi-language JSON kolonları: title, slug, body
$table->json('title');
$table->json('slug'); 
$table->json('body');
```

#### Model Pattern:
```php
// HasTranslations trait kullanımı
use App\Traits\HasTranslations;
protected $translatable = ['title', 'slug', 'body'];

// SEO relationship
public function seoSetting(): MorphOne
```

#### Service Pattern:
```php
// SOLID principles - readonly classes
readonly class AnnouncementService
{
    public function __construct(
        private AnnouncementRepositoryInterface $repository,
        private GlobalSeoRepositoryInterface $seoRepository
    ) {}
}
```

#### Component Pattern:
```php
// Livewire 3.5+ computed properties
public function availableSiteLanguages(): Collection
public function adminLocale(): string  
public function siteLocale(): string

// Modern dependency injection
public function boot() {
    $this->service = app(AnnouncementService::class);
}
```

#### Tab Configuration Pattern:
```php
// Modules/Announcement/config/tabs.php
return [
    'tabs' => [
        ['key' => 'basic', 'name' => 'Temel Bilgiler'],
        ['key' => 'seo', 'name' => 'SEO']
        // Code tab yok - Announcement'ta kod alanı olmaz
    ]
];
```

### 🚨 Kritik Sorun Çözümleri:

#### 1. Double-Encoded JSON Sorunu:
**Problem**: JSON veriler string olarak saklanıp getTranslated() doğru çalışmıyordu
**Çözüm**: HasTranslations trait'inde JSON decode kontrolü eklendi
```php
if (is_string($translations)) {
    $decoded = json_decode($translations, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $translations = $decoded;
    }
}
```

#### 2. Validation Hatalarının Türkçe Çevirisi:
**Problem**: Nested field validation hataları İngilizce çıkıyordu
**Çözüm**: 
- `lang/tr/validation.php` attributes'a field çevirileri eklendi
- `multiLangInputs.*.title => 'Başlık'` formatında

#### 3. Module-Specific Language Keys:
**Problem**: Blade'de `admin.announcement_url_slug` bulunamıyordu  
**Çözüm**: Global `lang/tr/admin.php`'ye modül-specific key'ler eklendi

### 📋 Checklist - Yeni Modül Pattern Uygulaması:

#### 🔹 Migration:
- [ ] JSON multi-language kolonları (title, slug, body)
- [ ] Module-specific kolonlar (homepage varsa kaldır vs.)
- [ ] Proper indexes ve foreign keys

#### 🔹 Model:
- [ ] HasTranslations trait ekle
- [ ] $translatable array tanımla  
- [ ] SEO morphOne relationship
- [ ] Modern fillable/casts tanımları

#### 🔹 Service Layer:
- [ ] Readonly service class
- [ ] Repository pattern dependency injection
- [ ] GlobalSeoService entegrasyonu
- [ ] SOLID principles uygulaması

#### 🔹 Component (Livewire):
- [ ] Computed properties (availableSiteLanguages, locales)
- [ ] Modern dependency injection (boot method)
- [ ] Multi-language input handling
- [ ] SEO data cache sistemi
- [ ] Tab completion tracking

#### 🔹 Configuration:
- [ ] Module config/tabs.php oluştur
- [ ] Module-specific tab configuration
- [ ] GlobalTabService entegrasyonu

#### 🔹 Language Files:
- [ ] Module lang dosyaları oluştur/güncelle  
- [ ] Global admin.php'ye module keys ekle
- [ ] Validation.php attributes güncelle

#### 🔹 Blade Templates:
- [ ] Page pattern'ındaki blade yapısını kopyala
- [ ] Module-specific customization'lar yap
- [ ] Language switcher entegre et
- [ ] Tab system entegre et

### 🎯 Sonuç:
Page pattern'ı başarıyla Announcement'a uyguladık. Bu metodoloji ile tüm modüller modernleştirilebilir.

### 🎯 MODÜL PATTERN SİSTEMİ - KAPSAMLI TANIMLAMA

#### **"PATTERN UYGULA" KOMUTU:**
- **Pattern**: Hem kod hem tasarım pattern'ı aynı anda uygulanır
- **Kod Pattern'i**: Sadece backend/logic yapısı uygulanır  
- **Tasarım Pattern'i**: Sadece frontend/UI yapısı uygulanır

#### **PAGE MODÜLÜ = MASTER PATTERN**
Page modülü artık **standart şablon** olarak kullanılır. Tüm yeni modüller Page pattern'ına uyar.

#### **KOD PATTERN YAPISI (Page → Diğer Modüller):**
- ✅ **Aynı dosya yapısı**: Models, Services, Controllers, Livewire, etc.
- ✅ **Aynı çoklu dil sistemi**: JSON field yapısı, getTranslated() methodları
- ✅ **Aynı SEO sistemi**: seoDataCache, allLanguagesSeoData patterns
- ✅ **Aynı validation sistemi**: MultiLang rules, SlugHelper entegrasyonu
- ✅ **Aynı component yapısı**: switchLanguage(), save(), load methodları
- ✅ **Modern PHP standards**: declare(strict_types=1), readonly, DTOs, Exceptions

#### **TASARIM PATTERN YAPISI (Page → Diğer Modüller):**
- **Form Sayfası**: `Modules/Page/manage` sayfasını pattern al
  - Form floating labels kullan
  - Pretty checkbox/switch kullan
  - **Selectbox**: Choices.js kullan (Portfolio manage örneği)
  - **Dil Sekmeleri**: Page'deki dil değiştirme UI'ı aynı şekilde
  - **SEO Paneli**: Page'deki SEO tab yapısı aynı şekilde
- **Sortable Liste**: Portfolio kategori listesini pattern al
  - JS ile drag-drop efekti
- **Tablo Listeleme**: Portfolio listesini pattern al
  - DataTable formatında
- **Basit Liste**: ModuleManagement page sayfasını pattern al
  - Basit liste görünümü

#### **PATTERN UYGULAMA ÖRNEKLERİ:**
```bash
# Her ikisini de uygula
"Page pattern'ını Announcement'a uygula"

# Sadece kod
"Page kod pattern'ını Announcement'a uygula" 

# Sadece tasarım
"Page tasarım pattern'ını Announcement'a uygula"
```

#### **ÖZEL DURUMLAR:**
- **Homepage alanı**: Sadece Page modülünde olur, diğer modüllerde olmaz
- **Modül-specific alanlar**: Her modülün kendine özel alanları olabilir
- **Core pattern**: Çoklu dil + SEO + Modern PHP her modülde ZORUNLU

#### **KURAL**: Yeni çalışmalar bu pattern'ları temel alsın!

### 🎯 **YENİ MODÜL OLUŞTURURKEN:**

1. **Response DTO'ları oluştur** (XxxOperationResult, BulkOperationResult)
2. **Cache Strategy** enum'unu kopyala ve adapt et
3. **Custom Exception'lar** modülüne özel oluştur
4. **Service Layer**: `declare(strict_types=1)`, readonly class, exception-first
5. **Repository**: Smart caching, modern PHP syntax
6. **Livewire**: `#[Computed]`, boot() dependency injection, type declarations

### 📊 **Kalite Standartları:**
- ✅ **SOLID Principles**: %100 uyumlu
- ✅ **Modern PHP 8.3+**: declare, readonly, match, nullsafe operator
- ✅ **Laravel 12**: Dependency injection, modern patterns
- ✅ **Exception-First**: Defensive programming
- ✅ **Smart Caching**: Performance optimization
- ✅ **Type Safety**: Strict types everywhere

### 🚨 **KRİTİK KURAL:**
**Her yeni modül Page modülünü pattern alacak! Aynı dosya yapısı, aynı modern kod standartları.**

## SlugHelper - Global Slug Yönetim Sistemi

**Konum**: `app/Helpers/SlugHelper.php` ve `app/Traits/HasSlugManagement.php`

Tüm modüllerde slug unique kontrolü ve otomatik düzeltme sistemi:

### Özellikler
- ✅ **Çoklu Dil Desteği**: Her dil için ayrı unique kontrol
- ✅ **Otomatik Düzeltme**: Duplicate slug'lar otomatik sayı ile düzeltilir (`iletisim` → `iletisim-1`)
- ✅ **Title'dan Slug**: Boş slug'lar title'dan otomatik oluşturulur
- ✅ **Türkçe Karakter Desteği**: ğ→g, ş→s dönüşümleri
- ✅ **Model Agnostic**: Her model için çalışır (Page, Portfolio, Announcement, vs.)
- ✅ **Validation Entegrasyonu**: Hazır validation kuralları ve mesajları

### Kullanım Örneği
```php
use App\Helpers\SlugHelper;
use App\Traits\HasSlugManagement;

class ExampleManageComponent extends Component
{
    use HasSlugManagement;
    
    // Save metodunda:
    $processedSlugs = $this->processMultiLanguageSlugs(
        ExampleModel::class,
        $this->multiLangInputs,
        $this->availableLanguages,
        $this->modelId
    );
}
```

### Mevcut Entegrasyonlar
- ✅ **Page Modülü**: `PageManageComponent` tamamen entegre edildi
- ✅ **Announcement Modülü**: `AnnouncementManageComponent` tamamen entegre edildi (Page pattern uygulandı)
- 🔄 **Portfolio Modülü**: Hazırlanabilir
- 🔄 **Diğer Modüller**: İhtiyaç halinde eklenebilir

**Detaylı dokümantasyon**: `SlugHelper_README.md`