# 🚨 ACİL DÜZELTME PLANI - AI Mimari Hatası

**Tarih:** 2025-11-08
**Sorun:** AI tablolarını yanlış yere koydum
**Etki:** Kritik mimari hatası, düzeltilmezse sistem bozuk çalışır

---

## ❌ YANLIŞ YAPTIKLARIM

1. **Model'lerde `connection` yorumunu kaldırdım**
   - AIConversation → Tenant DB'ye düşüyor (YANLIŞ!)
   - AIMessage → Tenant DB'ye düşüyor (YANLIŞ!)

2. **Tenant migration'lar oluşturdum**
   - ai_conversations tenant DB'de (YANLIŞ!)
   - Olması gereken: CENTRAL DB'de (tenant_id ile)

3. **Seed data'yı hem central hem tenant'a koydum**
   - Konuşmalar central'de olmalı

---

## ✅ DOĞRU MİMARİ

### KURAL 1: AI = GLOBAL MODÜL (tuufi.com yönetiyor)

```
AI MODÜLÜ MİMARİSİ:
├── tuufi.com (CENTRAL) → AI'yi yönetiyor
├── Tüm tenant'lara HİZMET veriyor
├── KREDİ SİSTEMİ: Central'de (tuufi dağıtıyor)
└── KONUŞMALAR: Central'de (tenant_id ile ayırt)
```

### KURAL 2: Bazı Tablolar Tenant-Specific

```
TENANT-SPECIFIC (her tenant'ın kendine özel):
├── ai_knowledge_base (bilgi bankası)
├── ai_tenant_directives (prompt ayarları)
├── tenant_conversation_flows (akışlar)
└── ai_workflow_nodes (node tanımları)
```

---

## 📊 TABLO DAĞILIMI (Hedef Durum)

### CENTRAL DB (tenant_tuufi):

| Tablo | Neden Central? | tenant_id var mı? |
|-------|----------------|-------------------|
| `ai_conversations` | Tüm tenant konuşmaları merkezi takip | ✅ Evet |
| `ai_messages` | Konuşmalara bağlı (conversation.tenant_id üzerinden) | ❌ Hayır (conversation'dan gelir) |
| `ai_credit_packages` | tuufi.com kredi paketlerini tanımlıyor | ❌ Hayır (global) |
| `ai_credit_purchases` | Hangi tenant ne aldı? | ✅ Evet |
| `ai_credit_usage` | Hangi tenant ne kadar harcadı? | ✅ Evet |
| `ai_credit_transactions` | Kredi hareketleri | ✅ Evet |
| `ai_providers` | OpenAI, Claude vb. provider'lar | ❌ Hayır (global) |
| `ai_provider_models` | GPT-4, Claude-3 vb. modeller | ❌ Hayır (global) |
| `ai_features` | Blog, SEO vb. AI özellikleri | ❌ Hayır (global) |
| `ai_tenant_profiles` | Tenant AI profilleri | ✅ Evet |

**Toplam:** ~15-20 tablo (global AI yönetimi)

---

### TENANT DB (tenant_ixtif, tenant_X):

| Tablo | Neden Tenant? | Her tenant farklı mı? |
|-------|---------------|----------------------|
| `ai_knowledge_base` | Her tenant'ın kendi bilgi bankası | ✅ Evet |
| `ai_tenant_directives` | Her tenant'ın kendi prompt ayarları | ✅ Evet |
| `tenant_conversation_flows` | Her tenant'ın kendi akışları | ✅ Evet |
| `ai_workflow_nodes` | Her tenant'ın kendi node'ları (opsiyonel) | ✅ Evet |

**Toplam:** 4 tablo (tenant-specific AI ayarları)

---

## 🔧 YAPILACAK DÜZELTMELER

### 1️⃣ MODEL DOSYALARI

**A. AIConversation.php**
```php
// ✅ DOĞRU
protected $connection = 'mysql'; // Force CENTRAL DB
protected $table = 'ai_conversations';

// ❌ YANLIŞ (benim yaptığım)
// No connection = tenant DB
```

**B. AIMessage.php**
```php
// ✅ DOĞRU
protected $connection = 'mysql'; // Force CENTRAL DB
protected $table = 'ai_messages';

// ❌ YANLIŞ (benim yaptığım)
// No connection = tenant DB
```

**C. AIKnowledgeBase.php (kontrol et)**
```php
// ✅ DOĞRU
// No connection = tenant DB (her tenant'ın kendi bilgisi)
protected $table = 'ai_knowledge_base';
```

**D. AITenantDirective.php (kontrol et)**
```php
// ✅ DOĞRU
// No connection = tenant DB (her tenant'ın kendi prompt'ları)
protected $table = 'ai_tenant_directives';
```

---

### 2️⃣ MIGRATION DOSYALARI

**A. CENTRAL DB Migration'ları**

**TUTULACAK:**
- ✅ `database/migrations/2024_11_04_120002_create_ai_conversations_table.php`
- ✅ `database/migrations/2025_11_05_023229_create_ai_conversation_messages_table.php`
- ✅ `database/migrations/*_create_ai_credit_*.php`

**B. TENANT DB Migration'ları**

**SİLİNECEK:**
- ❌ `database/migrations/tenant/2024_11_04_120002_create_ai_conversations_table.php` (ZATEN SİLDİM)
- ❌ `database/migrations/tenant/*ai_messages*.php` (eğer varsa)

**TUTULACAK:**
- ✅ `database/migrations/tenant/*knowledge_base*.php`
- ✅ `database/migrations/tenant/*tenant_directives*.php`
- ✅ `database/migrations/tenant/*conversation_flows*.php`
- ✅ `database/migrations/tenant/*workflow_nodes*.php`

---

### 3️⃣ SEED DATA

**A. CENTRAL DB Seed (central-ai-data.sql)**

**EKLENECEK:**
```sql
-- ai_conversations (boş başlar, sistem konuşma kaydedecek)
-- ai_messages (boş başlar)
-- tenant_conversation_flows (1 kayıt - default flow, tenant_id=2)
-- ai_tenant_directives (2 kayıt - default directives, tenant_id=2)
```

**ÇIKARILACAK:**
- ❌ Hiçbir şey çıkarma, sadece ekle

**B. TENANT DB Seed (tenant-ai-data.sql)**

**TUTULACAK:**
```sql
-- ai_knowledge_base (ixtif.com bilgi bankası)
-- ai_tenant_directives (ixtif.com prompt ayarları) - ASLINDA BUNLAR DA CENTRAL'E GİTMELİ Mİ?
-- tenant_conversation_flows (ixtif.com akışları) - ASLINDA BUNLAR DA CENTRAL'E GİTMELİ Mİ?
```

⚠️ **KARAR GEREKLİ:** Directives ve Flows TENANT'ta mı yoksa CENTRAL'de mi? (tenant_id ile)

---

### 4️⃣ KARARLANMASI GEREKEN TABLOLAR

**Soru:** Bunlar CENTRAL'de mi (tenant_id ile) yoksa TENANT'ta mı?

| Tablo | Şu An Nerede? | Nereye Gitmeli? | Sebep? |
|-------|---------------|-----------------|--------|
| `ai_knowledge_base` | TENANT | **TENANT** | ✅ Her tenant'ın kendi bilgisi |
| `ai_tenant_directives` | TENANT | **???** | ❓ Central'de tenant_id ile mi? |
| `tenant_conversation_flows` | TENANT | **???** | ❓ Central'de tenant_id ile mi? |
| `ai_workflow_nodes` | TENANT | **TENANT** | ✅ Tenant-specific node'lar |

**Önerim:**
- `ai_knowledge_base` → TENANT'ta kalsın (her tenant farklı bilgi)
- `ai_tenant_directives` → CENTRAL'e taşınsın (tenant_id ile, merkezi yönetim)
- `tenant_conversation_flows` → CENTRAL'e taşınsın (tenant_id ile, merkezi yönetim)
- `ai_workflow_nodes` → ??? (global node'lar var mı?)

---

## 🎯 ADIM ADIM DÜZELTME PLANI

### ADIM 1: Model Dosyalarını Düzelt

- [x] AIConversation.php → `connection = 'mysql'` ekle
- [x] AIMessage.php → `connection = 'mysql'` ekle
- [ ] AIKnowledgeBase.php → `connection` YOK (tenant'ta kalmalı)
- [ ] AITenantDirective.php → KARAR: Central mi tenant mi?
- [ ] TenantConversationFlow.php → KARAR: Central mi tenant mi?

### ADIM 2: Migration'ları Düzenle

- [x] Tenant ai_conversations migration'ı sil
- [ ] Tenant ai_messages migration'ı var mı kontrol et, varsa sil
- [ ] Central migration'ları kontrol et (doğru mu?)

### ADIM 3: Seed Data'yı Yeniden Düzenle

- [ ] KARAR: Directives ve Flows nereye?
- [ ] Central seed: ai_conversations (boş)
- [ ] Central seed: tenant_conversation_flows (tenant_id ile)
- [ ] Central seed: ai_tenant_directives (tenant_id ile)
- [ ] Tenant seed: ai_knowledge_base (sadece)

### ADIM 4: Kılavuzları Güncelle

- [ ] deployment-sql-v2.3.md → Doğru mimariyi yaz
- [ ] production-eksik-tablolar.md → Doğru tabloları belirt
- [ ] production-ai-yapilacaklar.md → Doğru adımları yaz

### ADIM 5: Test Et

- [ ] Local'de test: ai_conversations central'de mi?
- [ ] Model test: AIConversation::create() central'e mi yazıyor?
- [ ] Migration test: tenant'ta ai_conversations var mı? (olmamalı)

### ADIM 6: Git Push

- [ ] Tüm değişiklikleri commit et
- [ ] Detaylı commit mesajı yaz (mimari düzeltme)
- [ ] Push et

---

## ⚠️ KRİTİK SORULAR (Cevap Gerekli)

### SORU 1: ai_tenant_directives NEREDE OLMALI?

**Seçenek A: CENTRAL'de (tenant_id ile)**
- ✅ Merkezi yönetim
- ✅ Tüm tenant'ların prompt'larını tek yerden görebilirsin
- ❌ Her tenant DB'sinde yok, daha karmaşık

**Seçenek B: TENANT'ta (her tenant'ta ayrı)**
- ✅ Tenant-specific, basit
- ✅ Her tenant kendi prompt'larını yönetir
- ❌ Merkezi kontrol zor

**KARAR:** ???

---

### SORU 2: tenant_conversation_flows NEREDE OLMALI?

**Seçenek A: CENTRAL'de (tenant_id ile)**
- ✅ Merkezi yönetim
- ✅ Flow template'leri paylaşılabilir
- ❌ Daha karmaşık

**Seçenek B: TENANT'ta (her tenant'ta ayrı)**
- ✅ Tenant-specific flow'lar
- ✅ Basit
- ❌ Merkezi kontrol zor

**KARAR:** ???

---

### SORU 3: ai_workflow_nodes NEREDE OLMALI?

**Seçenek A: CENTRAL'de (global node definitions)**
- ✅ Tüm tenant'lar aynı node'ları kullanır
- ✅ Kod ile tanımlı node'lar var zaten
- ❌ Custom node'lar nasıl?

**Seçenek B: TENANT'ta (her tenant custom node)**
- ✅ Tenant-specific node'lar
- ❌ Global node'lar nasıl?

**KARAR:** ???

---

## 📞 SONRAKI ADIM

**BEN SANA SORUYORUM:**

1. `ai_tenant_directives` → CENTRAL mi TENANT mi?
2. `tenant_conversation_flows` → CENTRAL mi TENANT mi?
3. `ai_workflow_nodes` → CENTRAL mi TENANT mi?

**Bu 3 soruyu cevapla, sonra devam edelim!**

---

**Hazırlayan:** Claude AI Assistant
**Durum:** ⏸️ Karar Bekleniyor
