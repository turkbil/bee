  # 🚀 PAGE PATTERN BAZLI MODÜL DÖNÜŞÜMÜ

  ## 📋 GÖREV
  `/Users/nurullah/Desktop/cms/laravel/Modules/Page` modülünün **tüm özelliklerini, yapısını, fonksiyonlarını** bire bir kopyalayarak **Portfolio** ve **Announcement** modüllerini
  sıfırdan yeniden oluştur.

  ---

  ## 🎯 PROJE YAPISI PATTERN ANALİZİ

  ### Page Modülü Tam Yapısı:
  Page/
  ├── app/
  │   ├── Console/
  │   │   └── WarmPageCacheCommand.php
  │   ├── Contracts/
  │   │   └── PageRepositoryInterface.php
  │   ├── DataTransferObjects/
  │   │   ├── BulkOperationResult.php
  │   │   └── PageOperationResult.php
  │   ├── Enums/
  │   │   └── CacheStrategy.php
  │   ├── Events/
  │   │   └── TranslationCompletedEvent.php
  │   ├── Exceptions/
  │   │   ├── HomepageProtectionException.php
  │   │   ├── PageCreationException.php
  │   │   ├── PageException.php
  │   │   ├── PageNotFoundException.php
  │   │   ├── PageProtectionException.php
  │   │   └── PageValidationException.php
  │   ├── Http/
  │   │   ├── Controllers/
  │   │   │   ├── Admin/ (boş - Livewire kullanılıyor)
  │   │   │   ├── Api/
  │   │   │   │   └── PageApiController.php
  │   │   │   └── Front/
  │   │   │       └── PageController.php
  │   │   ├── Livewire/
  │   │   │   ├── Admin/
  │   │   │   │   ├── PageComponent.php (liste)
  │   │   │   │   └── PageManageComponent.php (manage/create/edit)
  │   │   │   ├── Front/ (boş)
  │   │   │   └── Traits/
  │   │   │       ├── InlineEditTitle.php
  │   │   │       ├── WithBulkActions.php
  │   │   │       └── WithBulkActionsQueue.php
  │   │   └── Resources/
  │   │       ├── PageCollection.php
  │   │       └── PageResource.php
  │   ├── Jobs/
  │   │   ├── BulkDeletePagesJob.php
  │   │   ├── BulkUpdatePagesJob.php
  │   │   ├── TranslatePageContentJob.php
  │   │   └── TranslatePageJob.php
  │   ├── Models/
  │   │   └── Page.php
  │   ├── Observers/
  │   │   └── PageObserver.php
  │   ├── Repositories/
  │   │   └── PageRepository.php
  │   └── Services/
  │       └── PageService.php
  ├── config/
  │   └── config.php (TAB sistem, cache, queue, SEO, validation, security)
  ├── database/
  │   ├── factories/
  │   │   └── PageFactory.php
  │   ├── migrations/
  │   │   ├── 2024_02_17_000001_create_pages_table.php
  │   │   └── tenant/
  │   │       └── 2024_02_17_000001_create_pages_table.php
  │   └── seeders/
  │       ├── PageSeeder.php
  │       ├── PageSeederCentral.php
  │       ├── PageSeederTenant2.php
  │       ├── PageSeederTenant3.php
  │       └── PageSeederTenant4.php
  ├── lang/
  │   ├── ar/
  │   │   └── admin.php
  │   ├── en/
  │   │   └── admin.php
  │   └── tr/
  │       ├── admin.php
  │       └── front.php
  ├── Providers/
  │   ├── EventServiceProvider.php
  │   ├── PageServiceProvider.php
  │   └── RouteServiceProvider.php
  ├── resources/
  │   └── views/
  │       ├── admin/
  │       │   ├── helper.blade.php
  │       │   ├── livewire/
  │       │   │   ├── page-component.blade.php
  │       │   │   └── page-manage-component.blade.php
  │       │   └── partials/
  │       │       ├── bulk-actions.blade.php
  │       │       └── inline-edit-title.blade.php
  │       ├── front/
  │       │   ├── index.blade.php
  │       │   └── show.blade.php
  │       └── themes/
  │           └── blank/
  │               └── index.blade.php
  ├── routes/
  │   ├── admin.php
  │   ├── api.php
  │   └── web.php
  ├── tests/
  │   ├── Feature/
  │   │   ├── PageAdminTest.php
  │   │   ├── PageApiTest.php
  │   │   ├── PageBulkOperationsTest.php
  │   │   ├── PageCacheTest.php
  │   │   └── PagePermissionTest.php
  │   ├── Unit/
  │   │   ├── PageModelTest.php
  │   │   ├── PageObserverTest.php
  │   │   ├── PageRepositoryTest.php
  │   │   └── PageServiceTest.php
  │   ├── README.md
  │   └── TestCase.php
  ├── module.json
  ├── phpunit.xml
  └── run-tests.sh

  ---

  ## 🔥 ÖNEMLİ KURALLAR

  ### ✅ YAPILACAKLAR:
  1. **ESKİ DOSYALARI SİL**: Portfolio ve Announcement modüllerindeki **tüm eski dosyaları tamamen kaldır**
  2. **BİRE BİR KOPYALA**: Page modülünün **her dosyasını, her fonksiyonunu, her özelliğini** aynen kopyala
  3. **İSİMLENDİRME**: Tüm `Page` referanslarını `Portfolio` veya `Announcement` ile değiştir:
     - Class isimleri: `PageController` → `PortfolioController`
     - Değişkenler: `$page` → `$portfolio` / `$announcement`
     - Tablolar: `pages` → `portfolios` / `announcements`
     - Primary Key: `page_id` → `portfolio_id` / `announcement_id`
     - Routes: `admin.page.index` → `admin.portfolio.index`
     - Config keys: `page.cache.enabled` → `portfolio.cache.enabled`
     - Lang keys: `page::admin.pages` → `portfolio::admin.portfolios`

  4. **VERİTABANI YAPISI**:
     - JSON çoklu dil sistemi: `title`, `slug`, `body` (array casts)
     - İndeksler: Page'deki tüm index'leri kopyala (composite indexes dahil)
     - Virtual column indexes (MySQL 8.0+): TR ve EN slug index'leri
     - Soft deletes: `deleted_at`
     - Timestamps: `created_at`, `updated_at`

  5. **PORTFOLIO ÖZEL ÖZELLİK - KATEGORİ SİSTEMİ**:
     - **YENİ TABLO**: `portfolio_categories` tablosu oluştur:
       ```php
       Schema::create('portfolio_categories', function (Blueprint $table) {
           $table->id('category_id');
           $table->json('name'); // Çoklu dil
           $table->json('slug'); // Çoklu dil
           $table->json('description')->nullable(); // Çoklu dil
           $table->boolean('is_active')->default(true)->index();
           $table->integer('sort_order')->default(0)->index();
           $table->timestamps();
           $table->softDeletes();
       });
       ```
     - **PORTFOLIOS TABLOSuna EKLE**: `category_id` foreign key:
       ```php
       $table->foreignId('category_id')->nullable()
           ->constrained('portfolio_categories', 'category_id')
           ->nullOnDelete();
       $table->index('category_id');
       ```
     - **YENİ MODEL**: `PortfolioCategory.php` (HasTranslations, HasSeo, Sluggable)
     - **YENİ REPOSITORY**: `PortfolioCategoryRepository.php`
     - **YENİ SERVICE**: `PortfolioCategoryService.php`
     - **YENİ LIVEWIRE**: `PortfolioCategoryComponent.php` ve `PortfolioCategoryManageComponent.php`
     - **RELATION**: Portfolio model'e `belongsTo` relation ekle:
       ```php
       public function category()
       {
           return $this->belongsTo(PortfolioCategory::class, 'category_id', 'category_id');
       }
       ```
     - **FİLTRELEME**: Portfolio liste sayfasında kategori filtreleme ekle

  ---

  ## 📦 MODEL ÖZELLİKLERİ

  ### Page Model'den Kopyalanacaklar:
  ```php
  // Traits
  use Sluggable, HasTranslations, HasSeo, HasFactory;

  // Fillable
  'title', 'slug', 'body', 'css', 'js', 'is_active', 'is_homepage' (sadece Page için)

  // Casts
  'is_homepage' => 'boolean',
  'title' => 'array',
  'slug' => 'array',
  'body' => 'array'

  // Translatable
  protected $translatable = ['title', 'slug', 'body'];

  // Scopes
  scopeActive(), scopeHomepage() (sadece Page)

  // Interface implementation
  implements TranslatableEntity

  // Methods
  getTranslatableFields(), hasSeoSettings(), afterTranslation(),
  getSeoFallback*() methods (title, description, keywords, canonical, image, schema),
  getOrCreateSeoSetting(), getIdAttribute()

  PORTFOLIO/ANNOUNCEMENT FARKLILIKLARI:
  - is_homepage alanı YOK
  - scopeHomepage() YOK
  - Portfolio: category_id ekle, category() relation ekle
  - Announcement: published_at, expires_at (datetime nullable) ekle
  - Announcement: scopePublished(), scopeActive() - yayın tarihi kontrolü

  ---
  🔧 REPOSITORY PATTERN

  Page Repository'den kopyala:
  readonly class PageRepository implements PageRepositoryInterface
  {
      // Properties
      private readonly string $cachePrefix;
      private readonly int $cacheTtl;
      private readonly TenantCacheService $cache;

      // Methods
      findById(), findByIdWithSeo(), findBySlug(), getActive(),
      getHomepage() (sadece Page), getPaginated(), search(),
      create(), update(), delete(), toggleActive(),
      bulkDelete(), bulkToggleActive(), updateSeoField(),
      clearCache(), getCacheKey(), getCacheTags()
  }

  PORTFOLIO ÖZEL:
  - findByCategory($categoryId) ekle
  - getByCategory($categoryId, $paginate = true) ekle

  ANNOUNCEMENT ÖZEL:
  - getPublished() ekle - yayın tarihi geçmiş, aktif olanlar
  - getUpcoming() ekle - yayın tarihi gelecek olanlar

  ---
  🎨 LIVEWIRE COMPONENT'LER

  1. ListComponent (PageComponent.php)

  class PageComponent extends Component
  {
      use WithPagination, WithBulkActionsQueue, InlineEditTitle, HasUniversalTranslation;

      // Properties
      #[Url] public $search, $perPage, $sortField, $sortDirection;
      public $selectedItems, $selectAll, $bulkActionsEnabled;
      private ?array $availableSiteLanguages = null;

      // Computed
      #[Computed] availableSiteLanguages(), adminLocale(), siteLocale();

      // Methods
      boot(), refreshPageData(), handleTranslationCompleted(),
      updatedPerPage(), updatedSearch(), sortBy(),
      toggleActive(), render(),
      queueTranslation(), translateFromModal()
  }

  PORTFOLIO ÖZEL EKLE:
  #[Url] public $categoryFilter = '';
  public function updatedCategoryFilter() { $this->resetPage(); }
  // render() içinde category filter ekle

  2. ManageComponent (PageManageComponent.php)

  - Form validation
  - Multi-language inputs
  - SEO tab integration
  - Universal Translation Modal
  - Monaco Editor integration
  - Save/Update logic

  ---
  🧩 TRAITS

  WithBulkActionsQueue.php

  trait WithBulkActionsQueue
  {
      // Properties
      public $bulkProgressVisible, $bulkProgress;

      // Methods
      bulkDelete(), bulkUpdate(), bulkToggleActive(),
      checkBulkProgress(), hideBulkProgress(),
      updatedSelectedItems(), updatedSelectAll(),
      translateContent(), confirmBulkDelete()
  }

  InlineEditTitle.php

  trait InlineEditTitle
  {
      public $editingTitleId, $editingTitleValue;

      // Methods
      startEditTitle(), updateTitle(), cancelEditTitle()
  }

  ---
  🚀 JOBS

  1. TranslatePageJob.php

  class TranslatePageJob implements ShouldQueue
  {
      public int $tries = 3;
      public int $timeout = 300;

      // Constructor params
      public array $pageIds,
      public string $sourceLanguage,
      public array $targetLanguages,
      public string $quality,
      public array $options,
      public string $operationId

      // Methods
      handle(), buildTranslationPrompt(),
      parseTranslationResponse(), updatePageTranslation(),
      updateProgress(), failed()
  }

  2. BulkDeletePagesJob.php

  class BulkDeletePagesJob implements ShouldQueue
  {
      public int $tries = 3;
      public int $timeout = 300;

      // Constructor params
      public array $pageIds,
      public string $tenantId,
      public string $userId,
      public array $options

      // Methods
      handle(), updateProgress(), clearPageCaches(), failed()
  }

  3. BulkUpdatePagesJob.php

  - Toplu güncelleme işlemleri
  - Progress tracking
  - Activity logging

  ---
  🎭 OBSERVERS

  Page Observer'dan kopyala:
  class PageObserver
  {
      // Events
      creating() - slug oluştur, homepage kontrolü
      created() - cache temizle, activity log
      updating() - homepage koruma, slug benzersizlik
      updated() - cache temizle, activity log
      saving() - CSS/JS boyut kontrolü, title validation
      saved() - universal SEO cache temizle
      deleting() - homepage koruma, reserved slug kontrolü
      deleted() - cache temizle, SEO ayarları sil
      restoring(), restored(), forceDeleting(), forceDeleted()
  }

  FARKLILIKLARI:
  - Homepage kontrollerini kaldır (Portfolio/Announcement'ta yok)
  - Portfolio: category_id validation ekle
  - Announcement: published_at, expires_at validation ekle

  ---
  ⚙️ CONFIG (config/config.php)

  Page config'den kopyala:
  return [
      'name' => 'Page',
      'slugs' => [...],
      'routes' => [...],
      'tabs' => [...],
      'form' => [...],
      'menu_url_types' => [...],
      'pagination' => [...],
      'features' => [...],
      'queue' => [...],
      'performance' => [...],
      'defaults' => [...],
      'media' => [...],
      'cache' => [...],
      'seo' => [...],
      'validation' => [...],
      'security' => [...]
  ];

  PORTFOLIO EKLE:
  'category' => [
      'enabled' => true,
      'required' => false,
      'default_category_id' => null,
      'show_in_menu' => true,
      'allow_multiple' => false, // Gelecek için
  ],

  ANNOUNCEMENT EKLE:
  'scheduling' => [
      'enabled' => true,
      'auto_publish' => true,
      'auto_expire' => true,
      'default_duration_days' => 30,
  ],

  ---
  🗄️ MİGRATION ÖRNEKLERİ

  Portfolio Migration:

  Schema::create('portfolios', function (Blueprint $table) {
      $table->id('portfolio_id');
      $table->foreignId('category_id')->nullable()
          ->constrained('portfolio_categories', 'category_id')
          ->nullOnDelete();
      $table->json('title');
      $table->json('slug');
      $table->json('body')->nullable();
      $table->text('css')->nullable();
      $table->text('js')->nullable();
      $table->json('seo')->nullable();
      $table->boolean('is_active')->default(true)->index();
      $table->timestamps();
      $table->softDeletes();

      // Indexes - Page'deki gibi
      $table->index(['category_id', 'is_active']);
      $table->index(['is_active', 'deleted_at', 'created_at']);
      // ... (Page'deki tüm index'leri kopyala)
  });

  // JSON slug indexes (MySQL 8.0+) - Page'deki gibi

  Announcement Migration:

  Schema::create('announcements', function (Blueprint $table) {
      $table->id('announcement_id');
      $table->json('title');
      $table->json('slug');
      $table->json('body')->nullable();
      $table->text('css')->nullable();
      $table->text('js')->nullable();
      $table->json('seo')->nullable();
      $table->boolean('is_active')->default(true)->index();
      $table->dateTime('published_at')->nullable()->index();
      $table->dateTime('expires_at')->nullable()->index();
      $table->timestamps();
      $table->softDeletes();

      // Indexes
      $table->index(['is_active', 'published_at', 'deleted_at']);
      $table->index(['published_at', 'expires_at']);
      // ... (Page'deki tüm index'leri kopyala)
  });

  ---
  🌐 ROUTES

  Admin Routes (routes/admin.php):

  Route::middleware(['admin', 'tenant'])
      ->prefix('admin')
      ->name('admin.')
      ->group(function () {
          Route::prefix('page')
              ->name('page.')
              ->group(function () {
                  Route::get('/', PageComponent::class)
                      ->middleware('module.permission:page,view')
                      ->name('index');

                  Route::get('/manage/{id?}', PageManageComponent::class)
                      ->middleware('module.permission:page,update')
                      ->name('manage');
              });
      });

  PORTFOLIO EKLE (category routes):
  Route::get('/category', PortfolioCategoryComponent::class)
      ->name('category.index');
  Route::get('/category/manage/{id?}', PortfolioCategoryManageComponent::class)
      ->name('category.manage');

  ---
  🎨 VIEWS

  Admin Views:

  1. helper.blade.php - Her sayfanın tepesinde
  2. page-component.blade.php - Liste view (tablo, bulk actions, inline edit)
  3. page-manage-component.blade.php - Create/Edit form (tabs, monaco editor, SEO)
  4. partials/bulk-actions.blade.php - Toplu işlem butonları
  5. partials/inline-edit-title.blade.php - Inline başlık düzenleme

  Frontend Views:

  1. front/index.blade.php - Liste sayfası
  2. front/show.blade.php - Detay sayfası
  3. themes/blank/index.blade.php - Tema örneği

  PORTFOLIO EKLE:
  - Category dropdown - liste filtreleme
  - Category badge - tabloda göster
  - Category create/edit views

  ---
  🧪 TESTS

  Page'deki tüm testleri kopyala:
  tests/
  ├── Feature/
  │   ├── PageAdminTest.php → PortfolioAdminTest.php
  │   ├── PageApiTest.php → PortfolioApiTest.php
  │   ├── PageBulkOperationsTest.php → PortfolioBulkOperationsTest.php
  │   ├── PageCacheTest.php → PortfolioCacheTest.php
  │   └── PagePermissionTest.php → PortfolioPermissionTest.php
  ├── Unit/
  │   ├── PageModelTest.php → PortfolioModelTest.php
  │   ├── PageObserverTest.php → PortfolioObserverTest.php
  │   ├── PageRepositoryTest.php → PortfolioRepositoryTest.php
  │   └── PageServiceTest.php → PortfolioServiceTest.php
  ├── README.md
  ├── TestCase.php
  └── phpunit.xml

  ---
  📝 DİL DOSYALARI (lang/)

  Page lang/tr/admin.php'yi kopyala ve tüm key'leri değiştir:
  'pages' → 'portfolios' / 'announcements'
  'page_management' → 'portfolio_management' / 'announcement_management'
  'new_page' → 'new_portfolio' / 'new_announcement'
  // ... tüm key'ler

  PORTFOLIO EKLE:
  'categories' => 'Kategoriler',
  'category' => 'Kategori',
  'select_category' => 'Kategori Seç',
  'no_category' => 'Kategorisiz',
  'category_management' => 'Kategori Yönetimi',

  ANNOUNCEMENT EKLE:
  'published_at' => 'Yayın Tarihi',
  'expires_at' => 'Bitiş Tarihi',
  'scheduled' => 'Zamanlanmış',
  'expired' => 'Süresi Dolmuş',

  ---
  🔥 SEEDER'LAR

  Page seeder pattern'ini kopyala:
  class PageSeeder extends Seeder
  {
      public function run(): void
      {
          $languages = ['tr', 'en'];

          // Anasayfa
          Page::create([
              'title' => [
                  'tr' => 'Anasayfa',
                  'en' => 'Homepage'
              ],
              'slug' => [...],
              'body' => [...],
              'is_active' => true,
              'is_homepage' => true, // Sadece Page'de
          ]);

          // Diğer sayfalar...
      }
  }

  PORTFOLIO: 3-5 örnek kategori + 10-15 portfolio item
  ANNOUNCEMENT: 5-10 örnek duyuru (geçmiş, aktif, gelecek tarihli)

  ---
  🚨 KRİTİK KONTROL LİSTESİ

  ✅ Her Modül İçin Kontrol Et:

  - Tüm Page referansları değiştirildi mi?
  - Primary key doğru mu? (portfolio_id, announcement_id)
  - Tablolar doğru isimde mi? (portfolios, announcements)
  - Config dosyası tamam mı?
  - Routes doğru mu?
  - Livewire component'ler register edildi mi?
  - Observer register edildi mi?
  - Repository binding yapıldı mı?
  - Migration index'leri tamam mı?
  - Seeder verileri eklendi mi?
  - Lang dosyaları çevrildi mi?
  - Tests path'leri doğru mu?

  ✅ Portfolio Özel:

  - portfolio_categories tablosu oluşturuldu mu?
  - Category model, repository, service oluşturuldu mu?
  - Category Livewire component'leri eklendi mi?
  - Portfolio-Category relation doğru mu?
  - Category filtreleme çalışıyor mu?
  - Category seeder verileri eklendi mi?

  ✅ Announcement Özel:

  - published_at, expires_at alanları eklendi mi?
  - scopePublished() çalışıyor mu?
  - Tarih filtreleme çalışıyor mu?
  - Otomatik yayın/bitiş sistemi config'de mi?

  ---
  📤 İŞLEM ADIMLARI

  1. TEMİZLEME:
  # Portfolio'daki eski dosyaları sil (Providers, routes, module.json hariç tümü)
  # Announcement'taki eski dosyaları sil (Providers, routes, module.json hariç tümü)
  2. PORTFOLIO OLUŞTUR:
    - Page modülünden tüm yapıyı kopyala
    - İsimlendirmeleri değiştir (Page → Portfolio, page → portfolio, pages → portfolios)
    - Kategori sistemini ekle (migration, model, repository, service, livewire)
    - is_homepage kaldır
    - Config, routes, providers güncelle
    - Seeders oluştur (categories + portfolios)
    - Tests uyarla
  3. ANNOUNCEMENT OLUŞTUR:
    - Page modülünden tüm yapıyı kopyala
    - İsimlendirmeleri değiştir (Page → Announcement, page → announcement, pages → announcements)
    - published_at, expires_at ekle
    - is_homepage kaldır
    - scopePublished() ekle
    - Config, routes, providers güncelle
    - Seeders oluştur (announcements)
    - Tests uyarla
  4. VERIFY:
  php artisan migrate:fresh --seed
  php artisan module:clear-cache
  php artisan app:clear-all
  5. TEST:
    - Admin panelde listeleme kontrol
    - Oluşturma/düzenleme/silme kontrol
    - Bulk işlemler kontrol
    - Çeviri sistemi kontrol
    - Frontend görüntüleme kontrol

  ---
  🎯 SONUÇ BEKLENTİSİ

  Bu işlem sonunda:
  - Portfolio: Page'in %100 kopyası + Kategori sistemi
  - Announcement: Page'in %100 kopyası + Yayın tarihleri
  - Her iki modül: Tüm Page özellikleri (bulk, queue, translation, SEO, cache, inline edit, tests)
  - Temiz kod: Hiç eski dosya kalmamalı
  - Çalışır durum: Migration + seed + test başarılı

  ---
  🚀 ŞIMDI BU PROMPT'A GÖRE HER İKİ MODÜLÜ DE YENİDEN OLUŞTUR!
