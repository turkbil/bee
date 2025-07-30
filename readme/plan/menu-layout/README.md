# 🚀 MENU VE LAYOUT YÖNETİM SİSTEMİ - DETAYLI ÇALIŞMA PLANI

## 📋 İÇİNDEKİLER

1. [Genel Bakış](#genel-bakış)
2. [Modül Yapısı](#modül-yapısı)
3. [MenuManagement Modülü](#menumanagement-modülü)
4. [LayoutBuilder Modülü](#layoutbuilder-modülü)
5. [Entegrasyon ve Bağımlılıklar](#entegrasyon-ve-bağımlılıklar)
6. [Uygulama Adımları](#uygulama-adımları)
7. [Test Senaryoları](#test-senaryoları)

---

## 🎯 GENEL BAKIŞ

### Amaç
Multi-tenant Laravel sistemimizde tema bağımsız, esnek ve kullanıcı dostu bir menü ve layout yönetim sistemi oluşturmak.

### Temel Özellikler
- ✅ **Tema Bağımsızlığı**: Tema değişse bile ayarlar korunur
- ✅ **Çoklu Dil Desteği**: Page modülü pattern'ı ile JSON tabanlı
- ✅ **Drag & Drop**: Sürükle-bırak ile kolay düzenleme
- ✅ **Widget Sistemi**: Modüler ve genişletilebilir
- ✅ **Responsive**: Mobil/tablet/desktop için ayrı ayarlar
- ✅ **Multi-tenant**: Her tenant kendi layout'unu yönetir

### Pattern Kullanımı
- **Kod Pattern**: Page modülü (Service Layer, Repository, DTO, Modern PHP 8.3+)
- **Tasarım Pattern**: Page manage sayfası (Form yapısı, Tab sistemi, Multi-lang)
- **Widget Pattern**: WidgetManagement item/settings schema yapısı

---

## 🏗️ MODÜL YAPISI

### 1. MenuManagement Modülü
```
Modules/MenuManagement/
├── app/
│   ├── Models/
│   │   ├── Menu.php                    # HasTranslations trait
│   │   ├── MenuItem.php                # HasTranslations trait
│   │   └── MenuLocation.php            # Menü konumları
│   ├── Services/
│   │   ├── MenuService.php             # Readonly, SOLID
│   │   └── MenuBuilder.php             # Menü oluşturma logic
│   ├── Repositories/
│   │   ├── MenuRepositoryInterface.php
│   │   └── MenuRepository.php          # Smart caching
│   ├── DTOs/
│   │   └── MenuOperationResult.php     # Response DTO
│   ├── Exceptions/
│   │   ├── MenuException.php
│   │   └── MenuNotFoundException.php
│   └── Http/
│       └── Livewire/
│           └── Admin/
│               ├── MenuManageComponent.php  # CRUD + Drag&Drop
│               └── MenuBuilderComponent.php # Menü öğeleri yönetimi
├── config/
│   └── menu-locations.php              # Varsayılan menü konumları
├── database/
│   └── migrations/
│       └── tenant/
│           ├── create_menus_table.php
│           ├── create_menu_items_table.php
│           └── create_menu_locations_table.php
└── resources/
    └── views/
        └── livewire/
            ├── menu-manage.blade.php   # Page pattern UI
            └── menu-builder.blade.php  # Drag&drop builder
```

### 2. LayoutBuilder Modülü
```
Modules/LayoutBuilder/
├── app/
│   ├── Models/
│   │   ├── Layout.php                  # Header/Footer ayarları
│   │   ├── LayoutArea.php              # Widget alanları
│   │   └── LayoutWidget.php            # Alan-widget ilişkisi
│   ├── Services/
│   │   ├── LayoutService.php           # Readonly, SOLID
│   │   ├── HeaderBuilder.php           # Header logic
│   │   └── FooterBuilder.php           # Footer logic
│   ├── Repositories/
│   │   ├── LayoutRepositoryInterface.php
│   │   └── LayoutRepository.php        # Smart caching
│   ├── DTOs/
│   │   └── LayoutOperationResult.php   # Response DTO
│   ├── Widgets/                        # Yeni widget tipleri
│   │   ├── LogoWidget.php
│   │   ├── MenuWidget.php              # MenuManagement entegrasyonu
│   │   ├── HtmlWidget.php
│   │   ├── ButtonWidget.php
│   │   ├── SearchWidget.php
│   │   └── LanguageSwitcherWidget.php
│   └── Http/
│       └── Livewire/
│           └── Admin/
│               ├── LayoutBuilderComponent.php    # Ana builder
│               ├── HeaderBuilderComponent.php    # Header özel
│               └── FooterBuilderComponent.php    # Footer özel
├── config/
│   ├── layout-areas.php                # Widget alanları tanımı
│   └── layout-templates.php            # Hazır şablonlar
├── database/
│   └── migrations/
│       └── tenant/
│           ├── create_layouts_table.php
│           ├── create_layout_areas_table.php
│           └── create_layout_widgets_table.php
└── resources/
    └── views/
        ├── livewire/
        │   ├── layout-builder.blade.php
        │   ├── header-builder.blade.php
        │   └── footer-builder.blade.php
        ├── widgets/                     # Widget blade'leri
        │   ├── logo.blade.php
        │   ├── menu.blade.php
        │   └── ...
        └── render/                      # Frontend render
            ├── header.blade.php
            └── footer.blade.php
```

---

## 📱 MENUMANAGEMENT MODÜLÜ DETAYI

### Database Yapısı

#### menus tablosu
```sql
CREATE TABLE menus (
    menu_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,           -- "Ana Menü", "Footer Menü"
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    location VARCHAR(100),                 -- header-main, footer-1, vb.
    is_active BOOLEAN DEFAULT TRUE,
    settings JSON,                         -- Menü ayarları
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### menu_items tablosu
```sql
CREATE TABLE menu_items (
    menu_item_id INT PRIMARY KEY AUTO_INCREMENT,
    menu_id INT NOT NULL,
    parent_id INT NULL,                    -- Alt menü için
    title JSON NOT NULL,                   -- {"tr": "Hakkımızda", "en": "About"}
    type ENUM('page', 'module', 'custom', 'category'),
    target_id INT NULL,                    -- page_id, category_id vb.
    url VARCHAR(500),                      -- Custom linkler için
    icon VARCHAR(100),                     -- FontAwesome icon
    css_class VARCHAR(255),                -- Custom CSS
    target VARCHAR(20) DEFAULT '_self',    -- _blank, _self
    position INT DEFAULT 0,                -- Sıralama
    is_active BOOLEAN DEFAULT TRUE,
    settings JSON,                         -- Özel ayarlar
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (menu_id) REFERENCES menus(menu_id),
    FOREIGN KEY (parent_id) REFERENCES menu_items(menu_item_id),
    INDEX idx_menu_position (menu_id, position)
);
```

#### menu_locations tablosu
```sql
CREATE TABLE menu_locations (
    location_id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(100) UNIQUE NOT NULL,     -- header-main, footer-1
    name VARCHAR(255) NOT NULL,            -- "Header Ana Menü"
    description TEXT,
    is_system BOOLEAN DEFAULT FALSE,       -- Sistem lokasyonu mu?
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Model Yapısı

#### Menu.php
```php
declare(strict_types=1);

namespace Modules\MenuManagement\app\Models;

use App\Models\BaseModel;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Menu extends BaseModel
{
    use HasTranslations;
    
    protected $primaryKey = 'menu_id';
    
    protected $fillable = [
        'name', 'slug', 'description', 
        'location', 'is_active', 'settings'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'json',
    ];
    
    protected $translatable = ['name', 'description'];
    
    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'menu_id', 'menu_id')
            ->whereNull('parent_id')
            ->orderBy('position');
    }
    
    public function allItems(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'menu_id', 'menu_id')
            ->orderBy('position');
    }
    
    public function location(): BelongsTo
    {
        return $this->belongsTo(MenuLocation::class, 'location', 'code');
    }
}
```

### Service Layer

#### MenuService.php
```php
declare(strict_types=1);

namespace Modules\MenuManagement\app\Services;

readonly class MenuService
{
    public function __construct(
        private MenuRepositoryInterface $repository,
        private MenuBuilder $builder
    ) {}
    
    public function createMenu(array $data): MenuOperationResult
    {
        try {
            // Slug kontrolü ve unique yapma
            $data['slug'] = SlugHelper::generateUniqueSlug(
                Menu::class,
                $data['slug'] ?? $data['name']
            );
            
            $menu = $this->repository->create($data);
            
            return MenuOperationResult::success(
                message: __('menu::admin.menu_created'),
                data: $menu
            );
        } catch (\Throwable $e) {
            throw MenuCreationException::withError($e->getMessage());
        }
    }
    
    public function buildMenuTree(int $menuId): array
    {
        return $this->builder->buildTree($menuId);
    }
    
    public function reorderItems(int $menuId, array $order): MenuOperationResult
    {
        return $this->builder->reorder($menuId, $order);
    }
}
```

### Livewire Component

#### MenuBuilderComponent.php
```php
declare(strict_types=1);

namespace Modules\MenuManagement\app\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

#[Layout('admin.layout')]
class MenuBuilderComponent extends Component
{
    public int $menuId;
    public array $menuItems = [];
    public array $availablePages = [];
    public array $availableModules = [];
    
    // Multi-language inputs
    public array $multiLangInputs = [];
    
    private MenuService $menuService;
    
    public function boot(MenuService $menuService): void
    {
        $this->menuService = $menuService;
    }
    
    public function mount(int $id): void
    {
        $this->menuId = $id;
        $this->loadMenuItems();
        $this->loadAvailableContent();
    }
    
    #[Computed]
    public function availableLanguages(): array
    {
        return TenantLanguage::getActiveLanguages();
    }
    
    public function updateOrder(array $items): void
    {
        try {
            $result = $this->menuService->reorderItems(
                $this->menuId, 
                $items
            );
            
            $this->dispatch('toast', [
                'message' => $result->message,
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => __('menu::admin.reorder_failed'),
                'type' => 'error'
            ]);
        }
    }
}
```

### UI/UX - Drag & Drop Builder

#### menu-builder.blade.php
```html
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ __('menu::admin.menu_builder') }}</h3>
    </div>
    
    <div class="card-body">
        <div class="row">
            {{-- Sol Panel - Mevcut Öğeler --}}
            <div class="col-md-8">
                <div class="menu-builder-container">
                    <div id="menu-items" 
                         class="dd"
                         wire:sortable="updateOrder">
                        @foreach($menuItems as $item)
                            @include('menu::partials.menu-item', [
                                'item' => $item,
                                'level' => 0
                            ])
                        @endforeach
                    </div>
                </div>
            </div>
            
            {{-- Sağ Panel - Yeni Öğe Ekle --}}
            <div class="col-md-4">
                <div class="accordion" id="menuItemTypes">
                    {{-- Sayfalar --}}
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" 
                                    type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#pages">
                                {{ __('menu::admin.pages') }}
                            </button>
                        </h2>
                        <div id="pages" class="accordion-collapse collapse show">
                            <div class="accordion-body">
                                @foreach($availablePages as $page)
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               wire:model="selectedPages"
                                               value="{{ $page->page_id }}">
                                        <label class="form-check-label">
                                            {{ $page->getTranslated('title') }}
                                        </label>
                                    </div>
                                @endforeach
                                <button class="btn btn-sm btn-primary mt-2"
                                        wire:click="addPages">
                                    {{ __('menu::admin.add_to_menu') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Özel Link --}}
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" 
                                    type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#customLink">
                                {{ __('menu::admin.custom_link') }}
                            </button>
                        </h2>
                        <div id="customLink" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                {{-- Dil Sekmeleri --}}
                                <ul class="nav nav-tabs mb-3">
                                    @foreach($this->availableLanguages as $lang)
                                        <li class="nav-item">
                                            <a class="nav-link @if($loop->first) active @endif"
                                               data-bs-toggle="tab"
                                               href="#custom-{{ $lang }}">
                                                {{ strtoupper($lang) }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                                
                                <div class="tab-content">
                                    @foreach($this->availableLanguages as $lang)
                                        <div class="tab-pane @if($loop->first) active @endif" 
                                             id="custom-{{ $lang }}">
                                            <div class="form-floating mb-2">
                                                <input type="text" 
                                                       class="form-control"
                                                       wire:model="customLink.title.{{ $lang }}"
                                                       placeholder="{{ __('menu::admin.link_text') }}">
                                                <label>{{ __('menu::admin.link_text') }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="form-floating mb-2">
                                    <input type="url" 
                                           class="form-control"
                                           wire:model="customLink.url"
                                           placeholder="https://">
                                    <label>{{ __('menu::admin.url') }}</label>
                                </div>
                                
                                <button class="btn btn-sm btn-primary"
                                        wire:click="addCustomLink">
                                    {{ __('menu::admin.add_to_menu') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Drag & Drop Script --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Nested sortable for menu items
    initializeSortable();
});

function initializeSortable() {
    const nestedSortables = document.querySelectorAll('.dd-list');
    
    for (let i = 0; i < nestedSortables.length; i++) {
        new Sortable(nestedSortables[i], {
            group: 'nested',
            animation: 150,
            fallbackOnBody: true,
            swapThreshold: 0.65,
            handle: '.dd-handle',
            onEnd: function(evt) {
                // Sıralama değiştiğinde Livewire'a bildir
                const order = extractOrder();
                @this.call('updateOrder', order);
            }
        });
    }
}

function extractOrder() {
    // Menu item'ların yeni sıralamasını çıkar
    const items = [];
    document.querySelectorAll('.dd-item').forEach((item, index) => {
        items.push({
            id: item.dataset.id,
            parent_id: item.closest('.dd-list')?.closest('.dd-item')?.dataset.id || null,
            position: index
        });
    });
    return items;
}
</script>
@endpush
```

---

## 🏗️ LAYOUTBUILDER MODÜLÜ DETAYI

### Database Yapısı

#### layouts tablosu
```sql
CREATE TABLE layouts (
    layout_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    type ENUM('header', 'footer', 'both') DEFAULT 'both',
    is_active BOOLEAN DEFAULT TRUE,
    header_settings JSON,                  -- Header yapılandırması
    footer_settings JSON,                  -- Footer yapılandırması
    responsive_settings JSON,              -- Responsive ayarlar
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### layout_areas tablosu
```sql
CREATE TABLE layout_areas (
    area_id INT PRIMARY KEY AUTO_INCREMENT,
    layout_id INT NOT NULL,
    type ENUM('header', 'footer') NOT NULL,
    section VARCHAR(50) NOT NULL,          -- top, main, bottom
    position VARCHAR(50) NOT NULL,         -- left, center, right
    area_code VARCHAR(100) NOT NULL,       -- header-top-left
    width VARCHAR(50),                     -- auto, 25%, 50%, vb.
    css_class VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    settings JSON,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (layout_id) REFERENCES layouts(layout_id),
    UNIQUE KEY unique_area (layout_id, area_code)
);
```

#### layout_widgets tablosu
```sql
CREATE TABLE layout_widgets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    area_id INT NOT NULL,
    widget_type VARCHAR(50) NOT NULL,      -- logo, menu, html, button
    widget_id INT NULL,                    -- WidgetManagement widget_id (opsiyonel)
    settings JSON NOT NULL,                -- Widget ayarları
    position INT DEFAULT 0,                -- Sıralama
    is_active BOOLEAN DEFAULT TRUE,
    responsive JSON,                       -- Responsive görünürlük
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (area_id) REFERENCES layout_areas(area_id)
);
```

### Widget Tipleri

#### 1. LogoWidget
```php
namespace Modules\LayoutBuilder\app\Widgets;

class LogoWidget extends BaseLayoutWidget
{
    public function getSettingsSchema(): array
    {
        return [
            [
                'name' => 'logo_type',
                'label' => 'Logo Tipi',
                'type' => 'select',
                'options' => [
                    ['value' => 'image', 'label' => 'Resim'],
                    ['value' => 'text', 'label' => 'Metin'],
                    ['value' => 'both', 'label' => 'Her İkisi']
                ],
                'default' => 'image'
            ],
            [
                'name' => 'logo_image',
                'label' => 'Logo Resmi',
                'type' => 'image',
                'condition' => ['logo_type' => ['image', 'both']]
            ],
            [
                'name' => 'logo_text',
                'label' => 'Logo Metni',
                'type' => 'text',
                'translatable' => true,
                'condition' => ['logo_type' => ['text', 'both']]
            ],
            [
                'name' => 'link_to_home',
                'label' => 'Ana Sayfaya Yönlendir',
                'type' => 'switch',
                'default' => true
            ]
        ];
    }
    
    public function render(array $settings): string
    {
        return view('layoutbuilder::widgets.logo', [
            'settings' => $settings
        ])->render();
    }
}
```

#### 2. MenuWidget
```php
class MenuWidget extends BaseLayoutWidget
{
    public function getSettingsSchema(): array
    {
        return [
            [
                'name' => 'menu_id',
                'label' => 'Menü Seçin',
                'type' => 'select',
                'options' => $this->getMenuOptions(),
                'required' => true
            ],
            [
                'name' => 'display_type',
                'label' => 'Görünüm Tipi',
                'type' => 'select',
                'options' => [
                    ['value' => 'horizontal', 'label' => 'Yatay'],
                    ['value' => 'vertical', 'label' => 'Dikey'],
                    ['value' => 'dropdown', 'label' => 'Dropdown']
                ],
                'default' => 'horizontal'
            ],
            [
                'name' => 'mobile_type',
                'label' => 'Mobil Görünüm',
                'type' => 'select',
                'options' => [
                    ['value' => 'hamburger', 'label' => 'Hamburger Menü'],
                    ['value' => 'bottom', 'label' => 'Alt Menü'],
                    ['value' => 'hidden', 'label' => 'Gizle']
                ],
                'default' => 'hamburger'
            ]
        ];
    }
}
```

### Layout Builder UI

#### layout-builder.blade.php
```html
<div class="layout-builder-container">
    {{-- Header Builder --}}
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">{{ __('layout::admin.header_builder') }}</h3>
            <div class="card-actions">
                <button class="btn btn-sm btn-primary"
                        wire:click="toggleSection('header')">
                    <i class="ti ti-{{ $headerExpanded ? 'chevron-up' : 'chevron-down' }}"></i>
                </button>
            </div>
        </div>
        
        @if($headerExpanded)
        <div class="card-body">
            {{-- Header Sections --}}
            @foreach(['top', 'main', 'bottom'] as $section)
                <div class="layout-section mb-3" 
                     data-section="header-{{ $section }}">
                    <h5 class="section-title">
                        {{ __('layout::admin.header_' . $section) }}
                        <div class="form-check form-switch float-end">
                            <input class="form-check-input" 
                                   type="checkbox"
                                   wire:model="headerSections.{{ $section }}.enabled">
                            <label class="form-check-label">
                                {{ __('layout::admin.enable') }}
                            </label>
                        </div>
                    </h5>
                    
                    @if($headerSections[$section]['enabled'] ?? false)
                    <div class="row g-2">
                        @foreach(['left', 'center', 'right'] as $position)
                            <div class="col-md-4">
                                <div class="widget-area" 
                                     wire:sortable="updateWidgetOrder"
                                     wire:sortable.item="header-{{ $section }}-{{ $position }}">
                                    <div class="area-header">
                                        <span>{{ ucfirst($position) }}</span>
                                        <button class="btn btn-xs btn-primary"
                                                wire:click="openWidgetModal('header', '{{ $section }}', '{{ $position }}')">
                                            <i class="ti ti-plus"></i>
                                        </button>
                                    </div>
                                    
                                    <div class="area-widgets">
                                        @foreach($this->getAreaWidgets('header', $section, $position) as $widget)
                                            <div class="widget-item" 
                                                 wire:key="widget-{{ $widget->id }}"
                                                 wire:sortable.handle>
                                                <div class="widget-header">
                                                    <i class="ti ti-grip-vertical handle"></i>
                                                    <span>{{ $widget->getTitle() }}</span>
                                                    <div class="widget-actions">
                                                        <button wire:click="editWidget({{ $widget->id }})"
                                                                class="btn btn-xs">
                                                            <i class="ti ti-settings"></i>
                                                        </button>
                                                        <button wire:click="removeWidget({{ $widget->id }})"
                                                                class="btn btn-xs text-danger">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            @endforeach
        </div>
        @endif
    </div>
    
    {{-- Footer Builder --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('layout::admin.footer_builder') }}</h3>
            <div class="card-actions">
                <button class="btn btn-sm btn-primary"
                        wire:click="toggleSection('footer')">
                    <i class="ti ti-{{ $footerExpanded ? 'chevron-up' : 'chevron-down' }}"></i>
                </button>
            </div>
        </div>
        
        @if($footerExpanded)
        <div class="card-body">
            {{-- Footer Sections --}}
            @foreach(['top', 'main', 'bottom'] as $section)
                <div class="layout-section mb-3">
                    <h5 class="section-title">
                        {{ __('layout::admin.footer_' . $section) }}
                        <div class="form-check form-switch float-end">
                            <input class="form-check-input" 
                                   type="checkbox"
                                   wire:model="footerSections.{{ $section }}.enabled">
                        </div>
                    </h5>
                    
                    @if($footerSections[$section]['enabled'] ?? false)
                        @if($section === 'main')
                            {{-- Footer Main - Column System --}}
                            <div class="mb-3">
                                <label class="form-label">{{ __('layout::admin.column_count') }}</label>
                                <select class="form-select" 
                                        wire:model="footerColumns">
                                    @for($i = 1; $i <= 6; $i++)
                                        <option value="{{ $i }}">{{ $i }} {{ __('layout::admin.columns') }}</option>
                                    @endfor
                                </select>
                            </div>
                            
                            <div class="row g-2">
                                @for($col = 1; $col <= $footerColumns; $col++)
                                    <div class="col-md-{{ 12 / $footerColumns }}">
                                        <div class="widget-area"
                                             wire:sortable="updateWidgetOrder"
                                             wire:sortable.item="footer-main-col{{ $col }}">
                                            <div class="area-header">
                                                <span>{{ __('layout::admin.column') }} {{ $col }}</span>
                                                <button class="btn btn-xs btn-primary"
                                                        wire:click="openWidgetModal('footer', 'main', 'col{{ $col }}')">
                                                    <i class="ti ti-plus"></i>
                                                </button>
                                            </div>
                                            
                                            {{-- Column widgets --}}
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        @else
                            {{-- Footer Top/Bottom - 3 column system --}}
                            <div class="row g-2">
                                @foreach(['left', 'center', 'right'] as $position)
                                    <div class="col-md-4">
                                        {{-- Widget area (same as header) --}}
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

{{-- Widget Ekleme/Düzenleme Modal --}}
<div class="modal fade" id="widgetModal" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {{ $editingWidget ? __('layout::admin.edit_widget') : __('layout::admin.add_widget') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                @if(!$editingWidget)
                    {{-- Widget Type Selection --}}
                    <div class="mb-3">
                        <label class="form-label">{{ __('layout::admin.widget_type') }}</label>
                        <select class="form-select" wire:model="selectedWidgetType">
                            <option value="">{{ __('layout::admin.select_widget_type') }}</option>
                            @foreach($this->availableWidgetTypes as $type => $info)
                                <option value="{{ $type }}">{{ $info['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                
                {{-- Widget Settings --}}
                @if($selectedWidgetType)
                    <div class="widget-settings">
                        @foreach($this->getWidgetSettingsSchema($selectedWidgetType) as $field)
                            @include('layoutbuilder::partials.widget-field', [
                                'field' => $field,
                                'model' => 'widgetSettings.' . $field['name']
                            ])
                        @endforeach
                    </div>
                @endif
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ __('admin.cancel') }}
                </button>
                <button type="button" class="btn btn-primary" wire:click="saveWidget">
                    {{ __('admin.save') }}
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.layout-builder-container {
    --area-bg: #f8f9fa;
    --area-border: #dee2e6;
    --widget-bg: #ffffff;
    --widget-hover: #f1f3f5;
}

.dark .layout-builder-container {
    --area-bg: #1a1d23;
    --area-border: #2d3238;
    --widget-bg: #24272d;
    --widget-hover: #2d3238;
}

.widget-area {
    background: var(--area-bg);
    border: 1px dashed var(--area-border);
    border-radius: 4px;
    min-height: 100px;
    padding: 0.5rem;
}

.area-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
}

.widget-item {
    background: var(--widget-bg);
    border: 1px solid var(--area-border);
    border-radius: 4px;
    padding: 0.5rem;
    margin-bottom: 0.5rem;
    cursor: move;
    transition: all 0.2s;
}

.widget-item:hover {
    background: var(--widget-hover);
}

.widget-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.widget-header .handle {
    cursor: grab;
}

.widget-header .handle:active {
    cursor: grabbing;
}

.widget-actions {
    margin-left: auto;
    display: flex;
    gap: 0.25rem;
}

.layout-section {
    border: 1px solid var(--area-border);
    border-radius: 4px;
    padding: 1rem;
}

.section-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
document.addEventListener('livewire:initialized', () => {
    // Widget sortable initialization
    initializeWidgetSortable();
    
    // Reinitialize after Livewire updates
    Livewire.hook('morph.updated', ({ el, component }) => {
        initializeWidgetSortable();
    });
});

function initializeWidgetSortable() {
    document.querySelectorAll('.widget-area').forEach(area => {
        new Sortable(area.querySelector('.area-widgets'), {
            group: 'widgets',
            animation: 150,
            handle: '.handle',
            ghostClass: 'sortable-ghost',
            onEnd: function(evt) {
                const areaCode = evt.to.closest('.widget-area').dataset.area;
                const itemId = evt.item.dataset.widgetId;
                const newIndex = evt.newIndex;
                
                @this.call('updateWidgetPosition', itemId, areaCode, newIndex);
            }
        });
    });
}
</script>
@endpush
```

### Frontend Render System

#### RenderService.php
```php
namespace Modules\LayoutBuilder\app\Services;

class RenderService
{
    public function renderHeader(): string
    {
        $layout = $this->getActiveLayout();
        
        if (!$layout) {
            return $this->getDefaultHeader();
        }
        
        return view('layoutbuilder::render.header', [
            'layout' => $layout,
            'areas' => $this->getHeaderAreas($layout)
        ])->render();
    }
    
    public function renderFooter(): string
    {
        $layout = $this->getActiveLayout();
        
        if (!$layout) {
            return $this->getDefaultFooter();
        }
        
        return view('layoutbuilder::render.footer', [
            'layout' => $layout,
            'areas' => $this->getFooterAreas($layout)
        ])->render();
    }
    
    private function renderWidget(LayoutWidget $layoutWidget): string
    {
        $widgetClass = $this->getWidgetClass($layoutWidget->widget_type);
        
        if (!$widgetClass) {
            return '';
        }
        
        $widget = new $widgetClass();
        
        return $widget->render($layoutWidget->settings);
    }
}
```

---

## 🔗 ENTEGRASYON VE BAĞIMLILIKLAR

### 1. Theme Entegrasyonu

#### Theme Layout Dosyası Güncelleme
```php
// resources/views/themes/{theme}/layouts/app.blade.php

@php
    $layoutService = app(\Modules\LayoutBuilder\app\Services\RenderService::class);
@endphp

<!DOCTYPE html>
<html>
<head>
    {{-- Head content --}}
</head>
<body>
    {{-- Header Render --}}
    {!! $layoutService->renderHeader() !!}
    
    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>
    
    {{-- Footer Render --}}
    {!! $layoutService->renderFooter() !!}
</body>
</html>
```

### 2. Widget Management Entegrasyonu

```php
// LayoutBuilder modülünde custom widget register
public function boot()
{
    // Widget tiplerini kaydet
    WidgetTypeRegistry::register('logo', LogoWidget::class);
    WidgetTypeRegistry::register('menu', MenuWidget::class);
    WidgetTypeRegistry::register('html', HtmlWidget::class);
    WidgetTypeRegistry::register('button', ButtonWidget::class);
    WidgetTypeRegistry::register('search', SearchWidget::class);
    WidgetTypeRegistry::register('language', LanguageSwitcherWidget::class);
}
```

### 3. Module Slug Service Entegrasyonu

```php
// MenuWidget içinde modül linklerini oluştururken
$moduleSlugService = app(ModuleSlugService::class);
$url = $moduleSlugService->generateUrl($module, $action, $parameters);
```

### 4. Cache Strategy

```php
// Layout değişikliklerinde cache temizleme
class LayoutObserver
{
    public function saved(Layout $layout): void
    {
        Cache::tags(['layouts', 'tenant:' . tenant('id')])->flush();
    }
}
```

---

## 📋 UYGULAMA ADIMLARI

### Phase 1: MenuManagement Modülü (1-2 Hafta)

1. **Modül Oluşturma**
   ```bash
   php artisan module:make MenuManagement
   ```

2. **Migration Oluşturma**
   - menus, menu_items, menu_locations tabloları
   - Tenant migration olarak

3. **Model ve Repository**
   - Page pattern'ı uygula
   - HasTranslations trait kullan
   - Repository pattern ve interface

4. **Service Layer**
   - MenuService (CRUD operations)
   - MenuBuilder (Tree building, reordering)
   - Exception handling

5. **Livewire Components**
   - MenuManageComponent (Liste ve CRUD)
   - MenuBuilderComponent (Drag & drop builder)

6. **UI Implementation**
   - Page pattern UI kullan
   - Sortable.js entegrasyonu
   - Multi-language support

### Phase 2: LayoutBuilder Modülü (2-3 Hafta)

1. **Modül Oluşturma**
   ```bash
   php artisan module:make LayoutBuilder
   ```

2. **Database Yapısı**
   - layouts, layout_areas, layout_widgets tabloları
   - Widget registry sistemi

3. **Widget Sistemi**
   - BaseLayoutWidget abstract class
   - Her widget tipi için ayrı class
   - Settings schema yapısı

4. **Service Layer**
   - LayoutService (CRUD)
   - HeaderBuilder, FooterBuilder
   - RenderService (Frontend)

5. **Builder UI**
   - Drag & drop widget areas
   - Live preview (opsiyonel)
   - Responsive settings

6. **Theme Entegrasyonu**
   - Helper functions
   - Blade directives
   - Override sistemi

### Phase 3: Test ve Optimizasyon (1 Hafta)

1. **Unit Tests**
   - Service testleri
   - Repository testleri
   - Widget render testleri

2. **Feature Tests**
   - Menu oluşturma/düzenleme
   - Layout builder işlemleri
   - Multi-tenant testleri

3. **Performance**
   - Query optimization
   - Cache implementation
   - Lazy loading

4. **Documentation**
   - Kullanım kılavuzu
   - API documentation
   - Widget geliştirme guide

---

## 🧪 TEST SENARYOLARI

### MenuManagement Tests

1. **Menu CRUD Operations**
   - Menü oluşturma (multi-lang)
   - Menü düzenleme
   - Menü silme
   - Menü kopyalama

2. **Menu Items**
   - Sayfa ekleme
   - Custom link ekleme
   - Nested items (alt menü)
   - Drag & drop sıralama

3. **Multi-tenant**
   - Her tenant'ın kendi menüleri
   - İzolasyon kontrolü

### LayoutBuilder Tests

1. **Layout Management**
   - Layout oluşturma
   - Header/Footer ayarları
   - Widget ekleme/çıkarma

2. **Widget Tests**
   - Her widget tipinin render testi
   - Settings validation
   - Responsive görünürlük

3. **Theme Integration**
   - Farklı temalarda test
   - Override mekanizması
   - Fallback to default

### Integration Tests

1. **Menu + Layout**
   - MenuWidget'ın menüleri göstermesi
   - Multi-language menu render
   - Cache temizleme

2. **Performance**
   - N+1 query kontrolü
   - Cache hit rate
   - Page load time

---

## 🎯 BAŞARI KRİTERLERİ

1. **Kullanılabilirlik**
   - Admin panelde kolay yönetim
   - Drag & drop ile hızlı düzenleme
   - Anlık önizleme

2. **Esneklik**
   - Sınırsız menü ve layout
   - Custom widget desteği
   - Theme override

3. **Performance**
   - Cached render < 50ms
   - Admin operations < 200ms
   - Optimize edilmiş queries

4. **Kod Kalitesi**
   - %100 Page pattern uyumu
   - SOLID principles
   - Modern PHP 8.3+
   - Comprehensive tests

5. **Multi-tenant**
   - Tam izolasyon
   - Tenant-specific cache
   - Güvenli data access

---

## 🚀 BONUS ÖZELLİKLER (İLERİDE EKLENEBİLİR)

1. **Mega Menu Desteği**
   - Column based mega menu
   - Widget embed in menu

2. **A/B Testing**
   - Multiple layout versions
   - Performance tracking

3. **Template Library**
   - Pre-built headers/footers
   - One-click import

4. **Advanced Widgets**
   - Weather widget
   - Social media feed
   - Newsletter signup

5. **API Support**
   - REST API for menus
   - Headless CMS support

6. **Visual Builder**
   - Live drag & drop
   - Real-time preview
   - Undo/Redo