# 🚀 UNIVERSAL INPUT SYSTEM V3 PROFESSIONAL - KATI YOL HARİTASI

**VERSİYON:** 3.0 - Full Database-Driven Professional System
**TARİH:** 10.08.2025
**HEDEF:** Enterprise-Level AI Integration

---

## 📊 PHASE 1: VERİTABANI ALTYAPISI (ZORUNLU - İLK ADIM)

### **1.1 MEVCUT TABLOLARI GÜNCELLE**

#### ❗ **ai_features tablosu - GÜNCELLE**
```sql
ALTER TABLE ai_features ADD COLUMN `module_type` VARCHAR(50) DEFAULT NULL COMMENT 'blog, page, email, seo, translation';
ALTER TABLE ai_features ADD COLUMN `category` VARCHAR(100) DEFAULT NULL COMMENT 'content_generation, optimization, translation';
ALTER TABLE ai_features ADD COLUMN `supported_modules` JSON DEFAULT NULL COMMENT '["page", "blog", "portfolio"]';
ALTER TABLE ai_features ADD COLUMN `context_rules` JSON DEFAULT NULL COMMENT 'Module ve context bazlı kurallar';
ALTER TABLE ai_features ADD COLUMN `template_support` BOOLEAN DEFAULT FALSE;
ALTER TABLE ai_features ADD COLUMN `bulk_support` BOOLEAN DEFAULT FALSE;
ALTER TABLE ai_features ADD COLUMN `streaming_support` BOOLEAN DEFAULT FALSE;
ALTER TABLE ai_features ADD INDEX idx_module_category (module_type, category);
```

#### ❗ **ai_prompts tablosu - GÜNCELLE**
```sql
ALTER TABLE ai_prompts ADD COLUMN `prompt_type` ENUM('system', 'tone', 'length', 'style', 'context', 'template') DEFAULT 'system';
ALTER TABLE ai_prompts ADD COLUMN `module_specific` VARCHAR(50) DEFAULT NULL COMMENT 'Hangi modül için özel';
ALTER TABLE ai_prompts ADD COLUMN `context_conditions` JSON DEFAULT NULL COMMENT 'Bu prompt ne zaman kullanılır';
ALTER TABLE ai_prompts ADD COLUMN `variables` JSON DEFAULT NULL COMMENT '["company_name", "user_name", "module_type"]';
ALTER TABLE ai_prompts ADD COLUMN `is_chainable` BOOLEAN DEFAULT TRUE COMMENT 'Diğer promptlarla birleştirilebilir mi';
ALTER TABLE ai_prompts ADD INDEX idx_prompt_type_module (prompt_type, module_specific);
```

### **1.2 YENİ TABLOLAR OLUŞTUR**

#### ✅ **ai_prompt_templates - YENİ**
```sql
CREATE TABLE `ai_prompt_templates` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `template_key` VARCHAR(100) UNIQUE NOT NULL,
    `template_name` VARCHAR(255) NOT NULL,
    `template_type` ENUM('feature', 'module', 'page', 'component') DEFAULT 'feature',
    `module_type` VARCHAR(50) DEFAULT NULL,
    `category` VARCHAR(100) DEFAULT NULL,
    `template_structure` JSON NOT NULL COMMENT 'Template alan yapısı',
    `field_mappings` JSON NOT NULL COMMENT 'Hangi alan nereye map edilecek',
    `prompt_chain` JSON DEFAULT NULL COMMENT 'Kullanılacak prompt ID listesi',
    `preview_image` VARCHAR(500) DEFAULT NULL,
    `example_output` TEXT DEFAULT NULL,
    `min_fields` INT DEFAULT 1,
    `max_fields` INT DEFAULT 20,
    `is_active` BOOLEAN DEFAULT TRUE,
    `usage_count` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_template_type_module (template_type, module_type),
    INDEX idx_category_active (category, is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### ✅ **ai_context_rules - YENİ**
```sql
CREATE TABLE `ai_context_rules` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `rule_key` VARCHAR(100) UNIQUE NOT NULL,
    `rule_name` VARCHAR(255) NOT NULL,
    `rule_type` ENUM('module', 'user', 'time', 'content', 'language') DEFAULT 'module',
    `conditions` JSON NOT NULL COMMENT 'Koşullar: {"module": "blog", "user_role": "author"}',
    `actions` JSON NOT NULL COMMENT 'Uygulanacak değişiklikler',
    `prompt_modifiers` JSON DEFAULT NULL COMMENT 'Prompt değişiklikleri',
    `priority` INT DEFAULT 100,
    `is_active` BOOLEAN DEFAULT TRUE,
    `applies_to` JSON DEFAULT NULL COMMENT 'Hangi feature_ids için geçerli',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_rule_type_priority (rule_type, priority),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### ✅ **ai_module_integrations - YENİ**
```sql
CREATE TABLE `ai_module_integrations` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `module_name` VARCHAR(50) NOT NULL,
    `integration_type` ENUM('button', 'modal', 'inline', 'bulk', 'api') DEFAULT 'button',
    `target_field` VARCHAR(100) DEFAULT NULL COMMENT 'Hangi alan için',
    `target_action` VARCHAR(100) NOT NULL COMMENT 'generate, optimize, translate, analyze',
    `button_config` JSON DEFAULT NULL COMMENT 'Buton ayarları',
    `modal_config` JSON DEFAULT NULL COMMENT 'Modal ayarları',
    `features_available` JSON NOT NULL COMMENT 'Bu modülde kullanılabilir feature_ids',
    `context_data` JSON DEFAULT NULL COMMENT 'Modül context bilgileri',
    `permissions` JSON DEFAULT NULL COMMENT 'Yetki gereksinimleri',
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_module_field_action (module_name, target_field, target_action),
    INDEX idx_module_active (module_name, is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### ✅ **ai_bulk_operations - YENİ**
```sql
CREATE TABLE `ai_bulk_operations` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `operation_uuid` VARCHAR(36) UNIQUE NOT NULL,
    `operation_type` VARCHAR(50) NOT NULL COMMENT 'bulk_translate, bulk_seo, bulk_optimize',
    `module_name` VARCHAR(50) NOT NULL,
    `record_ids` JSON NOT NULL COMMENT 'İşlenecek kayıt ID listesi',
    `options` JSON DEFAULT NULL COMMENT 'İşlem seçenekleri',
    `status` ENUM('pending', 'processing', 'completed', 'failed', 'partial') DEFAULT 'pending',
    `progress` INT DEFAULT 0 COMMENT 'Yüzde olarak ilerleme',
    `total_items` INT NOT NULL,
    `processed_items` INT DEFAULT 0,
    `success_items` INT DEFAULT 0,
    `failed_items` INT DEFAULT 0,
    `results` JSON DEFAULT NULL COMMENT 'İşlem sonuçları',
    `error_log` JSON DEFAULT NULL COMMENT 'Hata kayıtları',
    `started_at` TIMESTAMP NULL DEFAULT NULL,
    `completed_at` TIMESTAMP NULL DEFAULT NULL,
    `created_by` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status_module (status, module_name),
    INDEX idx_created_by (created_by),
    INDEX idx_operation_type (operation_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### ✅ **ai_translation_mappings - YENİ**
```sql
CREATE TABLE `ai_translation_mappings` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `module_name` VARCHAR(50) NOT NULL,
    `table_name` VARCHAR(100) NOT NULL,
    `translatable_fields` JSON NOT NULL COMMENT 'Çevrilecek alanlar listesi',
    `json_fields` JSON DEFAULT NULL COMMENT 'JSON tipindeki alanlar',
    `seo_fields` JSON DEFAULT NULL COMMENT 'SEO alanları mapping',
    `field_types` JSON NOT NULL COMMENT 'Alan tipleri: text, html, json',
    `max_lengths` JSON DEFAULT NULL COMMENT 'Alan maksimum uzunlukları',
    `special_rules` JSON DEFAULT NULL COMMENT 'Özel çeviri kuralları',
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_module_table (module_name, table_name),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### ✅ **ai_user_preferences - YENİ**
```sql
CREATE TABLE `ai_user_preferences` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `feature_id` BIGINT UNSIGNED DEFAULT NULL,
    `preference_key` VARCHAR(100) NOT NULL,
    `preference_value` JSON NOT NULL,
    `last_used_values` JSON DEFAULT NULL COMMENT 'Son kullanılan değerler',
    `usage_count` INT DEFAULT 0,
    `favorite_prompts` JSON DEFAULT NULL,
    `custom_templates` JSON DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_feature_key (user_id, feature_id, preference_key),
    INDEX idx_user_feature (user_id, feature_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### ✅ **ai_usage_analytics - YENİ**
```sql
CREATE TABLE `ai_usage_analytics` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `feature_id` BIGINT UNSIGNED NOT NULL,
    `module_name` VARCHAR(50) DEFAULT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `action_type` VARCHAR(50) NOT NULL,
    `input_data` JSON DEFAULT NULL,
    `output_data` JSON DEFAULT NULL,
    `prompt_chain_used` JSON DEFAULT NULL,
    `tokens_used` INT DEFAULT 0,
    `response_time_ms` INT DEFAULT 0,
    `cache_hit` BOOLEAN DEFAULT FALSE,
    `success` BOOLEAN DEFAULT TRUE,
    `error_message` TEXT DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_feature_user (feature_id, user_id),
    INDEX idx_module_action (module_name, action_type),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### ✅ **ai_prompt_cache - YENİ**
```sql
CREATE TABLE `ai_prompt_cache` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `cache_key` VARCHAR(255) UNIQUE NOT NULL,
    `feature_id` BIGINT UNSIGNED DEFAULT NULL,
    `input_hash` VARCHAR(64) NOT NULL,
    `prompt_text` TEXT NOT NULL,
    `response_text` TEXT DEFAULT NULL,
    `metadata` JSON DEFAULT NULL,
    `hit_count` INT DEFAULT 0,
    `last_accessed` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `expires_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_cache_key_expires (cache_key, expires_at),
    INDEX idx_feature_hash (feature_id, input_hash),
    INDEX idx_last_accessed (last_accessed)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 📦 PHASE 2: SERVICE LAYER (ZORUNLU)

### **2.1 YENİ SERVICE SINIFLAR OLUŞTUR**

#### ✅ **UniversalInputManager Service**
```php
// Modules/AI/app/Services/Universal/UniversalInputManager.php
namespace Modules\AI\App\Services\Universal;

class UniversalInputManager
{
    // ZORUNLU METODLAR:
    public function getFormStructure(int $featureId, array $context = []): array;
    public function buildDynamicInputs(int $featureId, string $moduleType): array;
    public function applyContextRules(array $inputs, array $context): array;
    public function mapInputsToPrompts(array $userInputs, int $featureId): array;
    public function validateInputs(array $inputs, int $featureId): array;
    public function saveUserPreferences(int $userId, int $featureId, array $inputs): void;
    public function getSmartDefaults(int $userId, int $featureId): array;
}
```

#### ✅ **PromptChainBuilder Service**
```php
// Modules/AI/app/Services/Prompts/PromptChainBuilder.php
namespace Modules\AI\App\Services\Prompts;

class PromptChainBuilder
{
    // ZORUNLU METODLAR:
    public function buildChain(int $featureId, array $context): array;
    public function addSystemPrompts(array $chain): array;
    public function addContextPrompts(array $chain, string $moduleType): array;
    public function addUserPrompts(array $chain, array $userInputs): array;
    public function addTemplatePrompts(array $chain, int $templateId): array;
    public function optimizeChain(array $chain): array; // Duplicate temizleme
    public function sortByPriority(array $chain): array;
}
```

#### ✅ **ContextAwareEngine Service**
```php
// Modules/AI/app/Services/Context/ContextAwareEngine.php
namespace Modules\AI\App\Services\Context;

class ContextAwareEngine
{
    // ZORUNLU METODLAR:
    public function detectContext(array $request): array;
    public function getModuleContext(string $moduleName): array;
    public function getUserContext(int $userId): array;
    public function getTimeContext(): array; // Sabah/akşam, hafta içi/sonu
    public function getContentContext(string $content): array; // Mevcut içerik analizi
    public function applyRules(array $context): array;
    public function getRecommendations(array $context): array;
}
```

#### ✅ **BulkOperationProcessor Service**
```php
// Modules/AI/app/Services/Bulk/BulkOperationProcessor.php
namespace Modules\AI\App\Services\Bulk;

class BulkOperationProcessor
{
    // ZORUNLU METODLAR:
    public function createOperation(string $type, array $recordIds, array $options): string;
    public function processQueue(): void; // Queue job olarak çalışacak
    public function updateProgress(string $operationId, int $progress): void;
    public function handleFailure(string $operationId, int $recordId, string $error): void;
    public function getOperationStatus(string $operationId): array;
    public function cancelOperation(string $operationId): bool;
}
```

#### ✅ **TranslationEngine Service**
```php
// Modules/AI/app/Services/Translation/TranslationEngine.php
namespace Modules\AI\App\Services\Translation;

class TranslationEngine
{
    // ZORUNLU METODLAR:
    public function translateRecord(string $module, int $recordId, array $languages): array;
    public function translateField(string $text, string $fromLang, string $toLang): string;
    public function translateJSON(array $json, string $fromLang, string $toLang): array;
    public function bulkTranslate(array $records, array $languages): array;
    public function getTranslatableFields(string $module): array;
    public function preserveFormatting(string $text): array; // HTML/Markdown korunur
}
```

#### ✅ **TemplateGenerator Service**
```php
// Modules/AI/app/Services/Templates/TemplateGenerator.php
namespace Modules\AI\App\Services\Templates;

class TemplateGenerator
{
    // ZORUNLU METODLAR:
    public function getAvailableTemplates(string $moduleType, string $category): array;
    public function generateFromTemplate(int $templateId, array $context): array;
    public function mapFieldsToTemplate(array $fields, int $templateId): array;
    public function previewTemplate(int $templateId): string;
    public function createCustomTemplate(array $structure): int;
    public function cloneTemplate(int $templateId, array $modifications): int;
}
```

#### ✅ **SmartAnalyzer Service**
```php
// Modules/AI/app/Services/Analysis/SmartAnalyzer.php
namespace Modules\AI\App\Services\Analysis;

class SmartAnalyzer
{
    // ZORUNLU METODLAR:
    public function analyzePage(string $module, int $recordId): array;
    public function getSEOScore(string $content): array;
    public function getReadabilityScore(string $content): array;
    public function findMissingElements(array $data): array;
    public function suggestImprovements(array $analysis): array;
    public function compareWithCompetitors(string $content, string $keyword): array;
}
```

#### ✅ **ModuleIntegrationManager Service**
```php
// Modules/AI/app/Services/Integration/ModuleIntegrationManager.php
namespace Modules\AI\App\Services\Integration;

class ModuleIntegrationManager
{
    // ZORUNLU METODLAR:
    public function registerModule(string $moduleName, array $config): void;
    public function getModuleButtons(string $moduleName, string $fieldName): array;
    public function injectAIHelpers(string $moduleName, string $viewPath): string;
    public function getAvailableFeatures(string $moduleName): array;
    public function executeModuleAction(string $module, string $action, array $params): mixed;
}
```

---

## 🎯 PHASE 3: CONTROLLER & ROUTES (ZORUNLU)

### **3.1 YENİ CONTROLLER'LAR OLUŞTUR**

#### ✅ **UniversalInputController**
```php
// Modules/AI/app/Http/Controllers/Admin/Universal/UniversalInputController.php
namespace Modules\AI\App\Http\Controllers\Admin\Universal;

class UniversalInputController extends Controller
{
    // ZORUNLU METODLAR:
    public function getFormStructure(Request $request, $featureId);
    public function submitForm(Request $request, $featureId);
    public function getSmartDefaults(Request $request, $featureId);
    public function savePreferences(Request $request);
    public function validateInputs(Request $request);
}
```

#### ✅ **BulkOperationController**
```php
// Modules/AI/app/Http/Controllers/Admin/Bulk/BulkOperationController.php
namespace Modules\AI\App\Http\Controllers\Admin\Bulk;

class BulkOperationController extends Controller
{
    // ZORUNLU METODLAR:
    public function createBulkOperation(Request $request);
    public function getOperationStatus($operationId);
    public function cancelOperation($operationId);
    public function getOperationHistory(Request $request);
    public function retryFailedItems($operationId);
}
```

#### ✅ **ModuleIntegrationController**
```php
// Modules/AI/app/Http/Controllers/Admin/Integration/ModuleIntegrationController.php
namespace Modules\AI\App\Http\Controllers\Admin\Integration;

class ModuleIntegrationController extends Controller
{
    // ZORUNLU METODLAR:
    public function getModuleConfig($moduleName);
    public function updateModuleConfig(Request $request, $moduleName);
    public function getAvailableActions($moduleName, $fieldName);
    public function executeAction(Request $request);
    public function getFieldSuggestions(Request $request);
}
```

### **3.2 ROUTE TANIMLAMALARI**

```php
// Modules/AI/routes/admin.php - EKLENECEK

// Universal Input System Routes
Route::prefix('universal')->group(function() {
    Route::get('/form-structure/{featureId}', [UniversalInputController::class, 'getFormStructure']);
    Route::post('/submit/{featureId}', [UniversalInputController::class, 'submitForm']);
    Route::get('/defaults/{featureId}', [UniversalInputController::class, 'getSmartDefaults']);
    Route::post('/preferences', [UniversalInputController::class, 'savePreferences']);
    Route::post('/validate', [UniversalInputController::class, 'validateInputs']);
});

// Bulk Operations Routes
Route::prefix('bulk')->group(function() {
    Route::post('/create', [BulkOperationController::class, 'createBulkOperation']);
    Route::get('/status/{operationId}', [BulkOperationController::class, 'getOperationStatus']);
    Route::post('/cancel/{operationId}', [BulkOperationController::class, 'cancelOperation']);
    Route::get('/history', [BulkOperationController::class, 'getOperationHistory']);
    Route::post('/retry/{operationId}', [BulkOperationController::class, 'retryFailedItems']);
});

// Module Integration Routes
Route::prefix('integration')->group(function() {
    Route::get('/module/{moduleName}', [ModuleIntegrationController::class, 'getModuleConfig']);
    Route::put('/module/{moduleName}', [ModuleIntegrationController::class, 'updateModuleConfig']);
    Route::get('/actions/{moduleName}/{fieldName}', [ModuleIntegrationController::class, 'getAvailableActions']);
    Route::post('/execute', [ModuleIntegrationController::class, 'executeAction']);
    Route::post('/suggestions', [ModuleIntegrationController::class, 'getFieldSuggestions']);
});

// Template Routes
Route::prefix('templates')->group(function() {
    Route::get('/list', [TemplateController::class, 'listTemplates']);
    Route::get('/preview/{templateId}', [TemplateController::class, 'previewTemplate']);
    Route::post('/generate/{templateId}', [TemplateController::class, 'generateFromTemplate']);
    Route::post('/create', [TemplateController::class, 'createCustomTemplate']);
});

// Translation Routes
Route::prefix('translation')->group(function() {
    Route::post('/translate', [TranslationController::class, 'translateContent']);
    Route::post('/bulk-translate', [TranslationController::class, 'bulkTranslate']);
    Route::get('/languages', [TranslationController::class, 'getAvailableLanguages']);
    Route::get('/fields/{module}', [TranslationController::class, 'getTranslatableFields']);
});

// Analytics Routes
Route::prefix('analytics')->group(function() {
    Route::get('/usage/{featureId}', [AnalyticsController::class, 'getUsageStats']);
    Route::get('/performance', [AnalyticsController::class, 'getPerformanceMetrics']);
    Route::get('/popular-features', [AnalyticsController::class, 'getPopularFeatures']);
    Route::get('/user-preferences/{userId}', [AnalyticsController::class, 'getUserPreferences']);
});
```

---

## 🔧 PHASE 4: QUEUE JOBS (ZORUNLU)

### **4.1 YENİ JOB SINIFLAR OLUŞTUR**

```bash
php artisan make:job ProcessBulkOperation --path=Modules/AI/app/Jobs
php artisan make:job TranslateContent --path=Modules/AI/app/Jobs
php artisan make:job GenerateFromTemplate --path=Modules/AI/app/Jobs
php artisan make:job AnalyzeContent --path=Modules/AI/app/Jobs
php artisan make:job CacheWarmup --path=Modules/AI/app/Jobs
```

#### ✅ **ProcessBulkOperation Job**
```php
// Modules/AI/app/Jobs/ProcessBulkOperation.php
namespace Modules\AI\App\Jobs;

class ProcessBulkOperation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public function __construct(
        public string $operationId,
        public array $recordIds,
        public string $operationType,
        public array $options
    ) {}
    
    public function handle(BulkOperationProcessor $processor): void
    {
        // Her kayıt için işlem yap
        // Progress güncelle
        // Hataları logla
        // Sonuçları kaydet
    }
}
```

---

## 📊 PHASE 5: FRONTEND COMPONENTS (ZORUNLU)

### **5.1 BLADE COMPONENTS OLUŞTUR**

#### ✅ **universal-form.blade.php**
```blade
{{-- Modules/AI/resources/views/components/universal-form.blade.php --}}
@props([
    'featureId', 
    'moduleType' => null,
    'mode' => 'modal', // modal, inline, accordion
    'showDefaults' => true,
    'showTemplates' => true,
    'showHistory' => true
])

<div class="universal-form-container" 
     data-feature-id="{{ $featureId }}"
     data-module-type="{{ $moduleType }}"
     data-mode="{{ $mode }}">
    {{-- Dynamic form will be loaded here --}}
</div>
```

#### ✅ **ai-field-helper.blade.php**
```blade
{{-- Modules/AI/resources/views/components/ai-field-helper.blade.php --}}
@props([
    'fieldName',
    'fieldType' => 'text',
    'moduleName',
    'actions' => ['generate', 'optimize', 'translate']
])

<div class="ai-field-helper" data-field="{{ $fieldName }}">
    <button class="btn btn-sm btn-ai-assist">
        <i class="ti ti-sparkles"></i>
    </button>
    <div class="ai-dropdown d-none">
        @foreach($actions as $action)
            <a href="#" data-action="{{ $action }}">{{ __("ai.action.{$action}") }}</a>
        @endforeach
    </div>
</div>
```

#### ✅ **bulk-operation-modal.blade.php**
```blade
{{-- Modules/AI/resources/views/components/bulk-operation-modal.blade.php --}}
<div class="modal fade" id="bulkOperationModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            {{-- Operation type selection --}}
            {{-- Record selection --}}
            {{-- Options configuration --}}
            {{-- Progress tracking --}}
        </div>
    </div>
</div>
```

### **5.2 JAVASCRIPT MODULES OLUŞTUR**

#### ✅ **UniversalFormBuilder.js**
```javascript
// Modules/AI/resources/assets/js/UniversalFormBuilder.js
class UniversalFormBuilder {
    constructor(featureId, container, options = {}) {
        this.featureId = featureId;
        this.container = container;
        this.moduleType = options.moduleType;
        this.context = {};
        this.userPreferences = {};
        this.init();
    }
    
    // ZORUNLU METODLAR:
    async loadFormStructure() {}
    async loadSmartDefaults() {}
    async loadUserPreferences() {}
    renderForm() {}
    attachEventListeners() {}
    validateInputs() {}
    async submitForm() {}
    async savePreferences() {}
    showTemplateSelector() {}
    showHistoryPanel() {}
}
```

#### ✅ **BulkOperationManager.js**
```javascript
// Modules/AI/resources/assets/js/BulkOperationManager.js
class BulkOperationManager {
    constructor(moduleName) {
        this.moduleName = moduleName;
        this.selectedRecords = [];
        this.operationId = null;
        this.progressInterval = null;
    }
    
    // ZORUNLU METODLAR:
    selectRecords(recordIds) {}
    showOperationModal() {}
    async createOperation(type, options) {}
    trackProgress() {}
    updateProgressBar(percent) {}
    handleCompletion(results) {}
    handleErrors(errors) {}
    async retryFailed() {}
}
```

---

## 🎯 PHASE 6: ADMIN PANEL SAYFALARI (ZORUNLU)

### **6.1 ADMIN VIEW'LAR OLUŞTUR**

#### ✅ **Universal Input Management**
```blade
{{-- Modules/AI/resources/views/admin/universal/index.blade.php --}}
@extends('admin.layout')

@section('content')
<div class="page-header">
    <h2>Universal Input System Yönetimi</h2>
</div>

<div class="row">
    {{-- Feature listesi --}}
    {{-- Input configuration --}}
    {{-- Template management --}}
    {{-- Context rules --}}
    {{-- Analytics dashboard --}}
</div>
@endsection
```

#### ✅ **Module Integration Settings**
```blade
{{-- Modules/AI/resources/views/admin/integration/modules.blade.php --}}
@extends('admin.layout')

@section('content')
{{-- Her modül için AI entegrasyon ayarları --}}
{{-- Button configurations --}}
{{-- Available features per module --}}
{{-- Field mappings --}}
@endsection
```

---

## 📅 PHASE 7: SEEDER & TEST DATA (ZORUNLU)

### **7.1 SEEDER DOSYALARI OLUŞTUR**

```bash
php artisan make:seeder UniversalInputSystemV3Seeder --path=Modules/AI/database/seeders
php artisan make:seeder AIPromptTemplatesSeeder --path=Modules/AI/database/seeders
php artisan make:seeder AIContextRulesSeeder --path=Modules/AI/database/seeders
php artisan make:seeder AIModuleIntegrationsSeeder --path=Modules/AI/database/seeders
```

#### ✅ **UniversalInputSystemV3Seeder**
```php
// Modules/AI/database/seeders/UniversalInputSystemV3Seeder.php
class UniversalInputSystemV3Seeder extends Seeder
{
    public function run()
    {
        // Prompt Templates
        $this->seedPromptTemplates();
        
        // Context Rules
        $this->seedContextRules();
        
        // Module Integrations
        $this->seedModuleIntegrations();
        
        // Translation Mappings
        $this->seedTranslationMappings();
        
        // Default User Preferences
        $this->seedDefaultPreferences();
    }
}
```

---

## ⏰ ZAMAN ÇİZELGESİ (6 HAFTA)

### **HAFTA 1: Database & Models**
- [ ] Tüm tablo güncellemeleri
- [ ] Yeni tabloların oluşturulması
- [ ] Model dosyalarının hazırlanması
- [ ] İlişkilerin tanımlanması

### **HAFTA 2: Service Layer**
- [ ] UniversalInputManager
- [ ] PromptChainBuilder
- [ ] ContextAwareEngine
- [ ] BulkOperationProcessor

### **HAFTA 3: Controllers & Routes**
- [ ] Tüm controller'lar
- [ ] Route tanımlamaları
- [ ] API endpoints
- [ ] Middleware'ler

### **HAFTA 4: Frontend Components**
- [ ] Blade components
- [ ] JavaScript modules
- [ ] CSS styling
- [ ] AJAX integrations

### **HAFTA 5: Admin Panel**
- [ ] Admin sayfaları
- [ ] Configuration panels
- [ ] Analytics dashboard
- [ ] Template editor

### **HAFTA 6: Testing & Optimization**
- [ ] Unit tests
- [ ] Integration tests
- [ ] Performance optimization
- [ ] Documentation

---

## 🚨 KRİTİK KONTROL LİSTESİ

### **Database Hazırlık:**
- [ ] Backup al
- [ ] Migration dosyaları hazır
- [ ] Rollback planı var
- [ ] Index'ler optimize

### **Code Quality:**
- [ ] PSR-12 standards
- [ ] Type declarations
- [ ] PHPDoc comments
- [ ] Error handling

### **Performance:**
- [ ] Cache strategy
- [ ] Queue configuration
- [ ] Database indexes
- [ ] Eager loading

### **Security:**
- [ ] Input validation
- [ ] SQL injection koruması
- [ ] XSS koruması
- [ ] Rate limiting

---

## 🎯 BAŞARI KRİTERLERİ

1. **Tüm tablolar oluşturuldu ve ilişkiler kuruldu**
2. **Service layer'lar test edildi ve çalışıyor**
3. **Admin panel'den her şey yönetilebiliyor**
4. **Modül entegrasyonları sorunsuz çalışıyor**
5. **Bulk operations 100+ kayıt işleyebiliyor**
6. **Cache hit rate > %60**
7. **Response time < 500ms (cached)**
8. **Translation accuracy > %95**
9. **Template generation < 2 saniye**
10. **Zero downtime deployment**

---

## 📝 NOTLAR

**Bu yol haritası KATI bir şekilde takip edilmelidir:**
- Her phase sırasıyla tamamlanmalı
- Eksik bırakılan hiçbir adım olmamalı
- Tüm service'ler ve controller'lar oluşturulmalı
- Test edilmeden production'a geçilmemeli

**Başarı için:**
- Her gün progress takibi yap
- Sorunları anında raporla
- Documentation'ı eksik bırakma
- Code review'ları aksatma

---

*Bu doküman V3 Professional sisteminin KATI implementation planıdır. Her adım zorunludur ve sırası değiştirilemez.*