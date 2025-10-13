# 🤖 SHOP MODÜLÜ TAMAMLAMA PROMPTU

**Tarih**: 2025-10-09
**Hedef**: Shop modülünün eksik kısımlarını Portfolio pattern'ini BİREBİR takip ederek tamamla

---

## 📋 PROJE BİLGİLERİ

**Proje Root**: `/Users/nurullah/Desktop/cms/laravel/`
**Pattern Master**: Portfolio Modülü (`Modules/Portfolio/`)
**Hedef Modül**: Shop Modülü (`Modules/Shop/`)
**PHP Version**: 8.1+
**Framework**: Laravel 10+ (Multi-tenant)

---

## ⚠️ KRİTİK KURALLAR (BAŞLAMADAN ÖNCELİKLE OKU!)

### 🚫 CONTROLLER SİLME TALİMATI

**UYARI**: Admin Controller dosyaları KALDIRILACAK, sadece Livewire kullanılacak!

```bash
❌ SİLİNECEK DOSYALAR (Controller-based Admin yok):
/Users/nurullah/Desktop/cms/laravel/Modules/Shop/app/Http/Controllers/Admin/
  - ShopProductController.php (VARSA SİL)
  - ShopCategoryController.php (VARSA SİL)
  - ShopBrandController.php (VARSA SİL)

✅ FRONTEND CONTROLLER KALACAK:
/Users/nurullah/Desktop/cms/laravel/Modules/Shop/app/Http/Controllers/Front/ShopController.php

✅ API CONTROLLER KALACAK (opsiyonel):
/Users/nurullah/Desktop/cms/laravel/Modules/Shop/app/Http/Controllers/Api/ShopApiController.php
```

**Neden?**
- Admin paneli %100 **Livewire Component** tabanlı (Portfolio gibi)
- Controller + Blade view pattern YOK
- Tüm CRUD işlemleri Livewire ile

---

### 🎨 UI/UX PATTERN KURALLARI

#### 1. SORTABLE SİSTEMİ (Manuel Değil!)

**Pattern Referansı**:
```
/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/app/Http/Livewire/Admin/PortfolioCategoryComponent.php
  - Line 336-399: updateOrder() method
  - SortableJS drag-drop kullanımı
```

**Blade View Referansı**:
```
/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/resources/views/admin/livewire/category-component.blade.php
  - Line 138-242: Sortable list yapısı
  - Line 258-259: SortableJS kütüphanesi
```

**Kurallar**:
```php
❌ YANLIŞ: Manuel sort_order input field'ı
❌ YANLIŞ: Up/Down arrow butonları
❌ YANLIŞ: Manage sayfasında sıralama

✅ DOĞRU: SortableJS drag-drop
✅ DOĞRU: Liste sayfasında (Component.php) sıralama
✅ DOĞRU: updateOrder() method ile otomatik kayıt
```

**SortableJS Assets**:
```html
<!-- CSS -->
<link rel="stylesheet" href="{{ asset('admin-assets/css/category-sortable.css') }}">

<!-- JavaScript -->
<script src="{{ asset('admin-assets/libs/sortable/sortable.min.js') }}"></script>
<script src="{{ asset('admin-assets/js/category-sortable.js') }}"></script>
```

#### 2. SWITCH BUTONLAR (Toggle Durumu)

**Pattern Referansı**:
```
/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/resources/views/admin/livewire/category-component.blade.php
  - Line 183-201: Toggle button (is_active)
```

**Doğru Yapı**:
```html
<!-- Active/Inactive Toggle -->
<button wire:click="toggleCategoryStatus({{ $item->category_id }})"
    class="btn btn-icon btn-sm {{ $item->is_active ? 'text-muted bg-transparent' : 'text-red bg-transparent' }}"
    data-bs-toggle="tooltip" data-bs-placement="top"
    title="{{ $item->is_active ? __('admin.deactivate') : __('admin.activate') }}">

    <div wire:loading wire:target="toggleCategoryStatus({{ $item->category_id }})"
        class="spinner-border spinner-border-sm">
    </div>

    <div wire:loading.remove wire:target="toggleCategoryStatus({{ $item->category_id }})">
        @if($item->is_active)
        <i class="fas fa-check fa-lg"></i>
        @else
        <i class="fas fa-times fa-lg"></i>
        @endif
    </div>
</button>
```

**Kurallar**:
```php
❌ YANLIŞ: Checkbox toggle (form içinde)
❌ YANLIŞ: Pretty checkbox (manage sayfasında kullan, liste için DEĞİL)

✅ DOĞRU: Button + icon (check/times)
✅ DOĞRU: Loading state (wire:loading)
✅ DOĞRU: Tooltip (activate/deactivate)
✅ DOĞRU: Color: text-muted (active), text-red (inactive)
```

#### 3. FORM TAB SİSTEMİ

**Pattern Referansı**:
```
/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/app/Http/Livewire/Admin/PortfolioCategoryComponent.php
  - Line 67-69: Tab configuration
  - Line 21-23: Tab completion status
```

**Manage Component Tab Yapısı** (ÖRNEKLERİ İNCELE!):
```
/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/app/Http/Livewire/Admin/PortfolioManageComponent.php
  - Multi-tab: Genel Bilgiler, Medya, SEO
  - Tab completion tracking (GlobalTabService)
  - Language switcher her tab içinde
```

**Kurallar**:
```php
❌ YANLIŞ: Manuel tab yapısı
❌ YANLIŞ: Kafadan tab ekleme

✅ DOĞRU: x-tab-system component kullan
✅ DOĞRU: GlobalTabService ile completion tracking
✅ DOĞRU: Portfolio ManageComponent tab yapısını BİREBİR kopyala
```

#### 4. PRETTY CHECKBOX (Form Toggle)

**Pattern Referansı**:
```
/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/resources/views/admin/livewire/category-component.blade.php
  - Line 68-81: Pretty checkbox (is_active form toggle)
```

**Doğru Kullanım**:
```html
<!-- Aktif Durumu - Form içinde -->
<div class="mb-3">
    <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
        <input type="checkbox" id="is_active" name="is_active" wire:model="is_active"
            value="1"
            {{ !isset($is_active) || $is_active ? 'checked' : '' }} />

        <div class="state p-success p-on ms-2">
            <label>{{ __('portfolio::admin.active') }}</label>
        </div>
        <div class="state p-danger p-off ms-2">
            <label>{{ __('portfolio::admin.inactive') }}</label>
        </div>
    </div>
</div>
```

**Nerede Kullanılır**:
```php
✅ DOĞRU: Form içinde (Create/Edit)
✅ DOĞRU: Manage sayfalarında checkbox toggle için
✅ DOĞRU: Category form (sol taraf)

❌ YANLIŞ: Liste görünümünde toggle için (button kullan!)
```

#### 5. TASARIM STANDARTLARI

**Tabler.io + Bootstrap Pattern**:
```
Referans Dosyalar:
/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/resources/views/admin/livewire/
  - category-component.blade.php (İki sütunlu layout)
  - portfolio-component.blade.php (Tablo layout)
  - portfolio-manage-component.blade.php (Multi-tab form)
```

**Layout Kuralları**:
```php
✅ İki Sütunlu Layout (Category için):
   - Sol: Form (col-lg-5)
   - Sağ: Liste (col-lg-7)

✅ Tablo Layout (Product için):
   - Header: Search + Filters
   - Table: Responsive + Sortable columns
   - Pagination: Livewire pagination

✅ Manage Layout (Create/Edit için):
   - Multi-tab card
   - Language switcher (global)
   - Tab içinde language content
   - Sticky save button (floating)
```

---

## 📚 REFERANS DOSYALAR (OKUMADAN BAŞLAMA!)

### 1. Ana Analiz Raporu
```
/Users/nurullah/Desktop/cms/laravel/claudeguncel.md
```
**İçerik**: Detaylı eksiklik analizi, öneriler, eylem planı

### 2. Portfolio Pattern Referansları (MASTER PATTERN)

#### Models:
```
/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/app/Models/Portfolio.php
/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/app/Models/PortfolioCategory.php
```

#### Repositories:
```
/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/app/Repositories/PortfolioRepository.php
/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/app/Repositories/PortfolioCategoryRepository.php
/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/app/Contracts/PortfolioRepositoryInterface.php
/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/app/Contracts/PortfolioCategoryRepositoryInterface.php
```

#### Services:
```
/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/app/Services/PortfolioService.php
/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/app/Services/PortfolioCategoryService.php
```

#### Livewire Components (EN ÖNEMLİ!):
```
/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/app/Http/Livewire/Admin/PortfolioComponent.php
/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/app/Http/Livewire/Admin/PortfolioManageComponent.php
/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/app/Http/Livewire/Admin/PortfolioCategoryComponent.php
/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/app/Http/Livewire/Admin/PortfolioCategoryManageComponent.php
```

#### Blade Views (UI PATTERN):
```
/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/resources/views/admin/livewire/category-component.blade.php
/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/resources/views/admin/livewire/portfolio-component.blade.php
/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/resources/views/admin/livewire/portfolio-manage-component.blade.php
```

#### Helper Views (Admin Menü):
```
/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/resources/views/admin/helper.blade.php
/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/resources/views/admin/helper-category.blade.php
```

#### Migrations (Pattern):
```
/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/Database/migrations/2024_02_17_000000_create_portfolio_categories_table.php
/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/Database/migrations/2024_02_17_000001_create_portfolios_table.php
```

### 3. E-Commerce Dökümanları

#### Migration Planları (KULLANILACAK!):
```
/Users/nurullah/Desktop/cms/laravel/readme/ecommerce/migrations/phase-1/
  - 26 adet migration dosyası (Portfolio pattern'li)
  - Field isimleri, veri tipleri AYNEN kullanılacak
```

#### TODO Listesi:
```
/Users/nurullah/Desktop/cms/laravel/readme/ecommerce/TODO.md
```

#### README:
```
/Users/nurullah/Desktop/cms/laravel/readme/ecommerce/README.md
```

### 4. Mevcut Shop Dosyaları

#### Models (İyileştirilecek):
```
/Users/nurullah/Desktop/cms/laravel/Modules/Shop/app/Models/ShopProduct.php
/Users/nurullah/Desktop/cms/laravel/Modules/Shop/app/Models/ShopCategory.php
/Users/nurullah/Desktop/cms/laravel/Modules/Shop/app/Models/ShopBrand.php
```

#### Repositories (İyileştirilecek):
```
/Users/nurullah/Desktop/cms/laravel/Modules/Shop/app/Repositories/ShopProductRepository.php
/Users/nurullah/Desktop/cms/laravel/Modules/Shop/app/Repositories/ShopCategoryRepository.php
/Users/nurullah/Desktop/cms/laravel/Modules/Shop/app/Repositories/ShopBrandRepository.php
```

---

## ✅ GÖREV LİSTESİ (SIRAYLA TAKİP ET!)

### PHASE 1: CORE MODELS (ÖNCELİK: 🔴 YÜKSEK)

#### 1.1 ShopProductVariant Modeli

**Hedef Dosya**: `/Users/nurullah/Desktop/cms/laravel/Modules/Shop/app/Models/ShopProductVariant.php`

**Migration Referansı**: `/Users/nurullah/Desktop/cms/laravel/readme/ecommerce/migrations/phase-1/004_create_shop_product_variants_table.php`

**Pattern Referansı**: `Portfolio.php` (aynı trait ve interface yapısı)

**Gereksinimler**:
```php
<?php

declare(strict_types=1);

namespace Modules\Shop\App\Models;

use App\Contracts\TranslatableEntity;
use App\Models\BaseModel;
use App\Traits\HasSeo;
use App\Traits\HasTranslations;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\MediaManagement\App\Traits\HasMediaManagement;
use Spatie\MediaLibrary\HasMedia;

class ShopProductVariant extends BaseModel implements TranslatableEntity, HasMedia
{
    use Sluggable;
    use HasTranslations;
    use HasSeo;
    use HasFactory;
    use HasMediaManagement;

    protected $primaryKey = 'variant_id';

    // Migration'daki field'ları AYNEN kullan
    protected $fillable = [
        'product_id',
        'sku',
        'title',
        'slug',
        'base_price',
        'compare_at_price',
        'cost_price',
        'stock_quantity',
        'low_stock_threshold',
        'weight',
        'dimensions',
        'is_active',
        'sort_order',
    ];

    // Migration'a göre casts
    protected $casts = [
        'title' => 'array',
        'slug' => 'array',
        'base_price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'weight' => 'decimal:2',
        'dimensions' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected array $translatable = ['title', 'slug'];

    // Portfolio pattern: ID accessor
    public function getIdAttribute(): int
    {
        return (int) $this->variant_id;
    }

    // Portfolio pattern: Sluggable (devre dışı - JSON için)
    public function sluggable(): array
    {
        return [];
    }

    // Portfolio pattern: Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    // Relations
    public function product(): BelongsTo
    {
        return $this->belongsTo(ShopProduct::class, 'product_id', 'product_id');
    }

    // TranslatableEntity interface
    public function getTranslatableFields(): array
    {
        return [
            'title' => 'text',
            'slug' => 'auto',
        ];
    }

    public function hasSeoSettings(): bool
    {
        return false; // Varyantlar için SEO gerekli değil
    }

    public function afterTranslation(string $targetLanguage, array $translatedData): void
    {
        \Log::info('Shop product variant translation completed', [
            'variant_id' => $this->variant_id,
            'target_language' => $targetLanguage,
        ]);
    }

    public function getPrimaryKeyName(): string
    {
        return 'variant_id';
    }

    // SEO Fallbacks (boş - varyant için SEO yok)
    protected function getSeoFallbackTitle(): ?string { return null; }
    protected function getSeoFallbackDescription(): ?string { return null; }
    protected function getSeoFallbackKeywords(): array { return []; }
    protected function getSeoFallbackCanonicalUrl(): ?string { return null; }
    protected function getSeoFallbackImage(): ?string { return null; }
    protected function getSeoFallbackSchemaMarkup(): ?array { return null; }

    // Media Config (Portfolio pattern)
    protected function getMediaConfig(): array
    {
        return [
            'variant_image' => [
                'type' => 'image',
                'single_file' => true,
                'max_items' => 1,
                'max_size' => config('modules.media.max_file_size', 10240),
                'conversions' => array_keys(config('modules.media.conversions', ['thumb', 'medium'])),
                'sortable' => false,
            ],
        ];
    }

    // Factory
    protected static function newFactory()
    {
        return \Modules\Shop\Database\Factories\ShopProductVariantFactory::new();
    }
}
```

**Ayrıca Oluşturulacak Dosyalar**:
```
/Users/nurullah/Desktop/cms/laravel/Modules/Shop/app/Contracts/ShopProductVariantRepositoryInterface.php
/Users/nurullah/Desktop/cms/laravel/Modules/Shop/app/Repositories/ShopProductVariantRepository.php
/Users/nurullah/Desktop/cms/laravel/Modules/Shop/app/Services/ShopProductVariantService.php
```
**Pattern**: Portfolio Repository/Service pattern'ini birebir takip et!

---

#### 1.2-1.9 Diğer Core Modeller

**Sırayla Oluştur**:
```
1.2 ShopAttribute
1.3 ShopCart
1.4 ShopCartItem
1.5 ShopOrder
1.6 ShopOrderItem
1.7 ShopOrderAddress
1.8 ShopPaymentMethod
1.9 ShopPayment
```

**Her biri için**:
- Migration referansını kullan (phase-1 klasöründen)
- Portfolio pattern'i takip et
- Repository + Service + Interface oluştur

---

### PHASE 2: ADMIN PANEL (ÖNCELİK: 🔴 YÜKSEK)

#### 2.1 ShopProductComponent (Livewire - Liste)

**Hedef Dosya**: `/Users/nurullah/Desktop/cms/laravel/Modules/Shop/app/Http/Livewire/Admin/ShopProductComponent.php`

**Pattern Referansı**: `/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/app/Http/Livewire/Admin/PortfolioComponent.php`

**ÖNEMLİ**: Portfolio'daki AYNI yapıyı kullan!

**Gereksinimler**:
```php
<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Livewire\Admin;

use Livewire\Attributes\{Layout, Computed, Url};
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Shop\App\Services\ShopProductService;
// ... diğer use statements (Portfolio'dan kopyala)

#[Layout('admin.layout')]
class ShopProductComponent extends Component
{
    use WithPagination;
    // WithBulkActions, InlineEditTitle traits ekle (Portfolio'daki gibi)

    // Properties (Portfolio pattern)
    public $search = '';
    public $perPage = 10;
    public $sortField = 'product_id';
    public $sortDirection = 'desc';

    // URL Parameters (Portfolio pattern)
    #[Url] public $category_id = null;
    #[Url] public $brand_id = null;
    #[Url] public $status = null;

    // ... Portfolio'daki diğer properties

    // Methods (Portfolio pattern - AYNEN KOPYALA)
    public function toggleActive(int $productId): void { ... }
    public function sortBy(string $field): void { ... }
    public function updatedSearch(): void { ... }
    public function bulkDelete(): void { ... }
    // ... diğer methods
}
```

**Blade View**: `/Users/nurullah/Desktop/cms/laravel/Modules/Shop/resources/views/admin/livewire/product-component.blade.php`

**Pattern**: `/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/resources/views/admin/livewire/portfolio-component.blade.php`

**UI Yapısı**:
```html
<!-- Header -->
<div class="card-header">
    <h3>Ürünler</h3>
    <div class="input-icon">
        <input type="text" wire:model.live.debounce.300ms="search" class="form-control">
    </div>
</div>

<!-- Table (Tabler.io pattern) -->
<table class="table table-vcenter table-mobile-md card-table">
    <thead>
        <tr>
            <th>
                <input type="checkbox" wire:model.live="selectAll">
            </th>
            <th wire:click="sortBy('product_id')" style="cursor: pointer;">
                ID
                @if($sortField === 'product_id')
                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                @endif
            </th>
            <!-- Diğer kolonlar -->
        </tr>
    </thead>
    <tbody>
        @foreach($products as $product)
            <tr>
                <!-- Checkbox -->
                <td>
                    <input type="checkbox" wire:model.live="selected" value="{{ $product->product_id }}">
                </td>
                <!-- Image -->
                <td>
                    @if($product->hasMedia('featured_image'))
                        <img src="{{ $product->getFirstMediaUrl('featured_image', 'thumb') }}" class="avatar">
                    @endif
                </td>
                <!-- Title -->
                <td>
                    {{ $product->getTranslated('title', app()->getLocale()) }}
                </td>
                <!-- Status Toggle (Portfolio pattern) -->
                <td>
                    <button wire:click="toggleActive({{ $product->product_id }})"
                        class="btn btn-icon btn-sm {{ $product->is_active ? 'text-muted' : 'text-red' }}">
                        <div wire:loading wire:target="toggleActive({{ $product->product_id }})">
                            <div class="spinner-border spinner-border-sm"></div>
                        </div>
                        <div wire:loading.remove wire:target="toggleActive({{ $product->product_id }})">
                            @if($product->is_active)
                                <i class="fas fa-check fa-lg"></i>
                            @else
                                <i class="fas fa-times fa-lg"></i>
                            @endif
                        </div>
                    </button>
                </td>
                <!-- Actions -->
                <td>
                    <a href="{{ route('admin.shop.product.manage', $product->product_id) }}">
                        <i class="fa-solid fa-pen-to-square link-secondary fa-lg"></i>
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Pagination -->
{{ $products->links() }}
```

---

#### 2.2 ShopProductManageComponent (Livewire - Form)

**Hedef Dosya**: `/Users/nurullah/Desktop/cms/laravel/Modules/Shop/app/Http/Livewire/Admin/ShopProductManageComponent.php`

**Pattern Referansı**: `/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/app/Http/Livewire/Admin/PortfolioManageComponent.php`

**ÇOK ÖNEMLİ**: Portfolio ManageComponent'i AYNEN KOPYALA, sadece model/field isimlerini değiştir!

**Tab Yapısı** (Portfolio pattern):
```php
// Tab Configuration
public $tabConfig = [
    ['name' => 'Genel Bilgiler', 'icon' => 'fas fa-info-circle'],
    ['name' => 'Fiyatlandırma', 'icon' => 'fas fa-dollar-sign'],
    ['name' => 'Stok', 'icon' => 'fas fa-boxes'],
    ['name' => 'Medya', 'icon' => 'fas fa-image'],
    ['name' => 'SEO', 'icon' => 'fas fa-search'],
];

// Tab Completion (GlobalTabService)
public $tabCompletionStatus = [];
```

**Blade View**: `/Users/nurullah/Desktop/cms/laravel/Modules/Shop/resources/views/admin/livewire/product-manage-component.blade.php`

**Pattern**: `/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/resources/views/admin/livewire/portfolio-manage-component.blade.php`

**UI Yapısı** (Portfolio pattern - AYNEN KOPYALA):
```html
<div class="card">
    <!-- Tab System -->
    <x-tab-system :tabs="$tabConfig" :tab-completion="$tabCompletionStatus" storage-key="product_active_tab">
        <x-manage.language.switcher :current-language="$currentLanguage" />
    </x-tab-system>

    <div class="card-body">
        <div class="tab-content">
            <!-- Tab 1: Genel Bilgiler -->
            <div class="tab-pane fade show active" id="0" role="tabpanel">
                @foreach ($availableLanguages as $lang)
                    <div class="language-content" data-language="{{ $lang }}"
                        style="display: {{ $currentLanguage === $lang ? 'block' : 'none' }};">

                        <!-- Title Input -->
                        <div class="form-floating mb-3">
                            <input type="text"
                                class="form-control @error('multiLangInputs.' . $lang . '.title') is-invalid @enderror"
                                wire:model="multiLangInputs.{{ $lang }}.title"
                                placeholder="Ürün Adı">
                            <label>
                                Ürün Adı
                                @if ($lang === session('site_default_language', 'tr'))
                                    <span class="required-star">★</span>
                                @endif
                            </label>
                        </div>

                        <!-- TinyMCE Editor -->
                        <div wire:ignore>
                            <textarea id="editor_{{ $lang }}"
                                      class="tinymce-editor"
                                      wire:model="multiLangInputs.{{ $lang }}.body"></textarea>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Tab 2: Fiyatlandırma -->
            <div class="tab-pane fade" id="1" role="tabpanel">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input type="number" step="0.01" class="form-control" wire:model="inputs.base_price">
                            <label>Temel Fiyat (₺)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input type="number" step="0.01" class="form-control" wire:model="inputs.compare_at_price">
                            <label>İndirim Öncesi Fiyat (₺)</label>
                        </div>
                    </div>
                    <!-- Diğer fiyat alanları -->
                </div>
            </div>

            <!-- Tab 3: Stok -->
            <div class="tab-pane fade" id="2" role="tabpanel">
                <!-- Stok alanları -->
            </div>

            <!-- Tab 4: Medya -->
            <div class="tab-pane fade" id="3" role="tabpanel">
                <!-- Filepond media upload (Portfolio pattern) -->
            </div>

            <!-- Tab 5: SEO -->
            <div class="tab-pane fade" id="4" role="tabpanel">
                <!-- Universal SEO component -->
                <livewire:seo-management.admin.seo-form-component
                    :model-type="'Modules\Shop\App\Models\ShopProduct'"
                    :model-id="$productId"
                    :current-language="$currentLanguage" />
            </div>
        </div>
    </div>

    <!-- Floating Save Button (Portfolio pattern) -->
    <div class="card-footer">
        <div class="d-flex justify-content-end">
            <button type="button" wire:click="save" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>
                Kaydet
            </button>
        </div>
    </div>
</div>
```

---

#### 2.3 ShopCategoryComponent (Livewire - Kategori)

**Hedef Dosya**: `/Users/nurullah/Desktop/cms/laravel/Modules/Shop/app/Http/Livewire/Admin/ShopCategoryComponent.php`

**Pattern Referansı**: `/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/app/Http/Livewire/Admin/PortfolioCategoryComponent.php`

**ÇOK ÖNEMLİ**: PortfolioCategoryComponent'i TAMAMEN KOPYALA!

**Özellikler**:
- İki sütunlu layout (Sol: Form, Sağ: Liste)
- SortableJS drag-drop (Line 336-399)
- Hierarchical category list (buildHierarchicalList method)
- Toggle status button (Line 312-334)
- updateOrder method (drag-drop kayıt)

**Blade View**: `/Users/nurullah/Desktop/cms/laravel/Modules/Shop/resources/views/admin/livewire/category-component.blade.php`

**Pattern**: `/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/resources/views/admin/livewire/category-component.blade.php`

**AYNEN KOPYALA** (sadece portfolio → shop değiştir):
```html
<!-- İki Sütunlu Layout -->
<div class="row">
    <!-- Sol: Form -->
    <div class="col-lg-5 col-md-12 mb-3">
        <form wire:submit.prevent="addCategory">
            <!-- Form fields (Portfolio pattern) -->
        </form>
    </div>

    <!-- Sağ: Kategori Listesi -->
    <div class="col-lg-7 col-md-12">
        <div class="card">
            <!-- Header: Search -->
            <div class="row mb-3">
                <div class="col">
                    <h3>Kategoriler</h3>
                </div>
                <div class="col">
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control">
                </div>
            </div>

            <!-- Sortable List -->
            <div class="list-group list-group-flush" id="category-sortable-list">
                @foreach($categories as $item)
                    <div class="category-item list-group-item p-2"
                        style="padding-left: {{ 8 + ($item->depth_level * 30) }}px !important;"
                        wire:key="category-{{ $item->category_id }}"
                        data-id="{{ $item->category_id }}"
                        data-depth="{{ $item->depth_level }}"
                        @if($item->parent_id) data-parent-id="{{ $item->parent_id }}" @endif>

                        <div class="d-flex align-items-center">
                            <!-- Drag Handle -->
                            <div class="category-drag-handle me-2">
                                <i class="fas fa-grip-vertical text-muted"></i>
                            </div>

                            <!-- Icon -->
                            <div class="rounded-2 d-flex align-items-center justify-content-center me-2">
                                <i class="fas fa-folder"></i>
                            </div>

                            <!-- Title -->
                            <div class="flex-grow-1">
                                {{ $item->getTranslated('title', app()->getLocale()) }}
                            </div>

                            <!-- Actions -->
                            <div class="d-flex align-items-center gap-3">
                                <!-- Toggle Status (Portfolio pattern) -->
                                <button wire:click="toggleCategoryStatus({{ $item->category_id }})"
                                    class="btn btn-icon btn-sm {{ $item->is_active ? 'text-muted' : 'text-red' }}">
                                    <div wire:loading wire:target="toggleCategoryStatus({{ $item->category_id }})">
                                        <div class="spinner-border spinner-border-sm"></div>
                                    </div>
                                    <div wire:loading.remove>
                                        @if($item->is_active)
                                            <i class="fas fa-check fa-lg"></i>
                                        @else
                                            <i class="fas fa-times fa-lg"></i>
                                        @endif
                                    </div>
                                </button>

                                <!-- Edit -->
                                <a href="{{ route('admin.shop.category.manage', $item->category_id) }}">
                                    <i class="fa-solid fa-pen-to-square link-secondary fa-lg"></i>
                                </a>

                                <!-- Delete Dropdown -->
                                <div class="dropdown">
                                    <a class="dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-bars-sort fa-lg"></i>
                                    </a>
                                    <div class="dropdown-menu">
                                        <a onclick="..." class="dropdown-item link-danger">
                                            <i class="fas fa-trash me-2"></i> Sil
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- SortableJS Scripts (Portfolio pattern) -->
@push('scripts')
<script src="{{ asset('admin-assets/libs/sortable/sortable.min.js') }}"></script>
<script src="{{ asset('admin-assets/js/category-sortable.js') }}"></script>
@endpush
```

---

#### 2.4 Admin Helper Views (Menü Butonları)

**Hedef Dosyalar**:
```
/Users/nurullah/Desktop/cms/laravel/Modules/Shop/resources/views/admin/helper-category.blade.php
/Users/nurullah/Desktop/cms/laravel/Modules/Shop/resources/views/admin/helper-product.blade.php
/Users/nurullah/Desktop/cms/laravel/Modules/Shop/resources/views/admin/helper-brand.blade.php
```

**Pattern Referansı**:
```
/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/resources/views/admin/helper.blade.php
/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/resources/views/admin/helper-category.blade.php
```

**helper-product.blade.php** (örnek):
```blade
@if(auth()->user()->can('shop.products.view') || auth()->user()->hasRole('super_admin'))
    <div class="col-md-6 col-lg-4 mb-3">
        <a href="{{ route('admin.shop.products') }}"
           class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-between p-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-shopping-cart fa-2x me-3"></i>
                <div class="text-start">
                    <div class="fw-bold">Ürünler</div>
                    <div class="text-muted small">Ürün yönetimi</div>
                </div>
            </div>
            <i class="fas fa-chevron-right"></i>
        </a>
    </div>
@endif
```

---

### PHASE 3: FRONTEND (ÖNCELİK: 🟡 ORTA)

#### 3.1 ShopController (Frontend)

**Hedef Dosya**: `/Users/nurullah/Desktop/cms/laravel/Modules/Shop/app/Http/Controllers/Front/ShopController.php`

**Pattern Referansı**: `/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/app/Http/Controllers/Front/PortfolioController.php`

**Methods** (Portfolio pattern):
```php
<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Modules\Shop\App\Services\ShopProductService;
use Modules\Shop\App\Services\ShopCategoryService;

class ShopController extends Controller
{
    public function __construct(
        private readonly ShopProductService $productService,
        private readonly ShopCategoryService $categoryService
    ) {}

    public function index()
    {
        // Ürün listesi
        $products = $this->productService->getPaginatedProducts([], 12);

        return view('shop::front.index', compact('products'));
    }

    public function show(string $slug)
    {
        // Ürün detay
        $locale = app()->getLocale();
        $product = $this->productService->getProductBySlug($slug, $locale);

        return view('shop::front.show', compact('product'));
    }

    public function category(string $slug)
    {
        // Kategori sayfası
        $locale = app()->getLocale();
        $category = $this->categoryService->getCategoryBySlug($slug, $locale);
        $products = $this->productService->getProductsByCategory($category->category_id, 12);

        return view('shop::front.category', compact('category', 'products'));
    }

    public function brand(string $slug)
    {
        // Marka sayfası
        // ...
    }
}
```

---

#### 3.2 Frontend Views

**Hedef Dosyalar**:
```
/Users/nurullah/Desktop/cms/laravel/Modules/Shop/resources/views/front/index.blade.php
/Users/nurullah/Desktop/cms/laravel/Modules/Shop/resources/views/front/show.blade.php
/Users/nurullah/Desktop/cms/laravel/Modules/Shop/resources/views/front/category.blade.php
```

**Pattern Referansı**: `/Users/nurullah/Desktop/cms/laravel/Modules/Portfolio/resources/views/front/`

**Alpine.js + Tailwind Pattern** (Portfolio'dan kopyala):
```html
<!-- index.blade.php (Ürün Listesi) -->
<div x-data="{ viewMode: 'grid' }">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Ürünler</h1>

        <!-- View Toggle -->
        <div class="flex gap-2">
            <button @click="viewMode = 'grid'"
                    :class="viewMode === 'grid' ? 'bg-blue-500 text-white' : 'bg-gray-200'">
                <i class="fas fa-th"></i>
            </button>
            <button @click="viewMode = 'list'"
                    :class="viewMode === 'list' ? 'bg-blue-500 text-white' : 'bg-gray-200'">
                <i class="fas fa-list"></i>
            </button>
        </div>
    </div>

    <!-- Product Grid -->
    <div x-show="viewMode === 'grid'"
         class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($products as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                <!-- Image -->
                @if($product->hasMedia('featured_image'))
                    <img src="{{ $product->getFirstMediaUrl('featured_image', 'medium') }}"
                         class="w-full h-48 object-cover">
                @endif

                <!-- Content -->
                <div class="p-4">
                    <h3 class="font-semibold text-lg mb-2">
                        {{ $product->getTranslated('title', app()->getLocale()) }}
                    </h3>

                    <!-- Price -->
                    <div class="flex items-center justify-between">
                        <span class="text-2xl font-bold text-blue-600">
                            {{ number_format($product->base_price, 2) }} ₺
                        </span>

                        @if($product->has_discount)
                            <span class="text-sm line-through text-gray-400">
                                {{ number_format($product->compare_at_price, 2) }} ₺
                            </span>
                        @endif
                    </div>

                    <!-- Add to Cart -->
                    <button class="w-full mt-4 bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">
                        Sepete Ekle
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    {{ $products->links() }}
</div>
```

---

#### 3.3 Routes

**Hedef Dosya**: `/Users/nurullah/Desktop/cms/laravel/Modules/Shop/routes/web.php`

**Pattern**: DynamicRouteService (Portfolio pattern)

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Services\DynamicRouteService;

// Frontend routes (DynamicRouteService pattern)
DynamicRouteService::registerModuleRoutes('Shop', [
    'index' => 'ShopController@index',
    'shop' => 'ShopController@shop',
    'show' => 'ShopController@show',
    'category' => 'ShopController@category',
    'brand' => 'ShopController@brand',
]);

// Admin routes (Livewire pattern)
Route::middleware(['auth', 'tenant'])->prefix('admin/shop')->name('admin.shop.')->group(function () {
    Route::get('/products', \Modules\Shop\App\Http\Livewire\Admin\ShopProductComponent::class)
        ->name('products');
    Route::get('/products/manage/{id?}', \Modules\Shop\App\Http\Livewire\Admin\ShopProductManageComponent::class)
        ->name('product.manage');

    Route::get('/categories', \Modules\Shop\App\Http\Livewire\Admin\ShopCategoryComponent::class)
        ->name('categories');
    Route::get('/categories/manage/{id?}', \Modules\Shop\App\Http\Livewire\Admin\ShopCategoryManageComponent::class)
        ->name('category.manage');

    Route::get('/brands', \Modules\Shop\App\Http\Livewire\Admin\ShopBrandComponent::class)
        ->name('brands');
});
```

---

## ⚠️ KRİTİK HATIRLATMALAR

### ✅ YAPILMASI GEREKENLER

1. **Portfolio Pattern'ini Birebir Takip Et**
   - AYNI trait'ler
   - AYNI interface'ler
   - AYNI repository pattern
   - AYNI Livewire yapısı
   - AYNI UI/UX pattern

2. **SortableJS Kullan**
   - Manuel sort_order input YOK
   - Drag-drop ile sıralama
   - updateOrder() method

3. **Toggle Button Kullan (Liste)**
   - Button + Icon (check/times)
   - Loading state
   - Color: text-muted/text-red

4. **Pretty Checkbox Kullan (Form)**
   - Create/Edit formlarda
   - Toggle için (is_active)

5. **Migration Referanslarını Kullan**
   - Phase-1 migration field'ları AYNEN
   - Veri tipi değiştirme

### ❌ YAPILMAMASI GEREKENLER

1. ❌ Controller oluşturma (Admin için)
2. ❌ Manuel sıralama (input field)
3. ❌ Kafadan UI tasarlama (Portfolio kopyala)
4. ❌ Custom renk ekleme
5. ❌ Migration field değiştirme

---

## 📝 ÇIKTI FORMATI

Her dosya oluşturduktan sonra:

```
✅ TAMAMLANDI: ShopProductVariant.php
📍 Lokasyon: /Users/nurullah/Desktop/cms/laravel/Modules/Shop/app/Models/ShopProductVariant.php
📊 İçerik:
   - Primary Key: variant_id
   - Traits: HasTranslations, HasSeo, HasMediaManagement
   - Relations: product()
   - Methods: 15 method
   - Pattern: Portfolio.php (100% uyumlu)

🔗 İlişkili Dosyalar:
   ✅ ShopProductVariantRepositoryInterface.php
   ✅ ShopProductVariantRepository.php
   ✅ ShopProductVariantService.php

📋 Sonraki Adım: ShopAttribute.php
```

---

## 🎯 BAŞLANGIÇ KOMUTU

**Şu sıraya göre başla**:

1. ✅ Tüm referans dosyalarını OKU
2. ✅ claudeguncel.md'yi OKU
3. ✅ Portfolio pattern'ini İNCELE (özellikle Livewire components + views)
4. ✅ Phase-1 migration'ları İNCELE
5. ✅ Admin Controller'ları SİL (varsa)
6. 🚀 ShopProductVariant modelinden BAŞLA
7. 🚀 Repository/Service oluştur
8. 🚀 Livewire components oluştur (Portfolio'dan AYNEN kopyala!)
9. 🚀 Blade views oluştur (UI pattern'i BİREBİR)

**Hazır mısın? Başla! 🚀**
