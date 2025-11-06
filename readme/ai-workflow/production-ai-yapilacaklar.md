# 🚀 AI WORKFLOW - PRODUCTION'A ALMA YAPILACAKLAR LİSTESİ

**Tarih:** 5 Kasım 2024
**Durum:** DEV'de Test Edildi, Production'a Hazırlanıyor

---

## ⚠️ ÖNEMLİ UYARI

Bu işlemler **CANLI SUNUCUDA** yapılacak!
**Backup almadan işlem YAPMA!**

---

## 📋 DATABASE DEĞİŞİKLİKLERİ

### 1️⃣ **CENTRAL DATABASE** (tuufi_com / laravel)

#### A. `ai_conversations` Tablosuna Kolonlar Ekle

```sql
-- 1. flow_id kolonu
ALTER TABLE ai_conversations
ADD COLUMN flow_id BIGINT UNSIGNED NULL
COMMENT 'Hangi workflow akışı kullanılıyor - tenant_conversation_flows.id'
AFTER tenant_id;

-- 2. current_node_id kolonu
ALTER TABLE ai_conversations
ADD COLUMN current_node_id VARCHAR(50) NULL
COMMENT 'Workflow akışında şu anda hangi node\'da (örn: node_greeting_1)'
AFTER flow_id;

-- 3. state_history kolonu
ALTER TABLE ai_conversations
ADD COLUMN state_history JSON NULL
COMMENT 'Node geçiş geçmişi - [{node_id, timestamp, success}]'
AFTER context_data;

-- 4. context_data'yı JSON'a çevir (eğer longtext ise)
ALTER TABLE ai_conversations
MODIFY COLUMN context_data JSON NULL
COMMENT 'Sohbet sırasında toplanan veriler - JSON formatında';
```

#### B. Index'leri Ekle

```sql
-- flow_id için index
ALTER TABLE ai_conversations
ADD INDEX idx_flow_id (flow_id);

-- tenant_id + flow_id birleşik index
ALTER TABLE ai_conversations
ADD INDEX idx_tenant_flow (tenant_id, flow_id);

-- current_node_id için index
ALTER TABLE ai_conversations
ADD INDEX idx_current_node (current_node_id);
```

#### C. Doğrulama

```sql
-- Kolonları kontrol et
DESCRIBE ai_conversations;

-- Özellikle şunlar olmalı:
-- flow_id          -> bigint unsigned, NULL, idx_flow_id
-- current_node_id  -> varchar(50), NULL, idx_current_node
-- context_data     -> json, NULL
-- state_history    -> json, NULL
```

---

### 2️⃣ **TENANT DATABASE'LERDE YAPILMAYACAK İŞLEM**

**⚠️ DİKKAT:** `ai_conversations` tablosu **SADECE CENTRAL DATABASE'DE** olmalı!

**Eğer tenant database'lerde varsa:**
```sql
-- TENANT 1 (tenant_tuufi)
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS ai_conversations;
SET FOREIGN_KEY_CHECKS=1;

-- TENANT 2 (tenant_ixtif)
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS ai_conversations;
SET FOREIGN_KEY_CHECKS=1;

-- Diğer tenant'lar için de aynı işlem
```

**Sebep:** `ai_conversations` central'de toplanıyor, `tenant_id` ile ayırt ediliyor.

---

### 3️⃣ **MIGRATION DOSYALARI**

#### A. Tenant Migrations'dan Sil

```bash
# Bu dosya SADECE central migrations'da olmalı:
rm database/migrations/tenant/2024_11_04_120002_create_ai_conversations_table.php
```

#### B. Central Migrations'da Kalmalı

```bash
# Bu dosya yerinde kalacak:
database/migrations/2024_11_04_120002_create_ai_conversations_table.php
```

---

## 📝 KOD DEĞİŞİKLİKLERİ

### 1️⃣ **AIConversation Model**

**Dosya:** `app/Models/AIConversation.php`

**Değişiklik:**
```php
class AIConversation extends Model
{
    use HasFactory;

    // ⭐ BU SATIRI EKLE
    protected $connection = 'mysql'; // Central database - tüm tenant conversation'ları burada

    protected $table = 'ai_conversations';
```

**Sebep:** Model'e connection belirtilmezse tenant context'inde tenant database'i kullanmaya çalışır. Ama `ai_conversations` central'de olmalı.

---

### 3️⃣ **TenantConversationFlow Model**

**Dosya:** `app/Models/TenantConversationFlow.php`

**Değişiklik:**
```php
class TenantConversationFlow extends Model
{
    use HasFactory;

    // ⭐ BU SATIRI EKLE
    protected $connection = 'tenant'; // Tenant database - her tenant'ın kendi flow'ları

    protected $table = 'tenant_conversation_flows';
```

**Sebep:** Flow'lar tenant-specific olmalı. Her tenant kendi flow'larını tenant database'inde tutar.

---

### 2️⃣ **ChatMessage İlişkisi** (DÜZELTİLDİ)

**Dosya:** `app/Models/AIConversation.php`

**Sorun:** `ChatMessage` model'i yok, ama ilişki tanımlı. Bu flow execution sırasında `Class "App\Models\ChatMessage" not found` hatasına sebep oluyor.

**Değişiklik:**
```php
// Line 48-56
/**
 * Get messages in this conversation
 *
 * TODO: Implement ChatMessage model or use correct message model
 */
// public function messages(): HasMany
// {
//     return $this->hasMany(ChatMessage::class, 'conversation_id');
// }
```

**Sebep:** İlişki şu anda kullanılmıyor ve eksik model class'ı flow execution'ı bozuyor. Yorum satırı yaparak geçici çözüm sağlandı.

**Gelecek Çözüm:** Conversation message'ları için uygun model oluşturulduğunda bu ilişki aktif edilecek.

---

### 4️⃣ **SHOP NAMESPACE MIGRATION** (5 KASIM 2024 - TAMAMLANDI ✅)

**Önemli:** Tenant-specific node'lar artık **Shop namespace** altında global olarak sunuluyor!

**Değişiklik Özeti:**
- ❌ ESKİ: `App\Services\ConversationNodes\TenantSpecific\Tenant_2\CategoryDetectionNode`
- ✅ YENİ: `App\Services\ConversationNodes\Shop\CategoryDetectionNode`

**Yapılan İşlemler:**

1. **Seeder Dosyaları Güncellendi:**
   - `database/seeders/AIWorkflowNodesSeeder.php`: Shop namespace kullanılıyor, `is_global=true`
   - `database/seeders/AIWorkflowSeeder.php`: Hardcoded class referansları kaldırıldı

2. **Tenant Database Temizliği:**
   - Tenant DB'deki eski `ai_workflow_nodes` kayıtları silindi
   - Artık tüm node'lar central DB'den yükleniyor

3. **Cache Version Bump:**
   - `AIWorkflowNode::getForTenant()` cache version: v3 → v4

4. **Shop Node'ları Düzeltildi:**
   - `ProductSearchNode`: Multilingual field support (title, slug, body JSON search)
   - `CategoryDetectionNode`: title array handling
   - Parametre düzeltmeleri (ProductSearchService API)

5. **Flow Config Düzeltmeleri:**
   - Condition node: `condition` → `condition_type`
   - `next_node` fields eklendi

**Node'lar (19 adet - Tümü Global):**

**Common Nodes (6):** ai_response, sentiment_detection, context_builder, history_loader, message_saver, welcome, condition, end, collect_data, webhook, link_generator, share_contact

**Shop Nodes (7):** category_detection, product_search, price_query, currency_converter, product_comparison, contact_request, stock_sorter

**Test Sonucu:**
```bash
php /tmp/test-ai-chat.php
✅ 12 node başarıyla çalıştı
✅ AI response geldi
✅ Shop namespace aktif
```

**Production'da Yapılacak (Database İşlemleri):**

### ⚠️ ADIM 1: ÖNCE KONTROL ET (Hiçbir şey silmez!)

```bash
# Tenant DB'de eski kayıt var mı kontrol et
php artisan tinker --execute="
echo '━━━ TENANT DB KONTROL ━━━' . PHP_EOL;

// Tenant 2 (ixtif.com)
tenancy()->initialize(2);
\$count2 = DB::table('ai_workflow_nodes')->count();
echo 'Tenant 2: ' . \$count2 . ' kayıt';
if (\$count2 > 0) {
    echo ' ⚠️ (Eski kayıtlar var - temizlenmeli)' . PHP_EOL;
} else {
    echo ' ✅ (Temiz)' . PHP_EOL;
}
tenancy()->end();

// Tenant 3 (ixtif.com.tr)
tenancy()->initialize(3);
\$count3 = DB::table('ai_workflow_nodes')->count();
echo 'Tenant 3: ' . \$count3 . ' kayıt';
if (\$count3 > 0) {
    echo ' ⚠️ (Eski kayıtlar var - temizlenmeli)' . PHP_EOL;
} else {
    echo ' ✅ (Temiz)' . PHP_EOL;
}
tenancy()->end();

echo '━━━━━━━━━━━━━━━━━━━━━━' . PHP_EOL;
echo 'Eğer ⚠️ görüyorsan, temizlik komutu gerekli!' . PHP_EOL;
"
```

### ⚠️ ADIM 2: EĞER ESKİ KAYITLAR VARSA (⚠️ İşaretli tenant'lar için)

**UYARI:** Aşağıdaki komutlar veritabanından kayıt siler!

```bash
# Tenant 2 temizliği (SADECE ESKİ KAYIT VARSA ÇALIŞTIR!)
php artisan tinker --execute="
tenancy()->initialize(2);
\$count = DB::table('ai_workflow_nodes')->count();
if (\$count > 0) {
    DB::table('ai_workflow_nodes')->delete();
    echo '✅ Tenant 2: ' . \$count . ' eski kayıt silindi' . PHP_EOL;
}
tenancy()->end();
"

# Tenant 3 temizliği (SADECE ESKİ KAYIT VARSA ÇALIŞTIR!)
php artisan tinker --execute="
tenancy()->initialize(3);
\$count = DB::table('ai_workflow_nodes')->count();
if (\$count > 0) {
    DB::table('ai_workflow_nodes')->delete();
    echo '✅ Tenant 3: ' . \$count . ' eski kayıt silindi' . PHP_EOL;
}
tenancy()->end();
"
```

### ✅ ADIM 3: Cache Temizle (Her Zaman Gerekli)

```bash
# Node cache temizle
php artisan tinker --execute="
App\Models\AIWorkflowNode::clearCache(2);
App\Models\AIWorkflowNode::clearCache(3);
echo '✅ Node cache temizlendi' . PHP_EOL;
"

# Genel cache temizle
php artisan cache:clear
php artisan config:clear
php artisan view:clear

echo '✅ Tüm cache temizlendi'
```

**Not:** Kod değişiklikleri Git'ten otomatik gelecek, sadece DB kontrol/temizlik + cache gerekli!

---

### 5️⃣ **NodeExecutor Registry Fix** (KRİTİK!)

**Dosya:** `app/Services/ConversationNodes/NodeExecutor.php`

**Sorun:** Tenant context'inde NodeExecutor sadece tenant-specific node'ları yüklüyor, global node'ları (ai_response, condition, collect_data, end, share_contact, webhook) yüklemiyor.

**Hata:** `Unknown node type: ai_response. Available types: category_detection, product_recommendation...`

**Değişiklik:**
```php
protected function initializeRegistry(): void
{
    try {
        // Get tenant ID (if in tenant context)
        $tenantId = function_exists('tenant') && tenant() ? tenant('id') : null;

        if ($tenantId) {
            // Tenant context: Get both global and tenant-specific nodes
            $nodes = AIWorkflowNode::getForTenant($tenantId);

            foreach ($nodes as $node) {
                self::register($node['type'], $node['class']);
            }

            Log::info('Node registry initialized from database (tenant context)', [
                'tenant_id' => $tenantId,
                'total_nodes' => count(self::$nodeRegistry),
                'node_types' => array_keys(self::$nodeRegistry),
            ]);
        } else {
            // Central context: Get only global nodes from central DB
            $nodes = \DB::connection('mysql')->table('ai_workflow_nodes')
                ->where('is_active', true)
                ->where('is_global', true)
                ->orderBy('category')
                ->orderBy('order')
                ->get();

            foreach ($nodes as $node) {
                self::register($node->node_key, $node->node_class);
            }

            Log::info('Node registry initialized from database (central context)', [
                'total_nodes' => count(self::$nodeRegistry),
                'node_types' => array_keys(self::$nodeRegistry),
            ]);
        }
    } catch (\Exception $e) {
        Log::error('Failed to initialize node registry from database', [
            'error' => $e->getMessage(),
        ]);

        // Fallback: Initialize with hardcoded nodes (for safety)
        $this->initializeHardcodedRegistry();
    }
}
```

**Sebep:** Eski kod `AIWorkflowNode::where('is_active', true)` kullanıyordu. Bu tenant context'inde sadece tenant database'deki node'ları alıyor. Global node'lar central database'de olduğu için görünmüyor. `getForTenant()` metodu hem central DB'den global node'ları hem de tenant DB'den tenant-specific node'ları alıyor.

**Test:**
```bash
php artisan tinker
>>> $tenant = \App\Models\Tenant::find(2);
>>> tenancy()->initialize($tenant);
>>> \App\Services\ConversationNodes\NodeExecutor::clearRegistry();
>>> $executor = new \App\Services\ConversationNodes\NodeExecutor();
>>> $types = \App\Services\ConversationNodes\NodeExecutor::getRegisteredTypes();
>>> print_r($types);
# 13 node görülmeli: ai_response, condition, collect_data, end, share_contact, webhook, category_detection, product_recommendation, price_filter, currency_convert, stock_check, comparison, quotation
```

---

### 6️⃣ **NodeExecutor Force Reinit** (5 KASIM 2024 - TAMAMLANDI ✅)

**Dosya:** `app/Services/ConversationNodes/NodeExecutor.php`

**Sorun:** Laravel container NodeExecutor'ı boot sırasında oluşturuyordu, tenant context'i henüz hazır değildi.

**Çözüm:** `execute()` metodunda her çağrıda force reinitialize + explicit tenant_id geçişi

```php
public function execute(array $nodeData, AIConversation $conversation, string $userMessage): array
{
    $startTime = microtime(true);

    try {
        // 🚨 CRITICAL: ALWAYS reinitialize registry on EVERY execute()
        self::$initialized = false;
        self::$nodeRegistry = [];
        $this->initializeRegistry($conversation->tenant_id); // Pass explicit tenant_id
        self::$initialized = true;

        // Validate node data
        if (!isset($nodeData['type'])) {
            throw new \Exception('Node type not specified');
        }

        // Continue with execution...
    }
}

protected function initializeRegistry(?int $forceTenantId = null): void
{
    // Get tenant ID - prioritize forced ID, then tenant context
    $tenantId = $forceTenantId ?? (function_exists('tenant') && tenant() ? tenant('id') : null);

    if ($tenantId) {
        $nodes = AIWorkflowNode::getForTenant($tenantId);
        foreach ($nodes as $node) {
            self::register($node['type'], $node['class']);
        }
    }
}
```

**Sebep:** Dependency injection timing issue - tenant context'i NodeExecutor oluşturulduğunda hazır olmayabilir.

---

### 7️⃣ **Livewire 3 Uyumluluk** (ZATEN DÜZELTİLDİ)

**Dosya:** `Modules/AI/app/Http/Livewire/Admin/Workflow/FlowEditor.php`

**Değişiklik:** (Zaten yapıldı ama doğrula)
```php
// ❌ ESKI (Livewire 2)
$this->dispatchBrowserEvent('save-flow-request');

// ✅ YENİ (Livewire 3)
$this->dispatch('save-flow-request');
```

**Blade Dosyası:** `Modules/AI/resources/views/livewire/admin/workflow/flow-editor.blade.php`

```javascript
// ❌ ESKI
window.addEventListener('save-flow-request', () => {});

// ✅ YENİ
Livewire.on('save-flow-request', () => {});
```

---

### 6️⃣ **AI Entegrasyonu** (TAMAMLANDI ✅)

**Dosya:** `app/Services/ConversationFlowEngine.php`

**Değişiklik 1:** `generateAIResponse()` metodunu CentralAIService kullanacak şekilde güncelle (Line 212-255)

```php
protected function generateAIResponse(string $prompt, array $context): string
{
    try {
        // Use CentralAIService for AI requests
        $aiService = app(\App\Services\AI\CentralAIService::class);

        // Build context as user message
        $userMessage = $context['user_message'] ?? '';
        $conversationContext = $context['conversation_context'] ?? [];

        // Combine system prompt + user message
        $fullPrompt = $prompt . "\n\nKullanıcı mesajı: " . $userMessage;

        // Execute AI request
        $response = $aiService->executeRequest($fullPrompt, [
            'usage_type' => 'conversation_flow',
            'feature_slug' => 'ai_workflow',
            'reference_id' => $context['conversation_id'] ?? null,
            'force_provider' => 'openai', // TODO: Make this configurable
        ]);

        // Extract response text (response is array with 'content' key)
        if (isset($response['response'])) {
            if (is_array($response['response']) && isset($response['response']['content'])) {
                return $response['response']['content'];
            }
            if (is_string($response['response'])) {
                return $response['response'];
            }
        }

        return 'Üzgünüm, yanıt oluşturulamadı.';
    } catch (\Exception $e) {
        Log::error('AI response generation failed', ['error' => $e->getMessage()]);
        return 'Üzgünüm, bir hata oluştu.';
    }
}
```

**Değişiklik 2:** User message'ı context'e ekle (Line 70-71)

```php
// Add user message to context
$aiContext['user_message'] = $userMessage;
```

**Değişiklik 3:** Message history metodunu geçici devre dışı bırak (Line 190-214)

```php
protected function getMessageHistory(AIConversation $conversation): array
{
    // TODO: Implement message history when ChatMessage model is created
    return [];
}
```

**Sebep:**
- CentralAIService tüm AI provider'ları (OpenAI, Anthropic, DeepSeek) tek bir interface'den yönetir
- Credit tracking, token hesaplama, usage logging otomatik yapılır
- Response format: `$response['response']['content']` (nested array)

**Test Sonucu:**
```
Kullanıcı: "Merhaba, 2 ton kapasiteli transpalet arıyorum"
AI: "Merhaba! İxtif.com olarak sizi burada görmekten çok mutluyuz..."
✅ Gerçek AI yanıtları geliyor!
```

---

## 🧪 TEST ADIMLARI (Production'da)

### 1️⃣ **Database Değişikliklerini Uygula**

```bash
# Backup al
mysqldump -u root laravel > backup_ai_conversations_$(date +%Y%m%d_%H%M%S).sql

# Kolonları ekle (yukarıdaki SQL'leri çalıştır)
mysql -u root laravel < production_ai_workflow_schema.sql
```

### 2️⃣ **Kod Değişikliklerini Deploy Et**

```bash
# Git pull veya dosyaları upload et
git pull origin main

# Composer update (gerekirse)
composer install --no-dev --optimize-autoloader

# Cache temizle
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# ⚠️ ÖNEMLİ: Workflow nodes cache'ini de temizle
php artisan tinker --execute="
\Cache::forget('ai_workflow_nodes_tenant_1');
\Cache::forget('ai_workflow_nodes_tenant_2');
\Cache::forget('ai_workflow_nodes_tenant_3');
echo 'Workflow nodes cache temizlendi';
"

# NodeExecutor registry'yi temizle
php artisan tinker --execute="
\App\Services\ConversationNodes\NodeExecutor::clearRegistry();
echo 'NodeExecutor registry temizlendi';
"
```

### 3️⃣ **Test Et**

```bash
# Tinker ile test
php artisan tinker
>>> $executor = new \App\Services\ConversationNodes\NodeExecutor();
>>> $types = \App\Services\ConversationNodes\NodeExecutor::getRegisteredTypes();
>>> print_r($types);
# ai_response, condition, collect_data, end... olmalı

# Flow test
>>> $engine = app(\App\Services\ConversationFlowEngine::class);
>>> $result = $engine->processMessage('test_' . time(), 2, 'merhaba', null);
>>> print_r($result);
```

### 4️⃣ **Admin Panel Test**

1. Admin'e giriş yap
2. `/admin/ai/workflow/flows` → Flow listesi
3. Flow oluştur / düzenle
4. Test Flow butonuna tıkla
5. Mesaj gönder
6. Debug panel'i aç, sonuçları kontrol et

---

## ⚠️ OLASI HATALAR VE ÇÖZÜMLER

### Hata 1: `Table 'ai_conversations' doesn't exist`

**Sebep:** Tenant database'de arıyor ama tablo central'de

**Çözüm:**
```php
// AIConversation.php içinde
protected $connection = 'mysql'; // MUTLAKA EKLE
```

### Hata 2: `Unknown column 'flow_id'`

**Sebep:** Central database'e kolon eklenmemiş

**Çözüm:**
```sql
ALTER TABLE ai_conversations ADD COLUMN flow_id BIGINT UNSIGNED NULL;
```

### Hata 3: `Unknown column 'current_node_id'`

**Sebep:** Central database'e kolon eklenmemiş

**Çözüm:**
```sql
ALTER TABLE ai_conversations ADD COLUMN current_node_id VARCHAR(50) NULL;
```

### Hata 4: `Class "App\Models\ChatMessage" not found`

**Sebep:** Model'de yanlış class referansı

**Çözüm:**
```php
// AIConversation.php:53 - Doğru model adını kullan veya yorum satırı yap
// return $this->hasMany(ChatMessage::class, 'conversation_id');
```

### Hata 5: `Unknown node type: ai_response`

**Sebep:** NodeExecutor registry tenant context'inde global node'ları yüklemiyor

**Çözüm:**
```php
// app/Services/ConversationNodes/NodeExecutor.php
// initializeRegistry() metodunu yukarıdaki "4️⃣ NodeExecutor Registry Fix" bölümündeki gibi düzelt

// Sonra cache temizle ve test et
php artisan cache:clear
\App\Services\ConversationNodes\NodeExecutor::clearRegistry();
```

**Doğrulama:**
```bash
php artisan tinker
>>> $tenant = \App\Models\Tenant::find(2);
>>> tenancy()->initialize($tenant);
>>> $executor = new \App\Services\ConversationNodes\NodeExecutor();
>>> $types = \App\Services\ConversationNodes\NodeExecutor::getRegisteredTypes();
>>> count($types);
# 13 olmalı (6 global + 7 tenant-specific)
>>> in_array('ai_response', $types);
# true olmalı
```

### Hata 6: `Invalid node configuration for ai_response`

**Sebep:** Flow data'da eski config key kullanılmış (`prompt` yerine `system_prompt` olmalı)

**Çözüm:**
```php
// Tenant database'de flow config'i düzelt
$tenant = \App\Models\Tenant::find(2);
tenancy()->initialize($tenant);

$flow = \App\Models\TenantConversationFlow::find(1);
$flowData = $flow->flow_data;

// ai_response node'larındaki prompt -> system_prompt
foreach ($flowData['nodes'] as &$node) {
    if ($node['type'] === 'ai_response' && isset($node['config']['prompt'])) {
        $node['config']['system_prompt'] = $node['config']['prompt'];
        unset($node['config']['prompt']);
    }
}

$flow->flow_data = $flowData;
$flow->save();

tenancy()->end();
```

**VEYA:** Flow editor'da node'u aç, kaydet (otomatik doğru key ile kaydeder)

---

## 📊 KONTROL LİSTESİ

### Database
- [ ] Central database backup alındı
- [ ] `flow_id` kolonu eklendi
- [ ] `current_node_id` kolonu eklendi
- [ ] `state_history` kolonu eklendi
- [ ] `context_data` JSON'a çevrildi
- [ ] Index'ler eklendi
- [ ] Tenant database'lerden `ai_conversations` silindi (eğer varsa)

### Kod
- [x] ✅ `AIConversation::$connection = 'mysql'` eklendi
- [x] ✅ `TenantConversationFlow::$connection = 'tenant'` eklendi
- [x] ✅ **SHOP NAMESPACE MIGRATION** (TenantSpecific → Shop)
- [x] ✅ **Tenant DB'deki eski node kayıtları silindi**
- [x] ✅ **ProductSearchNode multilingual field support**
- [x] ✅ **CategoryDetectionNode array handling**
- [x] ✅ **NodeExecutor force reinit + explicit tenant_id**
- [x] ✅ **`NodeExecutor::initializeRegistry()` düzeltildi** (getForTenant kullanılıyor)
- [x] ✅ **`AIConversation::messages()` ilişkisi yorum satırı yapıldı** (ChatMessage yok)
- [x] ✅ **`ConversationFlowEngine::generateAIResponse()` CentralAIService entegrasyonu**
- [x] ✅ **`ConversationFlowEngine::getMessageHistory()` geçici devre dışı**
- [x] ✅ User message context'e eklendi
- [x] ✅ Livewire 3 dispatch metodları doğru
- [x] ✅ Migration dosyası tenant/ klasöründen silindi

### Test
- [ ] **NodeExecutor registry 13 node yüklüyor** (6 global + 7 tenant-specific)
- [ ] **ai_response, condition, end node'ları registry'de var**
- [ ] **Flow execution başarılı** (flow bulunuyor, node execute ediliyor)
- [ ] **AI yanıtları çalışıyor** (CentralAIService ile gerçek AI response)
- [ ] Test Flow modal çalışıyor
- [ ] Conversation'lar central database'e kaydediliyor
- [ ] `tenant_id` ile doğru tenant ayırt ediliyor
- [ ] AI provider seçimi (OpenAI/Anthropic/DeepSeek)

### Cache
- [ ] Application cache temizlendi
- [ ] Config cache temizlendi
- [ ] View cache temizlendi
- [ ] Route cache temizlendi
- [ ] **Workflow nodes cache temizlendi** (ai_workflow_nodes_tenant_*)
- [ ] **NodeExecutor registry temizlendi**
- [ ] OPcache reset (eğer varsa)

---

## 🎯 ÖZET

**KOD DEĞİŞİKLİKLERİ (Git'ten Otomatik Gelecek):**
1. ✅ AIConversation model'e `$connection = 'mysql'` eklendi
2. ✅ TenantConversationFlow model'e `$connection = 'tenant'` eklendi
3. ✅ **SHOP NAMESPACE MIGRATION** (TenantSpecific → Shop)
4. ✅ **NodeExecutor force reinit** + explicit tenant_id
5. ✅ **NodeExecutor::initializeRegistry() düzeltildi** (getForTenant kullanılıyor)
6. ✅ **ProductSearchNode multilingual field support**
7. ✅ **CategoryDetectionNode array handling**
8. ✅ ChatMessage ilişkisi yorum satırı yapıldı
9. ✅ **AI Entegrasyonu** (CentralAIService)
10. ✅ Migration dosyası tenant/ klasöründen silindi

**DATABASE İŞLEMLERİ (Production'da Manuel Yapılacak):**
1. ✅ **ÖNCE KONTROL ET:** Tenant DB'de eski kayıt var mı? (Kontrol komutu - hiçbir şey silmez)
2. ⚠️ **SADECE GEREKİRSE:** Eski kayıtları sil (1. adımda ⚠️ gördüysen)
3. ✅ **HER ZAMAN:** Node cache + genel cache temizle

**NOT:** Central database'e kolon ekleme işlemleri daha önce yapılmışsa tekrar yapılmasına gerek yok.

**Kritik Noktalar:**
- ✅ `ai_conversations` **SADECE CENTRAL DATABASE'DE** (`$connection = 'mysql'`)
- ✅ `tenant_conversation_flows` **TENANT DATABASE'DE** (`$connection = 'tenant'`)
- ✅ `tenant_id` ile ayırt ediliyor
- ✅ **SHOP NAMESPACE:** TenantSpecific node'lar artık global (19 node: 6 common + 13 shop)
- ✅ **Tenant DB'de ai_workflow_nodes YOK** - Tümü central DB'den yüklenir
- ✅ **NodeExecutor force reinit** her execute() çağrısında (tenant context güvenliği)
- ✅ **ProductSearchNode & CategoryDetectionNode** multilingual field support
- ✅ **AI yanıtları CentralAIService** ile alınıyor (response format: `['response']['content']`)
- ✅ **OpenAI kullanılıyor** (force_provider: 'openai')
- ⚠️ **Workflow nodes cache** temizlenmeli (v4 kullanılıyor)
- ⚠️ **Tenant DB kontrol edilmeli** (eski kayıtlar varsa temizlenmeli - ÖNCE KONTROL ET!)

**Tahmini Süre:** 5-10 dakika (sadece DB temizliği)
**Downtime:** Yok (backward compatible)

---

## 📞 DESTEK

**Sorun Yaşarsan:**
1. Backup'tan restore et
2. Cache'leri temizle
3. Log'lara bak: `storage/logs/laravel.log`
4. Database durumunu kontrol et: `DESCRIBE ai_conversations`

**Test Komutları:**
```bash
# Registry kontrol
php artisan tinker --execute="print_r(\App\Services\ConversationNodes\NodeExecutor::getRegisteredTypes());"

# Conversation sayısı
php artisan tinker --execute="echo \DB::connection('mysql')->table('ai_conversations')->count();"

# Flow test
php artisan tinker --execute="
\$engine = app(\App\Services\ConversationFlowEngine::class);
\$result = \$engine->processMessage('test_sim_' . time(), 2, 'test', null);
echo json_encode(\$result);
"
```

---

**SON KONTROL:** Bu dokümanı adım adım takip et, her adımı işaretle, sorun çıkarsa geri dön!
