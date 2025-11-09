# 🤖 AI SHOP CHAT - SETUP & ARCHITECTURE GUIDE

**Last Updated:** 2025-11-09
**Status:** Production Ready
**Version:** v2.0 (Simple Architecture)

---

## 📋 İÇİNDEKİLER

1. [Sistem Mimarisi](#sistem-mimarisi)
2. [Database Yapısı](#database-yapısı)
3. [Yeni Tenant Kurulumu](#yeni-tenant-kurulumu)
4. [Global Directive Sistemi](#global-directive-sistemi)
5. [Flow Kopyalama](#flow-kopyalama)
6. [Troubleshooting](#troubleshooting)

---

## 🏗️ SİSTEM MİMARİSİ

### **TEMEL YAPILANMA**

```
┌─────────────────────────────────────────┐
│         GLOBAL MOTOR (Ortak)            │
│  - FlowExecutor (workflow engine)       │
│  - Node'lar (BaseNode, AIResponseNode)  │
│  - Tüm tenant'lar kullanır              │
└─────────────────────────────────────────┘
                  ↓
┌─────────────────────────────────────────┐
│      TENANT DATA (Her tenant'a özel)    │
│                                          │
│  TENANT DB:                              │
│  ├─ ai_flows (tenant'ın workflow'u)     │
│  └─ ai_knowledge_base (bilgi bankası)   │
│                                          │
│  CENTRAL DB (tenant_id ile):             │
│  ├─ ai_tenant_directives (AI kuralları) │
│  └─ ai_conversations (mesaj geçmişi)    │
└─────────────────────────────────────────┘
                  ↓
┌─────────────────────────────────────────┐
│   TENANT-SPECIFIC SERVICES (Opsiyonel)  │
│  - Tenant2ProductSearchService          │
│  - Tenant2PromptService                 │
│  (Sadece özel logic gerektiriyorsa)     │
└─────────────────────────────────────────┘
```

---

## 💾 DATABASE YAPISI

### **1. TENANT DATABASE (Her tenant'ın kendi DB'si)**

```sql
-- tenant_ixtif, tenant_giyim, vs.

CREATE TABLE ai_flows (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255),
    description TEXT,
    flow_data JSON,          -- Node'lar ve workflow yapısı
    metadata JSON,
    priority INT,
    status ENUM('active', 'inactive')
);

CREATE TABLE ai_knowledge_base (
    id BIGINT PRIMARY KEY,
    title VARCHAR(255),
    content TEXT,
    category VARCHAR(100),
    is_active BOOLEAN
);
```

### **2. CENTRAL DATABASE (Tüm tenant'lar için merkezi)**

```sql
-- tuufi_4ekim (central)

CREATE TABLE ai_tenant_directives (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT,        -- 0 = global, >0 = tenant-specific
    directive_key VARCHAR(255),
    directive_value TEXT,
    directive_type ENUM('string', 'integer', 'boolean', 'json'),
    category VARCHAR(100),
    is_active BOOLEAN,
    INDEX idx_tenant_directive (tenant_id, directive_key)
);

CREATE TABLE ai_conversations (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT,        -- Hangi tenant'a ait
    session_id VARCHAR(64),
    user_id BIGINT NULL,
    feature_slug VARCHAR(100),
    status VARCHAR(50),
    ip_address VARCHAR(45)
);

CREATE TABLE ai_conversation_messages (
    id BIGINT PRIMARY KEY,
    conversation_id BIGINT,
    role ENUM('user', 'assistant'),
    content TEXT
);
```

---

## 🚀 YENİ TENANT KURULUMU

### **HIZLI KURULUM (5 Dakika!)**

```bash
# Tenant 10 için AI kur (Tenant 2'den kopyala)
php artisan tenant:setup-ai 10 --from=2
```

**Ne Yapar:**
1. ✅ Flow'u kopyalar (Tenant 2 → Tenant 10)
2. ✅ Directives'leri kopyalar (23 adet)
3. ✅ Tenant 10 AI chat'e hazır!

### **MANUEL KURULUM (PHP ile)**

```php
use App\Services\AI\SimpleFlowCopyService;

$copyService = new SimpleFlowCopyService();

// Full setup
$result = $copyService->setupTenantAI(
    targetTenantId: 10,
    templateTenantId: 2
);

// Sonuç:
// [
//     'flow_copied' => true,
//     'directives_copied' => 23,
//     'errors' => []
// ]
```

---

## 🌍 GLOBAL DİRECTİVE SİSTEMİ

### **Global Directive Nedir?**

`tenant_id = 0` olan directive'ler **tüm tenant'lar için varsayılan** değerlerdir.

### **Nasıl Çalışır?**

```
1. Directive oku: "greeting_message"
   ↓
2. Önce tenant-specific bak (tenant_id = 10)
   ├─ Varsa → Kullan (override)
   └─ Yoksa → Global bak (tenant_id = 0)
```

### **Global Directive Ekleme**

```php
use App\Services\AI\SimpleDirectiveService;

$service = new SimpleDirectiveService();

// Global directive ekle
$service->setGlobalDirective(
    key: 'new_feature_enabled',
    value: 'true',
    type: 'boolean',
    category: 'general'
);

// Tüm tenant'lar otomatik bu değeri kullanır!
```

### **Tenant Override**

```php
use App\Models\AITenantDirective;

// Tenant 2 için özel değer
AITenantDirective::setValue(
    tenantId: 2,
    key: 'greeting_message',
    value: 'İXtif\'e hoş geldiniz!',
    type: 'string'
);

// Diğer tenant'lar global'i kullanır
// Tenant 2 override'ı kullanır
```

---

## 📋 FLOW KOPYALAMA

### **Tenant'tan Tenant'a Kopyalama**

```php
$copyService = new SimpleFlowCopyService();

// Sadece flow kopyala
$success = $copyService->copyFlow(
    fromTenantId: 2,
    toTenantId: 15
);

// Sadece directives kopyala
$count = $copyService->copyDirectives(
    fromTenantId: 2,
    toTenantId: 15,
    overwrite: false  // Varsa atla
);
```

### **Artisan Command ile**

```bash
# Varsayılan (Tenant 2'den kopyala)
php artisan tenant:setup-ai 15

# Başka tenant'tan kopyala
php artisan tenant:setup-ai 15 --from=3

# Üzerine yaz
php artisan tenant:setup-ai 15 --from=2 --overwrite
```

---

## 🎯 MODEL CONFIGURATION

### **Flow Model (Tenant DB)**

```php
namespace Modules\AI\App\Models;

class Flow extends Model
{
    protected $connection = 'tenant'; // ✅ Explicit tenant DB
    protected $table = 'ai_flows';

    public static function getActiveFlow()
    {
        return static::where('status', 'active')
            ->orderBy('priority', 'asc')
            ->first();
    }
}
```

### **AITenantDirective Model (Central DB)**

```php
namespace App\Models;

class AITenantDirective extends Model
{
    protected $connection = 'mysql'; // ✅ Explicit central DB
    protected $table = 'ai_tenant_directives';

    public static function getValue(int $tenantId, string $key, $default = null)
    {
        // Önce tenant-specific, sonra global (tenant_id=0)
        $directive = self::where('tenant_id', $tenantId)
            ->where('directive_key', $key)
            ->first();

        if (!$directive) {
            $directive = self::where('tenant_id', 0)
                ->where('directive_key', $key)
                ->first();
        }

        return $directive ? $directive->directive_value : $default;
    }
}
```

---

## 🔧 TROUBLESHOOTING

### **Problem: Flow yüklenmiyor**

```bash
# 1. Tenant context kontrolü
php artisan tinker
>>> tenant()
>>> tenant('id')

# 2. Flow var mı?
>>> $flow = \Modules\AI\App\Models\Flow::where('status', 'active')->first();
>>> dd($flow);

# 3. Yoksa kopyala
php artisan tenant:setup-ai {tenant_id} --from=2
```

### **Problem: Directive okumuyor**

```bash
# 1. Central DB'de directive var mı?
php artisan tinker
>>> \App\Models\AITenantDirective::where('tenant_id', 2)->count()

# 2. Cache temizle
php artisan cache:clear

# 3. Global fallback kontrol
>>> $service = new \App\Services\AI\SimpleDirectiveService();
>>> $value = $service->getDirective('chatbot_system_prompt', 2);
```

### **Problem: Conversation kaydedilmiyor**

```bash
# 1. Model connection kontrolü
php artisan tinker
>>> $conv = new \Modules\AI\App\Models\AIConversation();
>>> $conv->getConnectionName()  # 'mysql' olmalı

# 2. Son conversation'ları kontrol
>>> \Modules\AI\App\Models\AIConversation::latest()->take(5)->get()

# 3. Message relation çalışıyor mu?
>>> $conv = \Modules\AI\App\Models\AIConversation::latest()->first();
>>> $conv->messages()->count()
```

---

## 📊 MEVCUT TENANT DURUMU

```bash
# Tüm tenant'ları kontrol et
php artisan tinker
>>> $tenants = \App\Models\Tenant::all();
>>> foreach ($tenants as $t) {
...     $t->run(function() use ($t) {
...         $flow = \Modules\AI\App\Models\Flow::where('status', 'active')->count();
...         echo "Tenant {$t->id}: {$flow} flows\n";
...     });
... }
```

**Mevcut Durum:**
- ✅ Tenant 1 (tuufi.com) - Central domain
- ✅ Tenant 2 (ixtif.com) - 1 active flow, 23 directives
- ✅ Tenant 3 (depyo.com.tr) - 1 active flow, 23 directives

---

## 🎨 CUSTOM NODE EKLEME (İleri Seviye)

### **Yeni Node Oluşturma**

```php
namespace Modules\AI\App\Services\Workflow\Nodes;

class CustomNode extends BaseNode
{
    public function execute(array $context): array
    {
        // Custom logic
        $result = $this->doSomething($context);

        return [
            'custom_data' => $result,
            'next_node' => $this->getConfig('next_node')
        ];
    }
}
```

### **Flow'a Ekleme**

```json
{
  "nodes": [
    {
      "id": "custom_1",
      "type": "custom_node",
      "name": "Custom İşlem",
      "config": {
        "parameter": "value",
        "next_node": "ai_response"
      }
    }
  ]
}
```

---

## 📚 İLGİLİ DOSYALAR

### **Core Services**
- `/app/Services/AI/SimpleFlowCopyService.php` - Flow/Directive kopyalama
- `/app/Services/AI/SimpleDirectiveService.php` - Global directive desteği

### **Models**
- `/Modules/AI/app/Models/Flow.php` - Flow model (tenant DB)
- `/app/Models/AITenantDirective.php` - Directive model (central DB)
- `/Modules/AI/app/Models/AIConversation.php` - Conversation model

### **Nodes**
- `/Modules/AI/app/Services/Workflow/Nodes/BaseNode.php` - Base class
- `/Modules/AI/app/Services/Workflow/Nodes/AIResponseNode.php` - AI yanıt üretici
- `/Modules/AI/app/Services/Workflow/FlowExecutor.php` - Workflow engine

### **Artisan Commands**
- `/app/Console/Commands/SetupTenantAI.php` - Tenant setup komutu

### **Tenant-Specific Services**
- `/Modules/AI/app/Services/Tenant/Tenant2ProductSearchService.php` - iXtif özel
- `/Modules/AI/app/Services/Tenant/Tenant2PromptService.php` - iXtif özel

---

## ✅ CHECKLIST: YENİ TENANT EKLEME

- [ ] Tenant oluştur (Plesk + Laravel)
- [ ] `php artisan tenant:setup-ai {tenant_id}` çalıştır
- [ ] Flow kopyalandığını doğrula
- [ ] Directives kopyalandığını doğrula
- [ ] Test conversation gönder
- [ ] AI response geldiğini kontrol et
- [ ] Message kaydedildiğini kontrol et

---

## 🚀 ÖZET

**Sistem Özellikleri:**
- ✅ Karmaşa yok - Basit ve anlaşılır mimari
- ✅ Yeni tablo yok - Mevcut yapı kullanılıyor
- ✅ Global support - tenant_id=0 ile global directive
- ✅ Inheritance - Otomatik fallback mekanizması
- ✅ 5 dakikada setup - Artisan command ile hızlı kurulum
- ✅ Production ready - Debug temizlendi, optimize edildi

**Kullanım:**
```bash
# Yeni tenant kur
php artisan tenant:setup-ai {tenant_id}

# Çalışıyor!
```

---

**Son Güncelleme:** 2025-11-09
**Güncelleme:** Model connection'lar eklendi, global directive desteği, tenant setup otomasyonu
