# 🚀 Layout Builder Development Roadmap - Practical Implementation Guide

## 📋 **GELİŞTİRME STRATEJİSİ**

### 🎯 **Temel Prensipler**
1. **User-First Development** - Kullanıcı deneyimi öncelikli
2. **Incremental Delivery** - Küçük, test edilebilir parçalar halinde
3. **Studio Pattern Base** - Mevcut Studio modülünü temel al
4. **No Breaking Changes** - Geriye dönük uyumluluk
5. **Performance Metrics** - Her aşamada performans ölçümü

---

## 🏗️ **PHASE 1: FOUNDATION (1-2 Hafta)**

### 📦 **1.1 Modül Oluşturma**
```bash
# Modül oluştur
php artisan module:make LayoutBuilder

# Gerekli dizinleri oluştur
cd Modules/LayoutBuilder
mkdir -p {app/{Services,Models,Http/{Controllers/Admin,Livewire/Admin},Repositories,DTOs,Exceptions},config,database/{migrations/{tenant},seeders},resources/{views/{admin,components,layouts},js,css},routes}
```

### 📁 **1.2 Temel Dosya Yapısı**
```
Modules/LayoutBuilder/
├── app/
│   ├── Services/
│   │   ├── LayoutEditorService.php      # Studio EditorService'den adapt
│   │   ├── LayoutComponentService.php   # Component yönetimi
│   │   ├── LayoutTemplateService.php    # Template sistemi
│   │   └── LayoutPreviewService.php     # Preview engine
│   ├── Models/
│   │   ├── LayoutTemplate.php           # Ana şablonlar
│   │   ├── LayoutComponent.php          # Component tanımları
│   │   └── TenantLayout.php             # Tenant layouts
│   ├── Http/
│   │   ├── Controllers/Admin/
│   │   │   └── LayoutBuilderController.php
│   │   └── Livewire/Admin/
│   │       ├── LayoutDashboard.php      # Ana dashboard
│   │       ├── HeaderBuilder.php        # Header builder
│   │       ├── FooterBuilder.php        # Footer builder
│   │       └── LayoutPreview.php        # Preview component
│   └── DTOs/
│       ├── LayoutConfig.php
│       └── ComponentConfig.php
├── config/
│   └── layoutbuilder.php               # Ana config
├── database/
│   ├── migrations/
│   │   ├── create_layout_templates_table.php
│   │   └── create_layout_components_table.php
│   └── seeders/
│       └── DefaultLayoutSeeder.php
└── resources/
    ├── views/
    │   ├── admin/
    │   │   ├── dashboard.blade.php
    │   │   ├── header-builder.blade.php
    │   │   └── footer-builder.blade.php
    │   └── components/
    │       └── preview-frame.blade.php
    └── js/
        ├── layout-builder.js
        └── components/
```

### 🔧 **1.3 Service Provider Setup**
```php
// Providers/LayoutBuilderServiceProvider.php
public function boot()
{
    $this->registerConfig();
    $this->registerViews();
    $this->registerRoutes();
    $this->registerLivewireComponents();
    $this->registerServices();
}

protected function registerServices()
{
    $this->app->singleton(LayoutEditorService::class);
    $this->app->singleton(LayoutComponentService::class);
    $this->app->singleton(LayoutTemplateService::class);
}
```

---

## 🎨 **PHASE 2: VISUAL BUILDER CORE (2-3 Hafta)**

### 🏗️ **2.1 GrapesJS Integration**
```javascript
// resources/js/layout-builder.js
import grapesjs from 'grapesjs';
import 'grapesjs-preset-webpage';

class LayoutBuilder {
    constructor(config) {
        this.editor = null;
        this.config = {
            container: '#layout-builder',
            height: '100vh',
            width: 'auto',
            storageManager: {
                type: 'remote',
                stepsBeforeSave: 1,
                urlStore: '/admin/layoutbuilder/save',
                urlLoad: '/admin/layoutbuilder/load',
                params: { _token: window.csrf_token }
            },
            canvas: {
                styles: [
                    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css'
                ],
                scripts: [
                    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'
                ]
            },
            plugins: ['gjs-preset-webpage'],
            pluginsOpts: {
                'gjs-preset-webpage': {}
            },
            ...config
        };
    }

    init() {
        this.editor = grapesjs.init(this.config);
        this.registerCustomBlocks();
        this.registerCustomComponents();
        this.setupEventListeners();
        return this.editor;
    }

    registerCustomBlocks() {
        const blockManager = this.editor.BlockManager;
        
        // Header Blocks
        blockManager.add('header-minimal', {
            label: 'Minimal Header',
            category: 'Headers',
            content: {
                type: 'header-component',
                variant: 'minimal'
            }
        });

        // Footer Blocks
        blockManager.add('footer-multi-column', {
            label: 'Multi Column Footer',
            category: 'Footers',
            content: {
                type: 'footer-component',
                variant: 'multi-column'
            }
        });
    }
}
```

### 🎛️ **2.2 Component System**
```php
// app/Services/LayoutComponentService.php
namespace Modules\LayoutBuilder\App\Services;

class LayoutComponentService
{
    protected array $components = [];
    
    public function register(string $type, array $config): void
    {
        $this->components[$type] = [
            'name' => $config['name'],
            'icon' => $config['icon'] ?? 'fa-cube',
            'category' => $config['category'] ?? 'General',
            'variants' => $config['variants'] ?? [],
            'settings' => $config['settings'] ?? [],
            'template' => $config['template'] ?? null,
            'preview' => $config['preview'] ?? null
        ];
    }
    
    public function getComponentsByCategory(): array
    {
        $grouped = [];
        foreach ($this->components as $type => $component) {
            $category = $component['category'];
            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            $grouped[$category][$type] = $component;
        }
        return $grouped;
    }
    
    public function renderComponent(string $type, array $props = []): string
    {
        $component = $this->components[$type] ?? null;
        if (!$component || !$component['template']) {
            return '';
        }
        
        return view($component['template'], [
            'props' => $props,
            'settings' => $component['settings']
        ])->render();
    }
}
```

### 📐 **2.3 Layout Templates**
```php
// database/seeders/DefaultLayoutSeeder.php
class DefaultLayoutSeeder extends Seeder
{
    public function run()
    {
        $templates = [
            [
                'name' => 'Business Classic',
                'category' => 'business',
                'description' => 'Professional business layout',
                'config' => [
                    'header' => [
                        'type' => 'horizontal',
                        'sticky' => true,
                        'components' => ['logo', 'nav', 'cta']
                    ],
                    'footer' => [
                        'type' => 'multi-column',
                        'columns' => 4,
                        'components' => ['about', 'links', 'contact', 'newsletter']
                    ]
                ],
                'preview_image' => 'business-classic.jpg',
                'is_premium' => false
            ],
            [
                'name' => 'E-commerce Modern',
                'category' => 'ecommerce',
                'description' => 'Modern e-commerce layout',
                'config' => [
                    'header' => [
                        'type' => 'split',
                        'sticky' => true,
                        'components' => ['logo', 'search', 'nav', 'cart', 'user']
                    ],
                    'footer' => [
                        'type' => 'mega',
                        'columns' => 5,
                        'components' => ['categories', 'support', 'account', 'social', 'payment']
                    ]
                ],
                'preview_image' => 'ecommerce-modern.jpg',
                'is_premium' => false
            ]
        ];
        
        foreach ($templates as $template) {
            LayoutTemplate::create($template);
        }
    }
}
```

---

## 🔧 **PHASE 3: BUILDER INTERFACES (2-3 Hafta)**

### 🎨 **3.1 Header Builder Interface**
```php
// app/Http/Livewire/Admin/HeaderBuilder.php
namespace Modules\LayoutBuilder\App\Http\Livewire\Admin;

use Livewire\Component;

class HeaderBuilder extends Component
{
    public $config = [];
    public $activeTab = 'layout';
    public $previewMode = 'desktop';
    
    protected $listeners = [
        'updateConfig' => 'handleConfigUpdate',
        'saveHeader' => 'save',
        'previewModeChanged' => 'setPreviewMode'
    ];
    
    public function mount()
    {
        $this->config = $this->loadCurrentConfig();
    }
    
    public function handleConfigUpdate($key, $value)
    {
        data_set($this->config, $key, $value);
        $this->emit('configUpdated', $this->config);
    }
    
    public function save()
    {
        try {
            $service = app(LayoutEditorService::class);
            $service->saveHeaderConfig(tenant()->id, $this->config);
            
            $this->dispatchBrowserEvent('toast', [
                'type' => 'success',
                'message' => 'Header configuration saved successfully!'
            ]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('toast', [
                'type' => 'error',
                'message' => 'Error saving configuration: ' . $e->getMessage()
            ]);
        }
    }
    
    public function render()
    {
        return view('layoutbuilder::admin.header-builder', [
            'availableComponents' => $this->getAvailableComponents(),
            'templates' => $this->getHeaderTemplates()
        ]);
    }
}
```

### 🖼️ **3.2 Preview System**
```javascript
// resources/js/components/preview-manager.js
export class PreviewManager {
    constructor(container) {
        this.container = container;
        this.iframe = null;
        this.currentDevice = 'desktop';
        this.devices = {
            desktop: { width: '100%', height: '100%' },
            tablet: { width: '768px', height: '1024px' },
            mobile: { width: '375px', height: '667px' }
        };
    }
    
    init() {
        this.createIframe();
        this.setupDeviceControls();
        this.setupRealtimeUpdates();
    }
    
    createIframe() {
        this.iframe = document.createElement('iframe');
        this.iframe.src = '/admin/layoutbuilder/preview';
        this.iframe.style.width = '100%';
        this.iframe.style.height = '100%';
        this.iframe.style.border = 'none';
        this.container.appendChild(this.iframe);
    }
    
    updatePreview(config) {
        this.iframe.contentWindow.postMessage({
            type: 'updateLayout',
            config: config
        }, '*');
    }
    
    setDevice(device) {
        this.currentDevice = device;
        const dimensions = this.devices[device];
        
        if (device === 'desktop') {
            this.iframe.style.width = dimensions.width;
            this.iframe.style.height = dimensions.height;
        } else {
            this.iframe.style.width = dimensions.width;
            this.iframe.style.height = dimensions.height;
            this.iframe.style.maxWidth = dimensions.width;
        }
        
        this.iframe.classList.remove('desktop', 'tablet', 'mobile');
        this.iframe.classList.add(device);
    }
}
```

### 🎛️ **3.3 Settings Panel**
```blade
{{-- resources/views/admin/components/settings-panel.blade.php --}}
<div class="settings-panel" x-data="settingsPanel()">
    <div class="settings-tabs">
        <button 
            @click="activeTab = 'layout'" 
            :class="{ 'active': activeTab === 'layout' }"
            class="tab-button"
        >
            <i class="fas fa-layout"></i> Layout
        </button>
        <button 
            @click="activeTab = 'style'" 
            :class="{ 'active': activeTab === 'style' }"
            class="tab-button"
        >
            <i class="fas fa-palette"></i> Style
        </button>
        <button 
            @click="activeTab = 'content'" 
            :class="{ 'active': activeTab === 'content' }"
            class="tab-button"
        >
            <i class="fas fa-font"></i> Content
        </button>
    </div>
    
    <div class="settings-content">
        {{-- Layout Tab --}}
        <div x-show="activeTab === 'layout'" class="tab-panel">
            <div class="form-group">
                <label>Layout Type</label>
                <select wire:model="config.layout.type" class="form-control">
                    <option value="horizontal">Horizontal</option>
                    <option value="vertical">Vertical</option>
                    <option value="split">Split</option>
                    <option value="centered">Centered</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Container</label>
                <select wire:model="config.layout.container" class="form-control">
                    <option value="fluid">Fluid</option>
                    <option value="boxed">Boxed</option>
                </select>
            </div>
            
            <div class="form-check">
                <input 
                    type="checkbox" 
                    wire:model="config.layout.sticky" 
                    class="form-check-input" 
                    id="stickyHeader"
                >
                <label class="form-check-label" for="stickyHeader">
                    Sticky Header
                </label>
            </div>
        </div>
        
        {{-- Style Tab --}}
        <div x-show="activeTab === 'style'" class="tab-panel">
            <div class="form-group">
                <label>Background Color</label>
                <input 
                    type="color" 
                    wire:model.defer="config.style.background" 
                    class="form-control form-control-color"
                >
            </div>
            
            <div class="form-group">
                <label>Text Color</label>
                <input 
                    type="color" 
                    wire:model.defer="config.style.textColor" 
                    class="form-control form-control-color"
                >
            </div>
            
            <div class="form-group">
                <label>Padding</label>
                <div class="row g-2">
                    <div class="col">
                        <input 
                            type="number" 
                            wire:model.defer="config.style.padding.top" 
                            class="form-control form-control-sm" 
                            placeholder="Top"
                        >
                    </div>
                    <div class="col">
                        <input 
                            type="number" 
                            wire:model.defer="config.style.padding.bottom" 
                            class="form-control form-control-sm" 
                            placeholder="Bottom"
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
```

---

## 🚀 **PHASE 4: ADVANCED FEATURES (2-3 Hafta)**

### 🤖 **4.1 AI Integration**
```php
// app/Services/AILayoutAssistant.php
namespace Modules\LayoutBuilder\App\Services;

class AILayoutAssistant
{
    protected $openai;
    
    public function __construct()
    {
        $this->openai = new OpenAI(['api_key' => config('services.openai.key')]);
    }
    
    public function suggestLayout(array $context): array
    {
        $prompt = $this->buildPrompt($context);
        
        $response = $this->openai->completions()->create([
            'model' => 'gpt-4',
            'prompt' => $prompt,
            'max_tokens' => 500,
            'temperature' => 0.7
        ]);
        
        return $this->parseResponse($response);
    }
    
    public function optimizeColors(array $currentColors, string $industry): array
    {
        // AI-based color optimization
        return [
            'primary' => '#007bff',
            'secondary' => '#6c757d',
            'accent' => '#28a745',
            'background' => '#ffffff',
            'text' => '#212529'
        ];
    }
    
    public function generateContent(string $section, array $businessInfo): string
    {
        // AI-powered content generation
        $prompts = [
            'header_tagline' => "Generate a short, impactful tagline for a {$businessInfo['industry']} company",
            'footer_about' => "Write a brief about section for a {$businessInfo['industry']} company footer"
        ];
        
        return $this->openai->completions()->create([
            'model' => 'gpt-3.5-turbo',
            'prompt' => $prompts[$section] ?? '',
            'max_tokens' => 100
        ])->choices[0]->text;
    }
}
```

### 📱 **4.2 Responsive Controls**
```javascript
// resources/js/components/responsive-manager.js
export class ResponsiveManager {
    constructor(editor) {
        this.editor = editor;
        this.breakpoints = {
            mobile: 576,
            tablet: 768,
            desktop: 1200
        };
    }
    
    setupResponsiveControls() {
        // Add device manager
        this.editor.DeviceManager.add('Mobile', '375px');
        this.editor.DeviceManager.add('Tablet', '768px');
        this.editor.DeviceManager.add('Desktop', '');
        
        // Add responsive traits
        this.addResponsiveTraits();
        
        // Setup media query listeners
        this.setupMediaQueryListeners();
    }
    
    addResponsiveTraits() {
        const textComponent = this.editor.DomComponents.getType('text');
        
        textComponent.model.prototype.defaults.traits.push({
            type: 'select',
            label: 'Mobile Display',
            name: 'data-mobile-display',
            options: [
                { value: '', name: 'Default' },
                { value: 'none', name: 'Hide' },
                { value: 'block', name: 'Show' }
            ]
        });
    }
    
    generateResponsiveCSS(config) {
        let css = '';
        
        // Mobile styles
        if (config.mobile) {
            css += `@media (max-width: ${this.breakpoints.mobile}px) {\n`;
            css += this.generateDeviceCSS(config.mobile);
            css += '}\n';
        }
        
        // Tablet styles
        if (config.tablet) {
            css += `@media (min-width: ${this.breakpoints.mobile + 1}px) and (max-width: ${this.breakpoints.tablet}px) {\n`;
            css += this.generateDeviceCSS(config.tablet);
            css += '}\n';
        }
        
        return css;
    }
}
```

### 🎨 **4.3 Template Marketplace**
```php
// app/Http/Livewire/Admin/TemplateMarketplace.php
class TemplateMarketplace extends Component
{
    public $templates = [];
    public $categories = ['all', 'business', 'ecommerce', 'portfolio', 'blog'];
    public $selectedCategory = 'all';
    public $searchTerm = '';
    
    public function mount()
    {
        $this->loadTemplates();
    }
    
    public function loadTemplates()
    {
        $query = LayoutTemplate::query()
            ->when($this->selectedCategory !== 'all', function ($q) {
                $q->where('category', $this->selectedCategory);
            })
            ->when($this->searchTerm, function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'like', "%{$this->searchTerm}%")
                          ->orWhere('description', 'like', "%{$this->searchTerm}%");
                });
            });
            
        $this->templates = $query->paginate(12);
    }
    
    public function installTemplate($templateId)
    {
        try {
            $template = LayoutTemplate::findOrFail($templateId);
            
            // Apply template to current tenant
            $service = app(LayoutTemplateService::class);
            $service->applyTemplate($template, tenant()->id);
            
            $this->emit('templateInstalled', $template);
            
            $this->dispatchBrowserEvent('toast', [
                'type' => 'success',
                'message' => "Template '{$template->name}' installed successfully!"
            ]);
            
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('toast', [
                'type' => 'error',
                'message' => 'Error installing template: ' . $e->getMessage()
            ]);
        }
    }
    
    public function previewTemplate($templateId)
    {
        $this->emit('openTemplatePreview', $templateId);
    }
}
```

---

## 🔍 **PHASE 5: TESTING & OPTIMIZATION (1-2 Hafta)**

### 🧪 **5.1 Unit Tests**
```php
// tests/Unit/LayoutBuilderTest.php
class LayoutBuilderTest extends TestCase
{
    public function test_can_create_layout_template()
    {
        $template = LayoutTemplate::factory()->create([
            'name' => 'Test Template',
            'category' => 'business'
        ]);
        
        $this->assertDatabaseHas('layout_templates', [
            'name' => 'Test Template',
            'category' => 'business'
        ]);
    }
    
    public function test_can_apply_template_to_tenant()
    {
        $tenant = Tenant::factory()->create();
        $template = LayoutTemplate::factory()->create();
        
        $service = app(LayoutTemplateService::class);
        $result = $service->applyTemplate($template, $tenant->id);
        
        $this->assertTrue($result);
        $this->assertDatabaseHas('tenant_layouts', [
            'tenant_id' => $tenant->id,
            'template_id' => $template->id
        ]);
    }
}
```

### ⚡ **5.2 Performance Optimization**
```php
// app/Services/LayoutCacheService.php
class LayoutCacheService
{
    protected $cache;
    protected $ttl = 3600; // 1 hour
    
    public function getOrSet(string $key, callable $callback)
    {
        return Cache::tags(['layouts', "tenant:" . tenant()->id])
            ->remember($key, $this->ttl, $callback);
    }
    
    public function clearTenantCache()
    {
        Cache::tags(["tenant:" . tenant()->id])->flush();
    }
    
    public function warmupCache()
    {
        // Preload common layouts
        $layouts = ['header', 'footer', 'sidebar'];
        
        foreach ($layouts as $layout) {
            $this->getOrSet("layout:{$layout}", function () use ($layout) {
                return app(LayoutEditorService::class)->loadLayout($layout);
            });
        }
    }
}
```

---

## 📊 **DELIVERY TIMELINE**

### Week 1-2: Foundation ✅
- [ ] Module setup
- [ ] Basic services
- [ ] Database schema
- [ ] Initial UI

### Week 3-4: Visual Builder 🔧
- [ ] GrapesJS integration
- [ ] Component system
- [ ] Basic templates
- [ ] Preview system

### Week 5-6: Builder Interfaces 🎨
- [ ] Header builder
- [ ] Footer builder
- [ ] Settings panels
- [ ] Save/Load functionality

### Week 7-8: Advanced Features 🚀
- [ ] AI integration
- [ ] Template marketplace
- [ ] Responsive controls
- [ ] Export/Import

### Week 9-10: Polish & Launch 🏁
- [ ] Testing
- [ ] Performance optimization
- [ ] Documentation
- [ ] Deployment

---

## 🎯 **SUCCESS METRICS**

### Development KPIs
- ✅ Code coverage > 80%
- ✅ Page load time < 2s
- ✅ Builder load time < 1s
- ✅ Zero breaking changes

### User Experience KPIs
- ✅ Time to first layout < 5 min
- ✅ Template satisfaction > 90%
- ✅ Support tickets < 5%
- ✅ Feature adoption > 70%

---

## 🚀 **IMMEDIATE NEXT STEPS**

1. **Create LayoutBuilder module**
   ```bash
   php artisan module:make LayoutBuilder
   ```

2. **Copy and adapt Studio services**
   ```bash
   cp Modules/Studio/app/Services/* Modules/LayoutBuilder/app/Services/
   ```

3. **Setup development environment**
   ```bash
   cd Modules/LayoutBuilder
   npm init -y
   npm install grapesjs grapesjs-preset-webpage
   ```

4. **Create initial migrations**
   ```bash
   php artisan module:make-migration create_layout_templates_table LayoutBuilder
   ```

5. **Start with Header Builder**
   - Simple UI first
   - Basic save/load
   - Preview system
   - Then add complexity

**Ready to start development! 🚀**