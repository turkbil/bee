# 🤖 BLOG AI SİSTEMİ - IMPLEMENTATION TODO

**Başlangıç Tarihi:** 2025-01-14
**Checkpoint Commit:** 09885bf76 (📚 BLOG AI SİSTEMİ - Detaylı Dokümantasyon Tamamlandı)

---

## 📋 İMPLEMENTATION ADIMLARI

### ✅ HAZIRLIK (TAMAMLANDI)

- [x] Git checkpoint commit (09885bf76)
- [x] Schema/Sitemap sistemi analizi
  - [x] SEOService analizi (getArticleSchema mevcut)
  - [x] HasSeo trait analizi (polymorphic seo_settings)
  - [x] TenantSitemapService analizi (blog auto-add)
  - [x] Blog model analizi (HasSeo + HasMediaManagement)
- [x] Mevcut credit system analizi (ai_use_credits helper mevcut)
- [x] Dokümantasyon tamamlandı

---

## 🗄️ PHASE 1: DATABASE & MODELS

### 1.1 Migration Oluşturma

- [ ] **Central Migration:** `database/migrations/YYYY_MM_DD_create_blog_ai_drafts_table.php`
  - İçerik: Boş migration (tenant için sadece placeholder)

- [ ] **Tenant Migration:** `database/migrations/tenant/YYYY_MM_DD_create_blog_ai_drafts_table.php`
  - Tablo: `blog_ai_drafts`
  - Kolonlar:
    ```php
    $table->id();
    $table->string('topic_keyword');          // Anahtar kelime
    $table->json('category_suggestions');     // [1, 5, 8] category ID'leri
    $table->json('seo_keywords');             // SEO anahtar kelimeler
    $table->json('outline');                  // Blog taslağı (başlıklar)
    $table->text('meta_description')->nullable();
    $table->boolean('is_selected')->default(false);
    $table->boolean('is_generated')->default(false);
    $table->foreignId('generated_blog_id')->nullable()->constrained('blogs')->onDelete('set null');
    $table->timestamps();
    $table->index(['is_selected', 'is_generated']);
    ```

- [ ] Migration çalıştır:
  ```bash
  php artisan migrate                    # Central için
  php artisan tenants:migrate            # Tenant'lar için
  ```

### 1.2 Model Oluşturma

- [ ] **BlogAIDraft Model:** `Modules/Blog/app/Models/BlogAIDraft.php`
  - Namespace: `Modules\Blog\App\Models`
  - Extends: `Illuminate\Database\Eloquent\Model`
  - Casts:
    ```php
    protected $casts = [
        'category_suggestions' => 'array',
        'seo_keywords' => 'array',
        'outline' => 'array',
        'is_selected' => 'boolean',
        'is_generated' => 'boolean',
    ];
    ```
  - Relations:
    ```php
    public function generatedBlog(): BelongsTo
    {
        return $this->belongsTo(Blog::class, 'generated_blog_id');
    }
    ```
  - Scopes:
    ```php
    public function scopeSelected($query)
    public function scopePending($query)  // selected but not generated
    public function scopeGenerated($query)
    ```

---

## 🛠️ PHASE 2: SERVICES

### 2.0 Tenant-Specific Prompt Customization (ÖNCE!)

**🎯 Amaç:** Her tenant için özelleştirilebilir AI prompt sistemi

- [ ] **Klasör Yapısı Oluştur:**
  ```
  Modules/Blog/app/Services/TenantPrompts/
  ├── TenantPromptLoader.php          # Ana loader servisi
  ├── DefaultPrompts.php              # Default prompt'lar
  └── Tenants/                        # Tenant-specific override'lar
      ├── Tenant2Prompts.php          # ixtif.com (shop odaklı)
      ├── Tenant3Prompts.php          # Örnek başka tenant
      └── ...                         # Gelecekte eklenecek tenant'lar
  ```

- [ ] **TenantPromptLoader Servisi:** `Modules/Blog/app/Services/TenantPrompts/TenantPromptLoader.php`
  - **Metod:** `getDraftPrompt(): string`
    - Tenant ID'yi al: `tenant('id')`
    - Tenant-specific prompt dosyası var mı kontrol et
    - Varsa: Tenant-specific prompt kullan
    - Yoksa: Default prompt kullan
  - **Metod:** `getBlogContentPrompt(): string`
    - Aynı mantık, blog içeriği için
  - **Metod:** `getTenantContext(): array`
    - Tenant'a özel ayarları döndür
    - Örnek: `['modules' => ['shop', 'references'], 'categories' => [...]]`

- [ ] **DefaultPrompts Servisi:** `Modules/Blog/app/Services/TenantPrompts/DefaultPrompts.php`
  - **Metod:** `getDraftPrompt(): string`
    - Genel taslak prompt'u
    - Sektör/kategori bilgisi yok
  - **Metod:** `getBlogContentPrompt(): string`
    - Genel blog içerik prompt'u
  - **Metod:** `getContext(): array`
    - Default context: Setting Group 6, 10

- [ ] **Tenant2Prompts (ixtif.com):** `Modules/Blog/app/Services/TenantPrompts/Tenants/Tenant2Prompts.php`
  - **extends DefaultPrompts**
  - **Override:** `getDraftPrompt(): string`
    ```php
    // Shop modülü odaklı
    // Kategoriler: Forklift, Transpalet, Akülü İstif Makinesi
    // Referanslar: Müşteri projeleri
    // Hizmetler: Bakım, Kiralama
    ```
  - **Override:** `getContext(): array`
    ```php
    return [
        'modules' => ['shop', 'references', 'services'],
        'shop_categories' => ShopCategory::all(),
        'site_settings' => [6, 10],
        'focus' => 'industrial_equipment',
        'keywords' => ['forklift', 'transpalet', 'istif makinesi']
    ];
    ```

- [ ] **Dinamik Yükleme Sistemi:**
  - TenantPromptLoader, tenant ID'ye göre `Tenants/Tenant{$id}Prompts.php` dosyasını kontrol eder
  - Dosya varsa: `app()->make("Modules\\Blog\\App\\Services\\TenantPrompts\\Tenants\\Tenant{$id}Prompts")`
  - Dosya yoksa: `app()->make(DefaultPrompts::class)`

**Kullanım Örneği:**
```php
// BlogAIDraftGenerator içinde
$promptLoader = app(TenantPromptLoader::class);
$prompt = $promptLoader->getDraftPrompt();
$context = $promptLoader->getTenantContext();

// OpenAI'a gönder
$response = OpenAI::chat()->create([
    'messages' => [
        ['role' => 'system', 'content' => $prompt],
        ['role' => 'user', 'content' => json_encode($context)]
    ]
]);
```

**Avantajlar:**
- ✅ Tenant 2 (ixtif): Shop, kategoriler, ürünler odaklı blog üretir
- ✅ Tenant 3: Farklı sektör, farklı prompt
- ✅ Yeni tenant: Dosya yoksa default kullanır, sorun çıkmaz
- ✅ Kolayca özelleştirilebilir, kod değişikliği gerektirmez

---

### 2.1 BlogAIDraftGenerator Servisi

- [ ] **Dosya:** `Modules/Blog/app/Services/BlogAIDraftGenerator.php`
- [ ] **TenantPromptLoader Entegrasyonu:**
  - Constructor'da inject et: `public function __construct(protected TenantPromptLoader $promptLoader)`
  - Prompt'u dinamik al: `$prompt = $this->promptLoader->getDraftPrompt()`
  - Context'i dinamik al: `$context = $this->promptLoader->getTenantContext()`
- [ ] **Metod:** `generateDrafts(int $count = 100): array`
  - **Duplicate Check:** Mevcut blog başlıklarını çek
    ```php
    $existingTitles = Blog::pluck('titles')->flatten()->toArray();
    $existingDrafts = BlogAIDraft::pluck('topic_keyword')->toArray();
    ```
  - OpenAI API call (gpt-4-turbo)
  - Prompt: **Artık TenantPromptLoader'dan gelecek (yukarıdaki gibi)**
    ```
    Generate {$count} blog post topics for an industrial equipment website (forklifts, pallet trucks).

    For each topic:
    1. Main keyword
    2. Suggested categories (IDs from available categories)
    3. SEO keywords (5-10 keywords)
    4. Blog outline (H2, H3 headings structure)
    5. Meta description (150-160 chars)

    Available categories: {json_encode($categories)}
    Existing titles to avoid: {json_encode($existingTitles)}
    Site context: {getSiteSettings(6, 10)}

    Output JSON array format:
    [
      {
        "topic_keyword": "Elektrikli Forklift Bakımı",
        "category_suggestions": [1, 5],
        "seo_keywords": ["elektrikli forklift", "bakım", "periyodik kontrol"],
        "outline": {
          "h2": ["Elektrikli Forklift Nedir?", "Bakım Önemi"],
          "h3": ["Günlük Kontroller", "Aylık Bakım"]
        },
        "meta_description": "Elektrikli forklift bakımı nasıl yapılır?..."
      }
    ]
    ```
  - Credit check: `ai_can_use_credits(1.0)` → Araştırma toplam 1 kredi
  - Credit usage: `ai_use_credits(1.0, 'blog_draft_generation')`
  - Save to DB: `BlogAIDraft::insert($drafts)`

### 2.2 BlogAIContentWriter Servisi

- [ ] **Dosya:** `Modules/Blog/app/Services/BlogAIContentWriter.php`
- [ ] **TenantPromptLoader Entegrasyonu:**
  - Constructor'da inject et: `public function __construct(protected TenantPromptLoader $promptLoader)`
  - Prompt'u dinamik al: `$prompt = $this->promptLoader->getBlogContentPrompt()`
  - Context'i dinamik al: `$context = $this->promptLoader->getTenantContext()`
- [ ] **Metod:** `generateBlogFromDraft(BlogAIDraft $draft): Blog`
  - OpenAI API call (gpt-4-turbo)
  - Prompt: **Artık TenantPromptLoader'dan gelecek**
    ```
    Write a complete blog post based on this outline:

    Topic: {$draft->topic_keyword}
    Outline: {json_encode($draft->outline)}
    SEO Keywords: {implode(', ', $draft->seo_keywords)}

    Requirements:
    - 1500-2000 words
    - Use outline headings as H2/H3
    - Natural keyword integration
    - Engaging introduction
    - Actionable conclusion
    - Use site context: {getSiteSettings(6, 10)}

    Output JSON:
    {
      "title": "Main title",
      "content": "Full HTML content with <h2>, <h3>, <p> tags",
      "excerpt": "Short summary (200 chars)"
    }
    ```
  - Credit check: `ai_can_use_credits(1.0)`
  - Credit usage: `ai_use_credits(1.0, 'blog_content_generation')`
  - Create Blog:
    ```php
    $blog = Blog::create([
        'titles' => ['tr' => $aiResponse['title']],
        'contents' => ['tr' => $aiResponse['content']],
        'excerpts' => ['tr' => $aiResponse['excerpt']],
        'status' => 'draft',  // Admin onayına sunulacak
    ]);
    ```
  - Attach categories: `$blog->categories()->attach($draft->category_suggestions)`
  - Create SEO:
    ```php
    $blog->seoSetting()->create([
        'titles' => ['tr' => $aiResponse['title']],
        'descriptions' => ['tr' => $draft->meta_description],
        'keywords' => $draft->seo_keywords,
        'status' => 'active'
    ]);
    ```
  - Update draft: `$draft->update(['is_generated' => true, 'generated_blog_id' => $blog->id])`
  - Return: `$blog`

- [ ] **Metod:** `generateFeaturedImage(Blog $blog): void`
  - DALL-E API call (optional, kredi yeterli ise)
  - Image prompt based on blog title
  - Download image
  - Attach to blog: `$blog->addMediaFromUrl($imageUrl)->toMediaCollection('featured_image')`

### 2.3 Batch Processing Helper

- [ ] **Dosya:** `Modules/Blog/app/Services/BlogAIBatchProcessor.php`
- [ ] **Metod:** `procesSelectedDrafts(array $draftIds): void`
  - Loop through selected drafts
  - Dispatch job for each: `GenerateBlogFromDraftJob::dispatch($draft)`
  - Track progress: Update session or cache
- [ ] **Metod:** `getBatchStatus(): array`
  - Return: ['total' => 10, 'completed' => 3, 'failed' => 0]

---

## 🚀 PHASE 3: QUEUE JOBS

### 3.1 GenerateDraftsJob

- [ ] **Dosya:** `Modules/Blog/app/Jobs/GenerateDraftsJob.php`
- [ ] **Queue:** `blog-ai`
- [ ] **Implements:** `ShouldQueue`
- [ ] **Constructor:**
  ```php
  public function __construct(public int $count = 100) {}
  ```
- [ ] **handle():**
  ```php
  public function handle(BlogAIDraftGenerator $generator)
  {
      try {
          $drafts = $generator->generateDrafts($this->count);
          // Job completed event (optional)
      } catch (\Exception $e) {
          Log::error('Blog AI Draft Generation Failed', ['error' => $e->getMessage()]);
          throw $e;  // Retry job
      }
  }
  ```

### 3.2 GenerateBlogFromDraftJob

- [ ] **Dosya:** `Modules/Blog/app/Jobs/GenerateBlogFromDraftJob.php`
- [ ] **Queue:** `blog-ai`
- [ ] **Implements:** `ShouldQueue`
- [ ] **Traits:** `InteractsWithQueue, Queueable, SerializesModels`
- [ ] **Properties:**
  ```php
  public $tries = 3;        // 3 deneme hakkı
  public $timeout = 300;    // 5 dakika timeout
  public $backoff = 60;     // 60 saniye bekle retry'da
  ```
- [ ] **Constructor:**
  ```php
  public function __construct(public BlogAIDraft $draft) {}
  ```
- [ ] **handle():**
  ```php
  public function handle(BlogAIContentWriter $writer)
  {
      try {
          $blog = $writer->generateBlogFromDraft($this->draft);
          // Optional: Generate featured image
          // $writer->generateFeaturedImage($blog);
      } catch (\Exception $e) {
          Log::error('Blog AI Content Generation Failed', [
              'draft_id' => $this->draft->id,
              'error' => $e->getMessage()
          ]);
          throw $e;  // Retry job
      }
  }
  ```

---

## 🎨 PHASE 4: LIVEWIRE COMPONENTS

### 4.1 BlogAIDraftComponent

- [ ] **Dosya:** `Modules/Blog/app/Http/Livewire/Admin/BlogAIDraftComponent.php`
- [ ] **Namespace:** `Modules\Blog\App\Http\Livewire\Admin`
- [ ] **Class:** `BlogAIDraftComponent extends Component`
- [ ] **Properties:**
  ```php
  public int $draftCount = 100;
  public array $selectedDrafts = [];
  public bool $isGenerating = false;
  public bool $isWriting = false;
  public array $batchProgress = [];  // ['total' => 10, 'completed' => 3]
  public $listeners = ['refreshComponent' => '$refresh'];
  ```
- [ ] **Methods:**
  - `generateDrafts()`: Dispatch GenerateDraftsJob, set isGenerating = true
  - `toggleDraftSelection($draftId)`: Add/remove from selectedDrafts array
  - `generateBlogs()`: Dispatch GenerateBlogFromDraftJob for each selected, set isWriting = true
  - `deleteDraft($draftId)`: Delete draft
  - `render()`: Return view with drafts list

- [ ] **Livewire Events:**
  - Listen: `draftGenerationComplete` → Refresh drafts, set isGenerating = false
  - Listen: `blogGenerationComplete` → Refresh drafts, set isWriting = false

### 4.2 View: Draft Selection UI

- [ ] **Dosya:** `Modules/Blog/resources/views/admin/livewire/blog-ai-draft-component.blade.php`
- [ ] **Layout:** Tabler.io admin layout
- [ ] **Sections:**
  1. **Header:**
     - Title: "🤖 AI Blog Taslak Üretici"
     - Credit balance display
     - "Taslak Üret" button (wire:click="generateDrafts")
  2. **Draft Count Input:**
     - Input: wire:model="draftCount" (default: 100)
     - Cost preview: "Maliyet: 1.0 kredi (araştırma toplam)"
  3. **Drafts Table:**
     - Columns: [Checkbox, Anahtar Kelime, Kategoriler, SEO Keywords, Durum]
     - Checkbox: wire:model="selectedDrafts" (multiple selection)
     - Status badges: Selected, Generated (with blog link)
  4. **Bulk Actions:**
     - "Seçili Blogları Yaz" button (wire:click="generateBlogs")
     - Cost preview: "{{ count($selectedDrafts) }} blog × 1 kredi = {{ count($selectedDrafts) }} kredi"
     - Delete selected button
  5. **Progress Indicator:**
     - Loading spinner when isGenerating or isWriting
     - "Taslaklar oluşturuluyor..." / "Bloglar yazılıyor..."
     - Progress bar: `{{ $batchProgress['completed'] }} / {{ $batchProgress['total'] }}`
     - Real-time update: `wire:poll.3s="checkBatchProgress"`
  6. **Error Handling:**
     - Failed drafts section
     - Retry button for failed items
     - Error message display

---

## 🔗 PHASE 5: ROUTES & NAVIGATION

### 5.1 Routes

- [ ] **Dosya:** `Modules/Blog/routes/web.php`
- [ ] **Admin Route Ekle:**
  ```php
  Route::middleware(['auth', 'admin'])->prefix('admin/blog')->name('admin.blog.')->group(function () {
      Route::get('/ai-drafts', BlogAIDraftComponent::class)->name('ai-drafts');
  });
  ```

### 5.2 Navigation Link

- [ ] **Dosya:** `Modules/Blog/app/Http/Livewire/Admin/BlogComponent.php`
- [ ] **getAllTabs() metoduna ekle:**
  ```php
  'ai-drafts' => [
      'title' => __('blog::admin.ai_drafts'),
      'icon' => 'ti ti-robot',
      'route' => route('admin.blog.ai-drafts'),
      'permission' => 'blog.manage',
  ]
  ```

- [ ] **Lang dosyası:** `Modules/Blog/lang/tr/admin.php`
  ```php
  'ai_drafts' => 'AI Taslaklar',
  ```

---

## ✅ PHASE 6: TESTING & DEPLOYMENT

### 6.0 Settings Kontrolü

- [ ] **Admin Panel Ayarları Kontrol:**
  - `/admin/settingmanagement/values/18` sayfasını aç
  - `blog_ai_enabled` = true olmalı
  - `blog_ai_daily_count` = 10 (test için)
  - `blog_ai_topic_source` = mixed
  - `blog_ai_manual_topics` içinde konular olmalı
  - `blog_ai_auto_publish` = false (önce draft olsun)
  - `blog_ai_professional_only` = false (tüm stiller)

### 6.1 Manuel Test

- [ ] **Taslak Üretme:**
  - Admin panel'e giriş yap
  - Blog → AI Taslaklar sayfasına git
  - 10 taslak üret (test için küçük sayı)
  - Kredi düşüşünü kontrol et (0.1 kredi)
  - Taslakların veritabanına kaydedildiğini doğrula

- [ ] **Taslak Seçimi:**
  - 3-5 taslak seç (checkbox)
  - "Seçili Blogları Yaz" butonuna tıkla
  - Queue job dispatch kontrolü
  - Job çalışmasını bekle (queue:work varsa)

- [ ] **Blog Oluşturma:**
  - Blog'ların oluşturulduğunu kontrol et
  - SEO ayarlarının eklendiğini doğrula (seo_settings tablosu)
  - Kategori ilişkilerini kontrol et
  - Slug oluşumunu doğrula
  - Draft status'ü kontrol et

- [ ] **Frontend Kontrol:**
  - Blog detay sayfasını aç
  - Schema.org markup kontrolü (view-source, JSON-LD)
  - Sitemap'e eklendiğini doğrula (/sitemap.xml)
  - SEO title/description kontrolü (head tag'leri)

### 6.2 Console Test

- [ ] **Tinker Test:**
  ```php
  // Credit kontrol
  ai_can_use_credits(10.0);

  // Draft oluşturma
  $drafts = app(\Modules\Blog\App\Services\BlogAIDraftGenerator::class)->generateDrafts(5);

  // Blog yazma
  $draft = \Modules\Blog\App\Models\BlogAIDraft::first();
  $blog = app(\Modules\Blog\App\Services\BlogAIContentWriter::class)->generateBlogFromDraft($draft);

  // SEO kontrolü
  $blog->seoSetting;
  $blog->getFallbackSeoTitle();
  ```

### 6.3 Cache Clear & Build

- [ ] **Frontend Compile:**
  ```bash
  php artisan view:clear
  php artisan responsecache:clear
  npm run prod
  echo "✅ Cache temizlendi, build tamamlandı!"
  ```

- [ ] **OPcache Reset:**
  ```bash
  curl -s -k https://ixtif.com/opcache-reset.php
  ```

---

## 📦 PHASE 7: FINAL COMMIT

- [ ] **Git Add:**
  ```bash
  git add .
  ```

- [ ] **Git Commit:**
  ```bash
  git commit -m "$(cat <<'EOF'
  ✨ BLOG AI SİSTEMİ - Tam Otomasyonlu Blog Üretimi

  🎯 Özellikler:
  - AI ile 100 blog taslağı üretme (0.01 kredi/taslak)
  - Admin tarafından taslak seçimi (checkbox)
  - Seçili taslakları tam blog yazıya dönüştürme (1 kredi/blog)
  - Otomatik SEO ayarları (seo_settings polymorphic)
  - Otomatik kategori eşleştirme
  - Otomatik Schema.org markup
  - Otomatik sitemap güncelleme

  📊 Migrations:
  - blog_ai_drafts tablosu (tenant database)

  🛠️ Services:
  - BlogAIDraftGenerator (OpenAI GPT-4)
  - BlogAIContentWriter (OpenAI GPT-4 + SEO)

  🚀 Jobs:
  - GenerateDraftsJob (queue: blog-ai)
  - GenerateBlogFromDraftJob (queue: blog-ai)

  🎨 Livewire:
  - BlogAIDraftComponent (draft selection UI)

  💰 Maliyet:
  - Araştırma (100 taslak): 1.0 kredi (toplam)
  - 10 blog: 10.0 kredi (1.0 × 10)
  - Toplam: 11.0 kredi

  🤖 Generated with Claude Code

  Co-Authored-By: Claude <noreply@anthropic.com>
  EOF
  )"
  ```

- [ ] **Git Push:**
  ```bash
  git push origin main
  ```

---

## 📝 NOTLAR

### Mevcut Sistemler (Kullanılacak):
- ✅ `HasSeo` trait → seo_settings polymorphic relationship
- ✅ `HasMediaManagement` trait → media tablosu (Spatie)
- ✅ `SEOService::getArticleSchema()` → Schema.org markup
- ✅ `TenantSitemapService::addBlogContent()` → Sitemap auto-update
- ✅ `ai_use_credits()` / `ai_can_use_credits()` → Credit helpers

### Multi-Tenant Uyarılar:
- ⚠️ `blog_ai_drafts` tablosu **TENANT database'inde**
- ⚠️ Migration **hem central hem tenant** klasörlerinde olmalı
- ⚠️ Credit işlemleri **CENTRAL database'den** yönetiliyor

### OpenAI API:
- Model: `gpt-4-turbo` (daha hızlı, daha ucuz)
- Temperature: 0.7 (yaratıcı ama kontrollü)
- Max tokens: 3000 (draft için), 8000 (blog için)
- **Config dosyası:** `config/modules/blog.php`
  ```php
  'openai' => [
      'api_key' => env('OPENAI_API_KEY'),
      'model' => 'gpt-4-turbo-preview',
      'draft_temperature' => 0.7,
      'blog_temperature' => 0.8,
      'draft_max_tokens' => 3000,
      'blog_max_tokens' => 8000
  ]
  ```

### Queue Sistemi:
- Queue name: `blog-ai`
- Worker: `php artisan queue:work --queue=blog-ai`
- Retry: 3 attempts
- Timeout: 300 seconds

---

## ✅ İLERLEME TAKIBI

**Son Güncelleme:** 2025-01-14 04:45

**Tamamlanan:** 3/20 aşama (Hazırlık tamamlandı)
**Kalan:** 17/20 aşama
**İlerleme:** %15

**Sıradaki Adım:** Tenant-Specific Prompt Customization sistemi (PHASE 2.0)

**Yeni Eklenen Özellikler:**
- ✅ Tenant-specific prompt customization sistemi eklendi
- ✅ Dinamik prompt yükleme mekanizması
- ✅ Tenant 2 (ixtif.com) için shop odaklı özel prompt yapısı
- ✅ Default fallback sistemi (yeni tenant'lar için)
