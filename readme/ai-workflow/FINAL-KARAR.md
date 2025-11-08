# 🎯 FİNAL KARAR - AI Tablo Dağılımı

**Tarih:** 2025-11-08
**Karar Veren:** Kullanıcı
**Durum:** ONAYLANDI

---

## 📊 TABLO DAĞILIMI

### 1️⃣ **ai_tenant_directives** → CENTRAL DB ✅

**Sebep:**
- Her tenant farklı ayarlara sahip
- AMA central gerekirse template olarak kopyalayabilir
- Merkezi yönetim avantajı
- Performans kaybı yok (tenant_id index'li)
- Central'de 1 numaralı tenant zaten var (tuufi.com)

**Yapı:**
```sql
ai_tenant_directives (CENTRAL DB)
├── id
├── tenant_id (2=ixtif, 3=başka site)
├── directive_key ("chatbot_name", "system_prompt" vb.)
├── value
└── timestamps
```

**Avantajlar:**
- ✅ Tüm tenant'ların ayarlarını tek yerden görebilirsin
- ✅ Template'ler oluşturabilirsin
- ✅ Yeni tenant açılınca mevcut tenant'tan kopyalayabilirsin
- ✅ Merkezi kontrol

---

### 2️⃣ **tenant_conversation_flows** → TENANT DB ✅

**Sebep:**
- Her tenant BAĞIMSIZ çalışacak
- Birbirini tanımayan siteler ve sektörler
- Tenant bazlı değişiklikler eklenebilir/çıkarılabilir
- Tam özelleştirme gerekli

**Yapı:**
```sql
tenant_conversation_flows (TENANT DB - her tenant'ta ayrı)
├── id
├── flow_name
├── flow_data (JSON)
├── start_node_id
└── timestamps

NOT: tenant_id YOK (çünkü zaten tenant DB'sinde)
```

**Avantajlar:**
- ✅ Tamamen bağımsız
- ✅ Tenant veritabanı ile birlikte yedeklenir
- ✅ Tenant bazlı özelleştirme kolay
- ✅ Daha basit sorgular (tenant_id filtresi yok)

---

### 3️⃣ **ai_workflow_nodes** → TENANT DB ✅

**Sebep:**
- Tamamen müşteri taleplerine göre
- Biri "Merhaba", biri "Selamun Aleykum"
- Biri kitap satar, biri transpalet
- Custom node'lar olacak

**Yapı:**
```sql
ai_workflow_nodes (TENANT DB - her tenant'ta ayrı)
├── id
├── node_id ("node_greeting_custom")
├── node_type ("ai_response")
├── node_name
├── config (JSON)
└── timestamps

NOT: tenant_id YOK (çünkü zaten tenant DB'sinde)
```

**Avantajlar:**
- ✅ Her tenant custom node'lar yapabilir
- ✅ Global node'lar kodda zaten var
- ✅ Tenant-specific özelleştirmeler kolay

---

## 🔄 MİGRATION DURUMU

### CENTRAL DB Migration'ları:

**TUTULACAK:**
- ✅ `2024_11_04_120002_create_ai_conversations_table.php`
- ✅ `2025_11_05_023229_create_ai_conversation_messages_table.php`
- ✅ `*_create_ai_credit_*.php`
- ✅ `*_create_ai_providers*.php`

**EKLENECEK:**
- ✅ `*_create_ai_tenant_directives_table.php` (eğer yoksa)

### TENANT DB Migration'ları:

**TUTULACAK:**
- ✅ `*_create_tenant_conversation_flows_table.php`
- ✅ `*_create_ai_workflow_nodes_table.php`
- ✅ `*_create_ai_knowledge_base_table.php`

**SİLİNECEK:**
- ❌ `2024_11_04_120002_create_ai_conversations_table.php` (ZATEN SİLİNDİ)
- ❌ `*_create_ai_messages*.php` (eğer varsa)
- ❌ `*_create_ai_tenant_directives*.php` (eğer varsa - CENTRAL'e taşındı)

---

## 📦 SEED DATA DAĞILIMI

### CENTRAL DB Seed:

```sql
-- central-ai-data.sql

-- ai_conversations (BOŞ - sistem dolduracak)
-- ai_messages (BOŞ - sistem dolduracak)

-- ai_tenant_directives (TAŞINDI - tenant'tan gelecek)
INSERT INTO ai_tenant_directives (tenant_id, directive_key, value, ...) VALUES
(2, 'chatbot_name', 'İxtif Yapay Zeka Asistanı', ...),
(2, 'system_prompt', 'Sen İxtif.com...', ...),
(2, 'max_tokens', '500', ...);
-- ... 11 kayıt (tenant_id=2 için)
```

### TENANT DB Seed:

```sql
-- tenant-ai-data.sql

-- tenant_conversation_flows (KALDI - tenant'ta)
INSERT INTO tenant_conversation_flows (flow_name, flow_data, ...) VALUES
('İxtif.com E-Ticaret Akışı', '{"nodes": [...]}', ...);

-- ai_knowledge_base (KALDI - tenant'ta)
INSERT INTO ai_knowledge_base (...) VALUES (...);

-- ai_workflow_nodes (KALDI - tenant'ta, eğer custom varsa)
```

---

## 🔧 MODEL DEĞİŞİKLİKLERİ

### 1. AIConversation.php

```php
// ✅ DOĞRU (ZATEN DÜZELTİLDİ)
protected $connection = 'mysql'; // CENTRAL DB
protected $table = 'ai_conversations';
```

### 2. AIMessage.php

```php
// ✅ DOĞRU (ZATEN DÜZELTİLDİ)
protected $connection = 'mysql'; // CENTRAL DB
protected $table = 'ai_messages';
```

### 3. AITenantDirective.php

```php
// ✅ DEĞİŞTİRİLECEK
protected $connection = 'mysql'; // CENTRAL'e taşındı
protected $table = 'ai_tenant_directives';

// Tenant filtreleme için scope
public function scopeForTenant($query, $tenantId)
{
    return $query->where('tenant_id', $tenantId);
}
```

### 4. TenantConversationFlow.php

```php
// ✅ DOĞRU (DEĞİŞMEYECEK)
// No $connection = TENANT DB (default)
protected $table = 'tenant_conversation_flows';
```

### 5. AIWorkflowNode.php

```php
// ✅ DOĞRU (DEĞİŞMEYECEK)
// No $connection = TENANT DB (default)
protected $table = 'ai_workflow_nodes';
```

---

## 📊 SON DURUM ÖZET

| Tablo | Nerede? | tenant_id var mı? | Sebep |
|-------|---------|-------------------|-------|
| `ai_conversations` | CENTRAL | ✅ Evet | Tüm konuşmalar merkezi takip |
| `ai_messages` | CENTRAL | ❌ Hayır | Conversation'dan gelir |
| `ai_credit_*` | CENTRAL | ✅ Evet | Kredi sistemi merkezi |
| `ai_providers` | CENTRAL | ❌ Hayır | Global provider'lar |
| **`ai_tenant_directives`** | **CENTRAL** | **✅ Evet** | **Merkezi yönetim + template** |
| **`tenant_conversation_flows`** | **TENANT** | **❌ Hayır** | **Bağımsız flow'lar** |
| **`ai_workflow_nodes`** | **TENANT** | **❌ Hayır** | **Custom node'lar** |
| `ai_knowledge_base` | TENANT | ❌ Hayır | Her tenant farklı bilgi |

---

## ✅ ONAYLANDI

**Karar:** KARMA (C)

**Detay:**
- ai_tenant_directives → CENTRAL (tenant_id ile)
- tenant_conversation_flows → TENANT
- ai_workflow_nodes → TENANT

**Sonraki Adım:** Bu karara göre düzeltmeleri uygula!

---

**Hazırlayan:** Claude AI Assistant
**Onaylayan:** Kullanıcı
**Durum:** ✅ ONAYLANDI - Uygulamaya Geç
