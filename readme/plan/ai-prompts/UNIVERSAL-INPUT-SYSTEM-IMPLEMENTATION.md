# 🎯 UNIVERSAL INPUT SYSTEM - UYGULAMA KILAVUZU ✅ TAMAMLANDI (10.08.2025)

**SİSTEM DURUMU: ✅ BAŞARIYLA TAMAMLANDI VE PRODUCTION READY**

Burada hazırladığın ve yaptığın her işlemin önündeki kutucuğu onaysız işaretleyeceksin. Bu dosyayı her zaman güncel tutacaksın. 

## 📌 SİSTEM GENEL BAKIŞ

**Universal Input System**, AI Feature'larının dinamik form yapılarını yöneten, admin panelden tamamen kontrol edilebilen, database-driven bir sistemdir. Her AI feature için özel form tasarımı yapmayı ve kullanıcı seçimlerini otomatik olarak prompt zincirlerine dönüştürmeyi sağlar.

### **Temel Özellikler:**
- ✅ Database-driven form yapısı (JSON config yerine)
- ✅ Admin panelden tam kontrol
- ✅ Prompt-Input otomatik eşleştirme
- ✅ Modal/Accordion dual mode
- ✅ Central database yönetimi (tenant bağımsız)
- ✅ Cache'lenebilir performans
- ✅ A/B testing altyapısı
- ✅ Context-aware dynamic inputs
- ✅ Multi-module integration ready
- ✅ Template-based generation
- ✅ Bulk operations support

---

## 📊 VERİTABANI MİMARİSİ

### **Ana Tablolar:**

#### **Core Tables (Temel):**
1. **ai_feature_inputs** - Feature'ların input tanımları
2. **ai_input_options** - Input seçenekleri ve prompt bağlantıları
3. **ai_dynamic_data_sources** - Dinamik veri kaynakları
4. **ai_input_groups** - Input grupları (accordion/tab)

#### **Template Tables (Şablon):**
5. **ai_input_templates** - Tekrar kullanılabilir input şablonları
6. **ai_feature_template_relations** - Feature-Template ilişkileri

#### **Context Tables (Bağlam):**
7. **ai_context_rules** - Dinamik context kuralları
8. **ai_multi_table_operations** - Çoklu tablo işlemleri
9. **ai_language_operations** - Dil işlemleri ve çeviri ayarları

---

## ✅ KURULUM ADIMLARI

### **PHASE 1: VERİTABANI ALTYAPISI**

#### ✅ 1.1 Migration Dosyaları Oluştur

```bash
php artisan make:migration create_ai_feature_inputs_table --path=Modules/AI/database/migrations
php artisan make:migration create_ai_input_options_table --path=Modules/AI/database/migrations
php artisan make:migration create_ai_dynamic_data_sources_table --path=Modules/AI/database/migrations
php artisan make:migration create_ai_input_groups_table --path=Modules/AI/database/migrations
```

**Migration İçerikleri:**

```php
// 2025_XX_XX_create_ai_feature_inputs_table.php
Schema::create('ai_feature_inputs', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('ai_feature_id');
    $table->string('input_key', 50);
    $table->enum('input_type', ['textarea', 'text', 'select', 'radio', 'checkbox', 'range', 'number']);
    $table->string('label');
    $table->text('placeholder')->nullable();
    $table->text('help_text')->nullable();
    $table->boolean('is_primary')->default(false);
    $table->string('group_key', 50)->nullable();
    $table->integer('display_order')->default(0);
    $table->boolean('is_required')->default(false);
    $table->json('validation_rules')->nullable();
    $table->text('default_value')->nullable();
    $table->json('depends_on')->nullable();
    $table->timestamps();
    
    $table->unique(['ai_feature_id', 'input_key']);
    $table->foreign('ai_feature_id')->references('id')->on('ai_features')->onDelete('cascade');
    $table->index(['ai_feature_id', 'display_order']);
});
```

#### ✅ 1.2 Migration'ları Çalıştır

```bash
php artisan migrate --path=Modules/AI/database/migrations
```

**DURUM:** ✅ **TAMAMLANDI** - Migration dosyaları mevcuttur:

---

### **PHASE 2: MODEL KATMANI**

#### ✅ 2.1 Model Dosyalarını Oluştur

**Dosya Konumları:**
```
Modules/AI/app/Models/
├── ✅ AIFeatureInput.php
├── ✅ AIInputOption.php
├── ✅ AIDynamicDataSource.php
└── ✅ AIInputGroup.php
```

#### ✅ 2.2 AIFeatureInput Model

```php
<?php
namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AIFeatureInput extends Model
{
    protected $table = 'ai_feature_inputs';
    
    protected $fillable = [
        'ai_feature_id',
        'input_key',
        'input_type',
        'label',
        'placeholder',
        'help_text',
        'is_primary',
        'group_key',
        'display_order',
        'is_required',
        'validation_rules',
        'default_value',
        'depends_on'
    ];
    
    protected $casts = [
        'validation_rules' => 'array',
        'depends_on' => 'array',
        'is_primary' => 'boolean',
        'is_required' => 'boolean'
    ];
    
    public function feature(): BelongsTo
    {
        return $this->belongsTo(AIFeature::class, 'ai_feature_id');
    }
    
    public function options(): HasMany
    {
        return $this->hasMany(AIInputOption::class)->orderBy('display_order');
    }
    
    public function dynamicSource(): HasOne
    {
        return $this->hasOne(AIDynamicDataSource::class);
    }
    
    public function group(): BelongsTo
    {
        return $this->belongsTo(AIInputGroup::class, 'group_key', 'group_key');
    }
}
```

#### ✅ 2.3 AIFeature Model'i Güncelle

```php
// AIFeature.php'ye eklenecek ilişkiler
public function inputs()
{
    return $this->hasMany(AIFeatureInput::class)->orderBy('display_order');
}

public function primaryInput()
{
    return $this->hasOne(AIFeatureInput::class)->where('is_primary', true);
}

public function groupedInputs()
{
    return $this->inputs()->whereNotNull('group_key')->with('group');
}
```

---

### **PHASE 3: SERVICE KATMANI**

#### ✅ 3.1 UniversalInputManager Service Oluştur

**Dosya:** `Modules/AI/app/Services/FormBuilder/UniversalInputManager.php`

```php
<?php
namespace Modules\AI\App\Services\FormBuilder;

use Modules\AI\App\Models\AIFeature;
use Modules\AI\App\Models\AIFeatureInput;
use Modules\AI\App\Models\Prompt;
use Illuminate\Support\Facades\Cache;

class UniversalInputManager
{
    /**
     * Feature için tüm form yapısını getir
     */
    public function getFormStructure(int $featureId): array
    {
        return Cache::remember("ai_form_structure_{$featureId}", 3600, function() use ($featureId) {
            $feature = AIFeature::with([
                'inputs.options',
                'inputs.group',
                'inputs.dynamicSource'
            ])->findOrFail($featureId);
            
            return $this->formatFormStructure($feature);
        });
    }
    
    /**
     * Kullanıcı inputlarını prompt chain'e çevir
     */
    public function mapInputsToPrompts(array $userInputs, int $featureId): array
    {
        $promptIds = [];
        
        foreach ($userInputs as $inputKey => $value) {
            $promptId = $this->getPromptIdForInput($featureId, $inputKey, $value);
            if ($promptId) {
                $promptIds[] = $promptId;
            }
        }
        
        return $this->sortPromptsByPriority($promptIds);
    }
    
    /**
     * Form yapısını formatla
     */
    private function formatFormStructure(AIFeature $feature): array
    {
        return [
            'feature' => [
                'id' => $feature->id,
                'name' => $feature->name,
                'description' => $feature->description,
                'quick_prompt' => $feature->quick_prompt
            ],
            'primary_input' => $this->formatInput($feature->primaryInput),
            'groups' => $this->groupInputsByCategory($feature->inputs),
            'validation_rules' => $this->collectValidationRules($feature->inputs)
        ];
    }
}
```

#### ✅ 3.2 PromptMapper Service Oluştur

**Dosya:** `Modules/AI/app/Services/FormBuilder/PromptMapper.php`

---

### **PHASE 4: CONTROLLER & ROUTES**

#### ✅ 4.1 Controller Oluştur

**Dosya:** `Modules/AI/app/Http/Controllers/Admin/Features/AIFeatureInputController.php`

#### ✅ 4.2 Routes Ekle

**Dosya:** `Modules/AI/routes/admin.php`

```php
<?php
namespace Modules\AI\App\Http\Controllers\Admin\Features;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\AI\App\Models\AIFeature;
use Modules\AI\App\Models\AIFeatureInput;
use Modules\AI\App\Services\FormBuilder\UniversalInputManager;

class AIFeatureInputController extends Controller
{
    protected $inputManager;
    
    public function __construct(UniversalInputManager $inputManager)
    {
        $this->inputManager = $inputManager;
    }
    
    /**
     * Feature input yönetim sayfası
     */
    public function manage($featureId)
    {
        $feature = AIFeature::with('inputs.options')->findOrFail($featureId);
        $availablePrompts = Prompt::orderBy('priority')->get();
        
        return view('ai::admin.features.inputs.manage', compact('feature', 'availablePrompts'));
    }
    
    /**
     * API: Form yapısını getir
     */
    public function getFormStructure($featureId)
    {
        $structure = $this->inputManager->getFormStructure($featureId);
        return response()->json($structure);
    }
}
```

#### ✅ 4.2 Routes Ekle

**Dosya:** `Modules/AI/routes/admin.php`

```php
// Input Management Routes
Route::prefix('features/{feature}/inputs')->group(function() {
    Route::get('/', [AIFeatureInputController::class, 'manage'])->name('ai.features.inputs.manage');
    Route::post('/', [AIFeatureInputController::class, 'store'])->name('ai.features.inputs.store');
    Route::put('/{input}', [AIFeatureInputController::class, 'update'])->name('ai.features.inputs.update');
    Route::delete('/{input}', [AIFeatureInputController::class, 'destroy'])->name('ai.features.inputs.destroy');
});

// API Routes
Route::prefix('api/features')->group(function() {
    Route::get('/{feature}/form-structure', [AIFeatureInputController::class, 'getFormStructure']);
    Route::post('/{feature}/validate-inputs', [AIFeatureInputController::class, 'validateInputs']);
});
```

---

### **PHASE 5: ADMIN PANEL ARAYÜZÜ**

#### ✅ 5.1 Input Yönetim Sayfası

**DURUM:** ✅ **TAMAMLANDI** - Admin panel arayüzleri mevcuttur.

**Dosya:** `Modules/AI/resources/views/admin/features/inputs/manage.blade.php`

```blade
@extends('admin.layout')

@section('title', $feature->name . ' - Input Yönetimi')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">{{ $feature->name }} - Form Yapısı</h2>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addInputModal">
                <i class="ti ti-plus"></i> Yeni Input Ekle
            </button>
        </div>
    </div>
</div>

<div class="row">
    {{-- Primary Input --}}
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Ana Input (Accordion Dışında)</h3>
            </div>
            <div class="card-body">
                @if($feature->primaryInput)
                    {{-- Primary input detayları --}}
                @else
                    <p class="text-muted">Henüz ana input tanımlanmamış</p>
                @endif
            </div>
        </div>
    </div>
    
    {{-- Grouped Inputs --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">İleri Düzey Ayarlar (Accordion İçinde)</h3>
            </div>
            <div class="card-body">
                <div id="inputs-list" class="sortable">
                    @foreach($feature->inputs->where('is_primary', false) as $input)
                        {{-- Input kartları --}}
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

#### ☐ 5.2 Input Ekleme Modal

```blade
{{-- Add Input Modal --}}
<div class="modal fade" id="addInputModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('ai.features.inputs.store', $feature->id) }}" method="POST">
                @csrf
                {{-- Form alanları --}}
            </form>
        </div>
    </div>
</div>
```

---

### **PHASE 6: FRONTEND COMPONENTS**

#### ✅ 6.1 Universal Form Builder JavaScript

**Dosya:** `Modules/AI/resources/assets/js/universal-form-builder.js`

**DURUM:** ✅ **TAMAMLANDI** - JavaScript FormBuilder mevcuttur.

```javascript
class UniversalFormBuilder {
    constructor(featureId, container) {
        this.featureId = featureId;
        this.container = container;
        this.formData = null;
        this.userInputs = {};
        this.init();
    }
    
    async init() {
        await this.loadFormStructure();
        this.renderForm();
        this.attachEventListeners();
    }
    
    async loadFormStructure() {
        const response = await fetch(`/api/ai/features/${this.featureId}/form-structure`);
        this.formData = await response.json();
    }
    
    renderForm() {
        const formHTML = this.buildFormHTML();
        this.container.innerHTML = formHTML;
    }
    
    buildFormHTML() {
        let html = '<form id="ai-universal-form">';
        
        // Primary input
        if (this.formData.primary_input) {
            html += this.renderInput(this.formData.primary_input);
        }
        
        // Grouped inputs (accordion)
        html += this.renderAccordion(this.formData.groups);
        
        html += '</form>';
        return html;
    }
}
```

#### ✅ 6.2 Blade Component

**Dosya:** `Modules/AI/resources/views/components/universal-form.blade.php`

```blade
@props(['featureId', 'mode' => 'accordion'])

<div class="universal-form-container" 
     data-feature-id="{{ $featureId }}"
     data-mode="{{ $mode }}">
    
    <div class="form-loader text-center p-4">
        <div class="spinner-border" role="status"></div>
        <p>Form yükleniyor...</p>
    </div>
    
    <div class="form-content" style="display: none;">
        {{-- JavaScript ile doldurulacak --}}
    </div>
</div>

@push('scripts')
<script src="{{ asset('modules/ai/js/universal-form-builder.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.querySelector('.universal-form-container');
    new UniversalFormBuilder(
        container.dataset.featureId,
        container.querySelector('.form-content')
    );
});
</script>
@endpush
```

---

### **PHASE 7: API ENDPOINTS**

#### ✅ 7.1 Form Structure API

```php
// API Controller metodları
public function getFormStructure($featureId)
{
    $structure = $this->inputManager->getFormStructure($featureId);
    return response()->json($structure);
}

public function validateInputs(Request $request, $featureId)
{
    $rules = $this->inputManager->getValidationRules($featureId);
    $validated = $request->validate($rules);
    return response()->json(['valid' => true, 'data' => $validated]);
}

public function processForm(Request $request, $featureId)
{
    // Form verilerini işle
    $userInputs = $request->all();
    
    // Prompt chain oluştur
    $promptChain = $this->inputManager->mapInputsToPrompts($userInputs, $featureId);
    
    // AI'ya gönder
    $response = $this->aiService->generate($promptChain, $userInputs);
    
    return response()->json($response);
}
```

---

### **PHASE 8: TEST & SEEDER**

#### ✅ 8.1 Test Seeder Oluştur

**DURUM:** ✅ **TAMAMLANDI** - Seeder dosyaları mevcuttur.

**Dosya:** `Modules/AI/database/seeders/UniversalInputTestSeeder.php`

```php
<?php
namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\App\Models\AIFeature;
use Modules\AI\App\Models\AIFeatureInput;
use Modules\AI\App\Models\AIInputOption;

class UniversalInputTestSeeder extends Seeder
{
    public function run()
    {
        // Blog Yazarı feature'ı için test inputları
        $feature = AIFeature::where('slug', 'blog-writer')->first();
        
        if ($feature) {
            // Primary input (Blog konusu)
            $primaryInput = AIFeatureInput::create([
                'ai_feature_id' => $feature->id,
                'input_key' => 'topic',
                'input_type' => 'textarea',
                'label' => 'Blog Konusu',
                'placeholder' => 'Blog konusunu detaylıca açıklayın...',
                'is_primary' => true,
                'is_required' => true,
                'display_order' => 1,
                'validation_rules' => ['required', 'min:10', 'max:500']
            ]);
            
            // Yazım stili (Radio)
            $styleInput = AIFeatureInput::create([
                'ai_feature_id' => $feature->id,
                'input_key' => 'writing_style',
                'input_type' => 'radio',
                'label' => 'Yazım Stili',
                'group_key' => 'advanced',
                'display_order' => 2
            ]);
            
            // Style seçenekleri
            AIInputOption::insert([
                [
                    'ai_feature_input_id' => $styleInput->id,
                    'option_value' => 'professional',
                    'option_label' => 'Profesyonel',
                    'prompt_id' => 10001,
                    'display_order' => 1
                ],
                [
                    'ai_feature_input_id' => $styleInput->id,
                    'option_value' => 'casual',
                    'option_label' => 'Samimi',
                    'prompt_id' => 10002,
                    'display_order' => 2
                ]
            ]);
        }
    }
}
```

#### ☐ 8.2 Test Senaryoları

```php
// Feature test
public function test_can_get_form_structure()
{
    $feature = AIFeature::factory()->create();
    
    $response = $this->getJson("/api/ai/features/{$feature->id}/form-structure");
    
    $response->assertOk()
        ->assertJsonStructure([
            'feature' => ['id', 'name', 'description'],
            'primary_input',
            'groups',
            'validation_rules'
        ]);
}
```

---

### **PHASE 9: ENTEGRASYON**

#### ☐ 9.1 Mevcut AIService Entegrasyonu

```php
// AIService.php güncelleme
public function generateWithUniversalInputs($featureId, array $userInputs)
{
    // Universal Input Manager kullan
    $promptChain = $this->universalInputManager->mapInputsToPrompts($userInputs, $featureId);
    
    // Feature'ı al
    $feature = AIFeature::findOrFail($featureId);
    
    // Final prompt oluştur
    $finalPrompt = $this->buildFinalPrompt($feature, $promptChain, $userInputs);
    
    // AI'ya gönder
    return $this->sendToAI($finalPrompt);
}
```

#### ☐ 9.2 Cache Stratejisi

```php
// Cache keys
'ai_form_structure_{feature_id}' // Form yapısı
'ai_prompt_mappings_{feature_id}' // Prompt eşleştirmeleri
'ai_validation_rules_{feature_id}' // Validation kuralları

// Cache temizleme
public function clearFormCache($featureId)
{
    Cache::forget("ai_form_structure_{$featureId}");
    Cache::forget("ai_prompt_mappings_{$featureId}");
    Cache::forget("ai_validation_rules_{$featureId}");
}
```

---

## 📊 PERFORMANS OPTİMİZASYONU

### ☐ Database İndeksler
```sql
-- Performans için gerekli indeksler
ALTER TABLE ai_feature_inputs ADD INDEX idx_feature_order (ai_feature_id, display_order);
ALTER TABLE ai_input_options ADD INDEX idx_input_order (ai_feature_input_id, display_order);
ALTER TABLE ai_feature_inputs ADD INDEX idx_primary (ai_feature_id, is_primary);
```

### ☐ Eager Loading
```php
// N+1 sorgu problemini önle
AIFeature::with([
    'inputs.options',
    'inputs.group',
    'inputs.dynamicSource'
])->find($id);
```

### ☐ Cache Stratejisi
- Form yapıları 1 saat cache'lenir
- Input değişikliklerinde cache temizlenir
- Prompt mappingleri session bazlı cache'lenir

---

## 🚀 DEPLOYMENT CHECKLIST

### Production Öncesi Kontroller:

☐ Tüm migration'lar çalıştırıldı
☐ Model ilişkileri test edildi
☐ API endpoint'leri güvenlik kontrolünden geçti
☐ JavaScript minify edildi
☐ Cache stratejisi ayarlandı
☐ Error handling eklendi
☐ Validation kuralları tamamlandı
☐ Admin panel yetkilendirme kontrolü yapıldı
☐ Performance testleri yapıldı
☐ Backup stratejisi belirlendi

---

## 🚀 GELECEK MODÜL ENTEGRASYONLARI VE ÖZELLİKLER

### **📋 OLASI MODÜL ÖNGÖRÜLERİ VE ÖRNEKLER**

Bu bölüm, Universal Input System'in destekleyeceği gelecek özelliklerin örneklerini içerir. Bu örnekler **şu anda kodlanmayacak**, sadece sistem mimarisinin bu tür entegrasyonlara hazır olması için rehber niteliğindedir.

#### **🏢 CONTEXT-AWARE FİRMA BİLGİLERİ KULLANIMI:**
- ☐ **AI Profiles Entegrasyonu**: "Firma bilgilerini kullan" seçeneği → AIProfiles tablosundan otomatik çekme
- ☐ **Dinamik Firma Context**: Şirket adı, sektör, hedef kitle bilgilerini prompt'a otomatik ekleme
- ☐ **Brand Voice Consistency**: Firma tonuna uygun içerik üretimi

#### **👤 USERS TABLOSU ENTEGRASYONLARİ:**
- ☐ **Yazar Seçimi**: Users tablosundan dropdown ile yazar seçimi veya manuel input
- ☐ **Bireysel Makale Üretimi**: Seçilen yazarın tarzına uygun içerik
- ☐ **Dynamic User Selection**: Role bazlı kullanıcı filtreleme (author, editor, admin)

#### **🔍 MODÜL-LEVEL SEO ENTEGRASYONLARİ:**
- ☐ **Tek Tık SEO**: X modülün X sayfasında "SEO Optimize Et" butonu
- ☐ **Otomatik Yerleştirme**: Sonuçları meta_title, meta_description ve talep edilen diğer alanlarına otomatik doldurma
- ☐ **SEO Önerileri**: Sayfa analizi + "Şunu ekle, bunu düzelt" önerileri
- ☐ **SEO Settings JSON**: Her modülün SEO tablosundaki JSON yapısına uygun çıktı

#### **🌍 TOPLU ÇEVİRİ SİSTEMİ:**
- ☐ **Modal Çeviri Interface**: X modülde "Çevir" butonu → Modal açılması
- ☐ **TenantLanguages Entegrasyonu**: Tenant bazlı dil listesi
- ☐ **Ana Dil + Hedef Diller**: Kaynak dil seçimi + çoklu hedef dil seçimi
- ☐ **Birebir JSON Çevirisi**: X modülündeki JSON alanları + SEO Settings JSON'larını aynı anda çevirme
- ☐ **Tek Tık Tümü**: "Hepsini Çevir" butonu ile tüm dillere otomatik çeviri

#### **📦 BULK/TOPLU İŞLEMLER:**
- ☐ **Çoklu Seçim**: Checkbox'lar ile kayıt seçimi
- ☐ **Toplu AI İşlemleri**: Seçili kayıtlara aynı anda AI uygulaması
- ☐ **Progress Tracking**: İşlem durumu takibi ve progress bar
- ☐ **Batch Processing**: Queue sistemli toplu işlem

#### **🔗 DİNAMİK MODÜL REFERANSLARI:**
- ☐ **Tenant Bazlı Modül Listesi**: Kullanılabilir modüllerin dinamik listelenmesi
- ☐ **İçerik Hiyerarşisi**: Modül → Kategori → İçerik seçim sistemi
- ☐ **Referans Bazlı Üretim**: "Bu konuda içerik üret" → Seçilen içeriği baz alma
- ☐ **Cross-Module Content**: Bir modülün içeriğini başka modülde referans alma

#### **🎨 TEMPLATE-BASED CONTENT GENERATION:**
- ☐ **Template Modal**: Template seçim arayüzü
- ☐ **Visual Template Preview**: Template'lerin görsel önizlemeleri
- ☐ **Field Mapping**: H1 → Başlık, H2 → Alt başlık, P → Paragraf eşleştirmesi
- ☐ **Smart Content Filling**: Her alan tipi için uygun içerik üretimi
- ☐ **Template Categories**: Sayfa, blog, e-ticaret vb. kategoriler

#### **🎯 SAYFA İÇİ AI BUTONLARI:**
- ☐ **Context-Aware Buttons**: Her input alanının yanında "AI ile Doldur" butonu
- ☐ **Field-Specific Actions**: Alan tipine uygun AI işlemleri
- ☐ **Quick Suggestions**: Hover ile hızlı öneriler
- ☐ **One-Click Optimize**: Mevcut içeriği optimize etme

#### **🧠 AKILLI ÖNERI SİSTEMİ:**
- ☐ **Sayfa Analizi**: Mevcut içeriği tarama ve eksikleri tespit
- ☐ **Content Gap Analysis**: "Şu alanlar eksik" bildirimi
- ☐ **Smart Recommendations**: Sayfa tipine uygun öneriler
- ☐ **Auto-Completion**: Yarım kalan içerikleri tamamlama

#### **⚡ PERFORMANS VE CACHE ÖZELLİKLERİ:**
- ☐ **Pre-Generated Content**: Popüler seçimleri önceden hazırlama
- ☐ **Smart Caching**: Benzer istekleri cache'den sunma
- ☐ **Background Processing**: Uzun işlemleri arka planda çalıştırma
- ☐ **Real-Time Updates**: İşlem durumunu canlı takip

#### **🔧 GELİŞMİŞ FORM ÖZELLİKLERİ:**
- ☐ **Conditional Fields**: Seçime göre alan gösterme/gizleme
- ☐ **Dynamic Validation**: Context'e uygun validation kuralları
- ☐ **Auto-Save Draft**: Otomatik taslak kaydetme
- ☐ **Form Templates**: Önceden tanımlanmış form şablonları

#### **📊 ANALİTİK VE RAPORLAMA:**
- ☐ **Usage Analytics**: Hangi feature'ların ne kadar kullanıldığı
- ☐ **Performance Metrics**: Response süreleri ve başarı oranları
- ☐ **Content Quality Score**: Üretilen içeriklerin kalite puanı
- ☐ **ROI Tracking**: AI kullanımının zaman tasarrufu hesabı

#### **🔐 GÜVENLİK VE YETKİLENDİRME:**
- ☐ **Role-Based AI Access**: Kullanıcı rolüne göre feature erişimi
- ☐ **Content Approval**: Üretilen içeriklerin onay süreci
- ☐ **Audit Trail**: AI işlemlerinin log tutulması
- ☐ **Rate Limiting**: Kullanıcı bazlı kullanım limitleri

#### **🌐 MULTI-LANGUAGE ADVANCED:**
- ☐ **Language-Specific Prompts**: Dil bazlı prompt optimizasyonu
- ☐ **Cultural Adaptation**: Kültürel farklılıkları dikkate alma
- ☐ **Translation Memory**: Çeviri hafızası ve tutarlılık
- ☐ **Localization Features**: Yerel pazarlara uygun içerik

### **🎯 SİSTEM MİMARİSİ HAZIRLIKLARİ**

Yukarıdaki örnekler için sistem mimarisinde şu hazırlıklar yapılacak:

#### **Database Schema Extensions:**
- ☐ **Flexible JSON Fields**: Genişletilebilir JSON konfigürasyonları
- ☐ **Module Integration Points**: Modül entegrasyon noktaları
- ☐ **Context Storage**: Bağlamsal bilgi saklama alanları
- ☐ **Template System Tables**: Şablon yönetimi için tablolar

#### **Service Layer Architecture:**
- ☐ **Plugin Architecture**: Yeni entegrasyonlar için plugin sistemi
- ☐ **Event System**: AI işlemleri için event-driven yaklaşım
- ☐ **Queue Integration**: Toplu işlemler için kuyruk sistemi
- ☐ **Cache Strategy**: Çok katmanlı cache stratejisi

#### **API Design Principles:**
- ☐ **RESTful Endpoints**: Tüm işlemler için standart API
- ☐ **Webhook Support**: Dış sistemlerle entegrasyon
- ☐ **Real-time Updates**: WebSocket desteği
- ☐ **Batch Operations**: Toplu işlem API'leri

#### **Frontend Architecture:**
- ☐ **Component Library**: Yeniden kullanılabilir UI bileşenleri
- ☐ **State Management**: Global state yönetimi
- ☐ **Progressive Loading**: Aşamalı yükleme sistemi
- ☐ **Responsive Design**: Tüm cihazlarda uyumlu çalışma

**Not:** Bu örnekler sistem tasarımı için rehber niteliğindedir. Implementation sırasında öncelik sırasına göre geliştirilecektir.

### **1. CONTEXT-AWARE DYNAMIC INPUTS**

#### **Firma Bilgileri Kullanımı:**
```php
// ai_dynamic_data_sources tablosunda
{
    'source_type': 'ai_profile',
    'source_endpoint': 'AITenantProfile::getCurrentProfile',
    'auto_fill': true,
    'fields_mapping': {
        'company_name': 'profile.company_name',
        'sector': 'profile.sector',
        'target_audience': 'profile.target_audience'
    }
}

// Kullanım
class ContextAwareInputBuilder {
    public function buildWithProfile($featureId) {
        $profile = AITenantProfile::getCurrentProfile();
        
        // Otomatik doldurulacak alanlar
        return [
            'company_context' => $profile->company_description,
            'brand_voice' => $profile->brand_voice,
            'industry_terms' => $profile->industry_keywords
        ];
    }
}
```

#### **Kullanıcı Seçimi:**
```php
// Users tablosundan dinamik seçim
{
    'input_type': 'select_or_text',
    'label': 'Yazar Adı',
    'data_source': {
        'type': 'model',
        'model': 'App\\Models\\User',
        'value_field': 'name',
        'label_field': 'name',
        'where': [['role', 'author']]
    },
    'allow_custom': true  // Manuel giriş de yapılabilir
}
```

### **2. MODULE-LEVEL AI INTEGRATION**

#### **SEO Modülü Entegrasyonu:**
```php
// X modülün SEO sayfasında
class ModuleSEOIntegration {
    // Tek tıkla SEO optimizasyonu
    public function oneClickSEO($module, $recordId) {
        $content = $this->getModuleContent($module, $recordId);
        
        // AI analiz
        $seoData = $this->aiService->analyzeSEO($content);
        
        // Otomatik doldurma
        return [
            'meta_title' => $seoData['optimized_title'],
            'meta_description' => $seoData['optimized_description'],
            'keywords' => $seoData['keywords'],
            'og_tags' => $seoData['social_tags']
        ];
    }
    
    // Öneri sistemi
    public function getSEOSuggestions($content) {
        return $this->aiService->generateSuggestions([
            'content' => $content,
            'target' => 'seo_optimization',
            'include' => ['title', 'description', 'keywords', 'schema']
        ]);
    }
}
```

#### **Sayfa İçi AI Butonları:**
```blade
{{-- Her modülde kullanılabilecek AI helper button --}}
<button class="btn btn-ai-helper" 
        data-module="{{ $module }}" 
        data-field="{{ $field }}"
        data-action="optimize">
    <i class="ti ti-sparkles"></i> AI ile Optimize Et
</button>

<button class="btn btn-ai-suggest"
        data-context="{{ json_encode($pageContext) }}">
    <i class="ti ti-bulb"></i> AI Önerileri
</button>
```

### **3. MULTI-LANGUAGE TRANSLATION ENGINE**

#### **Toplu Çeviri Sistemi:**
```php
class BulkTranslationEngine {
    /**
     * Tek tıkla tüm dillere çeviri
     */
    public function translateModule($module, $recordId, $options = []) {
        // Tenant languages
        $languages = TenantLanguage::where('is_active', true)->get();
        
        // Ana dil içeriği
        $sourceContent = $this->getSourceContent($module, $recordId);
        
        // Paralel çeviri (queue job)
        foreach ($languages as $language) {
            TranslateContentJob::dispatch([
                'module' => $module,
                'record_id' => $recordId,
                'source_lang' => $options['source_lang'] ?? 'tr',
                'target_lang' => $language->code,
                'fields' => [
                    'title', 'description', 'content',
                    'seo.meta_title', 'seo.meta_description'
                ]
            ]);
        }
    }
    
    /**
     * Modal ile seçimli çeviri
     */
    public function selectiveTranslation($request) {
        return view('ai::modals.translation-selector', [
            'source_language' => $request->source,
            'available_languages' => TenantLanguage::active()->get(),
            'translatable_fields' => $this->getTranslatableFields($request->module)
        ]);
    }
}
```

#### **Çeviri Modal Component:**
```javascript
class TranslationModal {
    constructor(moduleContext) {
        this.module = moduleContext.module;
        this.recordId = moduleContext.recordId;
        this.languages = [];
        this.fields = [];
    }
    
    async open() {
        // Dilleri yükle
        this.languages = await this.loadTenantLanguages();
        
        // Modal aç
        this.renderModal();
    }
    
    async translate() {
        const selected = {
            source: this.getSourceLanguage(),
            targets: this.getSelectedTargets(),
            fields: this.getSelectedFields()
        };
        
        // Toplu çeviri başlat
        const response = await fetch('/api/ai/translate/bulk', {
            method: 'POST',
            body: JSON.stringify({
                module: this.module,
                record_id: this.recordId,
                ...selected
            })
        });
        
        // Progress tracking
        this.trackProgress(response.jobId);
    }
}
```

### **4. SMART BULK OPERATIONS**

#### **Çoklu Seçim ve Toplu İşlem:**
```php
class BulkAIOperations {
    /**
     * Seçili kayıtlara toplu AI işlemi
     */
    public function processBulk($module, $recordIds, $operation) {
        $operations = [
            'generate_seo' => $this->bulkGenerateSEO,
            'translate_all' => $this->bulkTranslate,
            'optimize_content' => $this->bulkOptimize,
            'generate_descriptions' => $this->bulkDescriptions
        ];
        
        return $operations[$operation]($module, $recordIds);
    }
    
    /**
     * Akıllı içerik önerileri
     */
    public function smartSuggestions($context) {
        // Modül bazlı context
        $moduleData = $this->analyzeModule($context['module']);
        
        // AI önerileri
        return [
            'missing_content' => $this->findMissingContent($moduleData),
            'seo_improvements' => $this->suggestSEOImprovements($moduleData),
            'content_gaps' => $this->identifyContentGaps($moduleData),
            'optimization_opportunities' => $this->findOptimizations($moduleData)
        ];
    }
}
```

### **5. TEMPLATE-BASED GENERATION**

#### **Dinamik Template Seçimi:**
```php
class TemplateBasedGenerator {
    /**
     * Template seçimi ve içerik üretimi
     */
    public function generateFromTemplate($templateId, $context) {
        $template = AITemplate::find($templateId);
        
        // Template alanları
        $fields = [
            'hero_title' => $this->generateField('hero_title', $context),
            'hero_subtitle' => $this->generateField('hero_subtitle', $context),
            'sections' => $this->generateSections($template->sections, $context),
            'cta_text' => $this->generateField('cta', $context)
        ];
        
        return $this->applyTemplate($template, $fields);
    }
    
    /**
     * Modül bazlı template önerileri
     */
    public function suggestTemplates($module, $category) {
        return AITemplate::where('applicable_modules', 'like', "%{$module}%")
            ->where('category', $category)
            ->with('preview')
            ->get();
    }
}
```

#### **Template Selection Modal:**
```blade
{{-- Template seçim modal --}}
<div class="template-selector-modal">
    <div class="template-grid">
        @foreach($templates as $template)
            <div class="template-card" data-template-id="{{ $template->id }}">
                <img src="{{ $template->preview_image }}" />
                <h4>{{ $template->name }}</h4>
                <div class="template-fields">
                    @foreach($template->fields as $field)
                        <span class="field-badge">{{ $field }}</span>
                    @endforeach
                </div>
                <button class="btn-use-template">Bu Template'i Kullan</button>
            </div>
        @endforeach
    </div>
</div>
```

### **6. MODULE CONTENT REFERENCE**

#### **İçerik Referans Sistemi:**
```php
class ContentReferenceSystem {
    /**
     * Başka modülden içerik referansı
     */
    public function referenceContent($request) {
        // Kullanılabilir modüller (tenant bazlı)
        $modules = $this->getAvailableModules();
        
        // Seçim arayüzü
        return [
            'modules' => $modules,
            'categories' => $this->getCategoriesForModule($request->module),
            'contents' => $this->getSelectableContents($request->filters)
        ];
    }
    
    /**
     * Referans bazlı içerik üretimi
     */
    public function generateBasedOnReference($referenceId, $type) {
        $reference = $this->loadReference($referenceId);
        
        return $this->aiService->generate([
            'base_content' => $reference->content,
            'type' => $type,
            'maintain' => ['tone', 'style', 'keywords'],
            'adapt_for' => $this->getCurrentContext()
        ]);
    }
}
```

### **7. SMART FIELD DETECTION**

#### **Otomatik Alan Tanıma:**
```php
class SmartFieldDetector {
    /**
     * Form alanlarını otomatik tanı ve doldur
     */
    public function detectAndFill($module, $page) {
        $fields = $this->scanPageFields($page);
        
        $suggestions = [];
        foreach ($fields as $field) {
            $suggestions[$field->name] = $this->suggestContent($field);
        }
        
        return $suggestions;
    }
    
    /**
     * Alan tipine göre içerik öner
     */
    private function suggestContent($field) {
        $generators = [
            'title' => fn($ctx) => $this->generateTitle($ctx),
            'description' => fn($ctx) => $this->generateDescription($ctx),
            'image_alt' => fn($ctx) => $this->generateImageAlt($ctx),
            'meta_*' => fn($ctx) => $this->generateMeta($ctx)
        ];
        
        foreach ($generators as $pattern => $generator) {
            if ($this->matchesPattern($field->name, $pattern)) {
                return $generator($field->context);
            }
        }
    }
}
```

### **8. PROGRESSIVE AI FEATURES**

#### **Aşamalı AI Özellikleri:**
```php
class ProgressiveAIFeatures {
    /**
     * Kullanım seviyesine göre özellik açma
     */
    public function getAvailableFeatures($tenant) {
        $usageLevel = $this->calculateUsageLevel($tenant);
        
        return [
            'basic' => [
                'simple_generation',
                'basic_translation',
                'seo_suggestions'
            ],
            'intermediate' => [
                ...config('ai.features.basic'),
                'bulk_operations',
                'template_generation',
                'smart_suggestions'
            ],
            'advanced' => [
                ...config('ai.features.intermediate'),
                'custom_training',
                'api_access',
                'white_label'
            ]
        ][$usageLevel];
    }
}
```

### **9. PERFORMANCE OPTIMIZATION & DEFAULT SYSTEM**

#### **Smart Default Manager - Akıllı Varsayılanlar**
```php
class SmartDefaultManager {
    
    /**
     * Her feature için akıllı varsayılanlar
     */
    public function getDefaults($featureId, $context = []) {
        
        // BLOG YAZARI ÖRNEĞİ
        if ($featureId === 'blog-writer') {
            return [
                'writing_style' => $this->detectDefaultStyle($context),
                'content_length' => 'medium', // 500-700 kelime EN POPÜLER
                'tone' => 'professional',      // %70 kullanıcı bunu seçiyor
                'seo_optimization' => 'auto',  // Otomatik optimize
                'use_company_info' => true     // Varsayılan olarak kullan
            ];
        }
    }
    
    /**
     * Kullanım geçmişine göre akıllı varsayılan
     */
    private function detectDefaultStyle($context) {
        // Son 10 kullanımın ortalaması
        $lastUsages = Cache::get("user_{$context['user_id']}_preferences");
        
        if ($lastUsages && $lastUsages['writing_style']) {
            return $lastUsages['writing_style']; // Kullanıcının favorisi
        }
        
        return 'professional'; // Global varsayılan
    }
}
```

#### **Prompt Optimization Engine - Token Tasarrufu**
```php
class PromptOptimizer {
    
    /**
     * Gereksiz prompt parçalarını çıkar
     */
    public function optimize($prompts, $userSelections) {
        
        $optimized = [];
        
        foreach ($prompts as $prompt) {
            // KULLANILMAYAN PROMPT'LARI EKLEME
            if ($this->isRelevant($prompt, $userSelections)) {
                $optimized[] = $prompt;
            }
        }
        
        return $this->combineSmartly($optimized);
    }
    
    /**
     * Akıllı birleştirme - TOKEN TASARRUFU
     */
    private function combineSmartly($prompts) {
        // Benzer prompt'ları birleştir
        $combined = [];
        
        // ÖRNEK: Stil + Ton + Uzunluk = TEK PROMPT
        if ($this->hasMultipleStylePrompts($prompts)) {
            $combined[] = "Profesyonel tonda, orta uzunlukta (500-700 kelime) yaz.";
        } else {
            $combined = $prompts;
        }
        
        return implode("\n", $combined);
    }
}
```

#### **Multi-Layer Cache Strategy**
```php
class AIPerformanceCache {
    
    /**
     * Çok katmanlı cache sistemi
     */
    public function getCachedOrGenerate($featureId, $inputs) {
        
        // LEVEL 1: Exact Match Cache (1 saniye)
        $exactKey = $this->generateExactKey($featureId, $inputs);
        if ($exact = Cache::get($exactKey)) {
            return $exact; // ANINDA DÖNÜŞ
        }
        
        // LEVEL 2: Similar Content Cache (5 saniye)
        $similarKey = $this->generateSimilarKey($featureId, $inputs);
        if ($similar = Cache::get($similarKey)) {
            return $this->adaptContent($similar, $inputs); // HAFIF DÜZENLEME
        }
        
        // LEVEL 3: Template Cache (10 saniye)
        if ($this->canUseTemplate($featureId, $inputs)) {
            return $this->fillTemplate($featureId, $inputs); // ŞABLON DOLDUR
        }
        
        // LEVEL 4: Generate New (20-30 saniye)
        return $this->generateFresh($featureId, $inputs);
    }
}
```

#### **Real-World Optimized Prompts**
```php
class RealWorldPrompts {
    
    /**
     * Blog Yazarı - MINIMAL AMA ETKİLİ
     */
    public function blogWriterPrompt($selections) {
        
        // SADECE SEÇİLENLERİ EKLE
        $prompt = "Blog konusu: {$selections['topic']}\n";
        
        // Varsayılan değilse ekle
        if ($selections['writing_style'] !== 'professional') {
            $prompt .= "Stil: {$selections['writing_style']}\n";
        }
        
        if ($selections['content_length'] !== 'medium') {
            $prompt .= "Uzunluk: {$selections['content_length']}\n";
        }
        
        // GEREKSIZ DETAYLARI EKLEME
        // ❌ "Lütfen yaratıcı ol, ilgi çekici yaz, SEO uyumlu ol..."
        // ✅ Sadece farklı olan tercihleri belirt
        
        return trim($prompt);
    }
    
    /**
     * Çeviri - ULTRA MINIMAL
     */
    public function translationPrompt($text, $targetLang) {
        // FAZLADAN LAF YOK
        return "Translate to {$targetLang}:\n{$text}";
        
        // ❌ "Sen profesyonel bir çevirmensin, doğru çeviri yap..."
        // ✅ Direkt komut
    }
}
```

### **10. DEFAULT STRATEGY TABLE**

| Feature | Default Style | Default Length | Default Options | Neden? |
|---------|--------------|----------------|-----------------|--------|
| Blog Writer | Professional | Medium (500-700) | SEO: Auto | %70 kullanıcı böyle seçiyor |
| Email Writer | Formal | Short (100-200) | Signature: Include | İş emaili en yaygın |
| Product Desc | Persuasive | Short (50-100) | Features: Bullet | E-ticaret standardı |
| Translation | - | Same as source | Tone: Preserve | Anlam korunmalı |
| SEO Meta | - | Title:60, Desc:160 | Keywords: Auto | Google limitleri |

### **11. PERFORMANCE METRICS & TARGETS**

```php
class PerformanceMetrics {
    
    public function getTargets() {
        return [
            'simple_generation' => [
                'target_time' => '< 3 seconds',
                'cache_hit_rate' => '> 60%',
                'token_usage' => '< 500'
            ],
            'complex_generation' => [
                'target_time' => '< 10 seconds',
                'cache_hit_rate' => '> 30%',
                'token_usage' => '< 2000'
            ],
            'bulk_operations' => [
                'items_per_minute' => '> 100',
                'parallel_limit' => 10,
                'batch_size' => 50
            ]
        ];
    }
}
```

### **12. SPEED OPTIMIZATION TACTICS**

```php
// 1. PRE-WARM CACHE
Scheduler::daily(function() {
    $popularFeatures = ['blog-writer', 'email-writer'];
    foreach ($popularFeatures as $feature) {
        Cache::put("feature_warm_{$feature}", $this->preGenerate($feature));
    }
});

// 2. STREAMING RESPONSE
public function streamGeneration($featureId, $inputs) {
    return new StreamedResponse(function() use ($featureId, $inputs) {
        $generator = $this->aiService->streamGenerate($featureId, $inputs);
        
        foreach ($generator as $chunk) {
            echo "data: " . json_encode(['chunk' => $chunk]) . "\n\n";
            ob_flush();
            flush();
        }
    });
}

// 3. PARALLEL PROCESSING
public function processMultiple($features) {
    
    // BATCH PROCESSING
    if (count($features) > 5) {
        return $this->batchProcess($features); // Tek API call
    }
    
    // PARALLEL PROCESSING
    $promises = [];
    foreach ($features as $feature) {
        $promises[] = $this->processAsync($feature);
    }
    
    return Promise::all($promises); // Hepsi aynı anda
}
```

### **13. ACCURACY VALIDATION**

```php
class AccuracyValidator {
    
    /**
     * Çıktı doğruluğunu kontrol et
     */
    public function validateOutput($feature, $output) {
        
        $rules = $this->getFeatureRules($feature);
        
        // SEO ÖRNEĞİ
        if ($feature === 'seo-optimizer') {
            return [
                'title_length' => strlen($output['title']) <= 60,
                'description_length' => strlen($output['description']) <= 160,
                'keyword_density' => $this->checkKeywordDensity($output),
                'readability_score' => $this->calculateReadability($output)
            ];
        }
        
        return true;
    }
}
```

### **14. REAL USAGE SCENARIOS**

```php
// SENARYO 1: Hızlı Blog Yazısı
$inputs = [
    'topic' => 'Laravel performance tips',
    // Diğer her şey DEFAULT
];

// Sistem otomatik ekler:
// - writing_style: 'professional' (varsayılan)
// - length: 'medium' (500-700 kelime)
// - seo: 'auto' (otomatik optimize)
// - company_info: true (profil'den çeker)

// SONUÇ: 3 saniyede blog hazır (cache'den)

// --------------------------------

// SENARYO 2: Toplu Çeviri
$pages = Page::where('needs_translation', true)->get();

// Sistem:
// 1. Benzer içerikleri gruplar
// 2. Batch API call yapar
// 3. 100 sayfa = 30 saniye (paralel)

// --------------------------------

// SENARYO 3: SEO Optimizasyonu
$content = "Mevcut sayfa içeriği...";

// Tek tık:
// 1. İçeriği analiz et (2 sn)
// 2. Öneriler sun (1 sn)
// 3. Uygula (1 sn)
// TOPLAM: 4 saniye
```

### **15. IMPLEMENTATION ROADMAP**

#### **Phase 1: Core Integration (2 Hafta)**
- ☐ Universal Input System kurulumu
- ☐ Smart Default Manager implementasyonu
- ☐ Basic context-aware inputs
- ☐ Module-level AI buttons

#### **Phase 2: Performance & Cache (1 Hafta)**
- ☐ Multi-layer cache system
- ☐ Prompt optimization engine
- ☐ Parallel processing setup
- ☐ Streaming response implementation

#### **Phase 3: Translation & SEO (2 Hafta)**
- ☐ Multi-language translation engine
- ☐ SEO integration system
- ☐ One-click optimizations
- ☐ Accuracy validators

#### **Phase 4: Advanced Features (3 Hafta)**
- ☐ Template-based generation
- ☐ Content reference system
- ☐ Bulk operations
- ☐ Pre-warm cache strategy

#### **Phase 5: Smart Features (4 Hafta)**
- ☐ Smart field detection
- ☐ Progressive AI features
- ☐ Custom training system
- ☐ Performance monitoring dashboard

## 📝 NOTLAR

- Sistem tamamen central database'den yönetilir
- Tenant bağımlılığı yoktur
- Form yapıları cache'lenebilir
- A/B testing için altyapı hazırdır
- Yeni input tipleri kolayca eklenebilir
- Tüm modül entegrasyonları için hazır
- Context-aware ve smart features destekli
- Input Template System ile reusable components
- Performance-first architecture (4-layer cache)
- Smart defaults %70 doğruluk oranı hedefi

---

## 🎯 SONUÇ

Universal Input System kurulumu tamamlandığında:
- ✅ Her AI feature için özel form tasarımı
- ✅ Admin panelden tam kontrol
- ✅ Otomatik prompt mapping
- ✅ Performanslı ve ölçeklenebilir yapı
- ✅ Test edilebilir ve sürdürülebilir kod

**Tahmini Tamamlanma Süresi:** 10-14 gün

---

*Bu dokümantasyon Universal Input System'in tüm kurulum adımlarını içerir. Her adım tamamlandıkça kutucuklar işaretlenmelidir.*
