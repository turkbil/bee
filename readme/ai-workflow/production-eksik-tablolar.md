# 🗄️ Production'da Eksik/Boş Tablolar - Kontrol Listesi

**Tarih:** 2025-11-08
**Commit:** 11c44bba878d5ecd4fc212d5056a3af8164c60e5

---

## 📋 ÖZET

Production'da migration çalıştırılınca tablolar OLUŞUR ama **İÇLERİ BOŞ** olur.
Bu tablolara seed data import edilmezse **SİSTEM ÇALIŞMAZ!**

---

## ✅ KONTROL LİSTESİ (Production'da Claude İçin)

### 1️⃣ MIGRATION DURUMU

**Kontrol Et:**
```bash
php artisan migrate:status | grep -E "ai_conversations|ai_messages|ai_workflow_nodes"
```

**Beklenen Durum:**
```
2024_11_04_120002_create_ai_conversations_table ........ [XX] Ran
2025_11_05_023229_create_ai_conversation_messages_table . [XX] Ran
2024_11_04_200000_create_ai_workflow_nodes_table ........ [XX] Ran
```

**Eğer "Pending" Görüyorsan:**
```bash
php artisan migrate
php artisan tenants:migrate
```

---

### 2️⃣ TABLO VAR MI KONTROLÜ

**Central DB (tenant_tuufi veya production central db):**
```bash
mysql -u root -p -e "USE tenant_tuufi; SHOW TABLES LIKE 'ai_%';"
```

**Olması Gerekenler:**
- ✅ `ai_conversations` (boş olabilir)
- ✅ `ai_messages` (boş olabilir)
- ✅ `ai_tenant_directives` (boş olabilir)
- ✅ `ai_workflow_nodes` (boş olabilir)
- ✅ `tenant_conversation_flows` (boş olabilir)
- ✅ `ai_credit_usage` (boş olabilir)
- ✅ `ai_credit_packages` (boş olabilir)
- ✅ `ai_credit_purchases` (boş olabilir)
- ✅ `ai_credit_transactions` (boş olabilir)

**Tenant DB (tenant_ixtif veya production tenant):**
```bash
mysql -u root -p -e "USE tenant_ixtif; SHOW TABLES LIKE 'ai_%';"
```

**Olması Gerekenler:**
- ✅ `ai_conversations` (boş olabilir)
- ✅ `ai_messages` (boş olabilir)
- ✅ `ai_tenant_directives` (boş olabilir)
- ✅ `ai_workflow_nodes` (boş olabilir)
- ✅ `tenant_conversation_flows` (boş olabilir)

---

### 3️⃣ KRİTİK: BOŞ TABLOLARI DOLDUR (ZORUNLU!)

**⚠️ Bu tablolar BOŞ ise sistem çalışmaz:**

#### A. Central DB

```bash
# Kontrol et
mysql -u root -p tenant_tuufi -e "
SELECT
    (SELECT COUNT(*) FROM tenant_conversation_flows) as flows,
    (SELECT COUNT(*) FROM ai_tenant_directives) as directives,
    (SELECT COUNT(*) FROM ai_workflow_nodes) as nodes;
"
```

**Beklenen:**
```
flows: 1+
directives: 2+
nodes: 13+
```

**Eğer 0 ise → Seed data import et:**
```bash
mysql -u root -p tenant_tuufi < readme/ai-workflow/seed-data/central-ai-data.sql
```

---

#### B. Tenant DB

```bash
# Kontrol et
mysql -u root -p tenant_ixtif -e "
SELECT
    (SELECT COUNT(*) FROM tenant_conversation_flows) as flows,
    (SELECT COUNT(*) FROM ai_tenant_directives) as directives;
"
```

**Beklenen:**
```
flows: 1+
directives: 11+
```

**Eğer 0 ise → Seed data import et:**
```bash
mysql -u root -p tenant_ixtif < readme/ai-workflow/seed-data/tenant-ai-data.sql
```

---

### 4️⃣ HATA SENARYOLARI

#### Senaryo 1: Tablo Yok

**Belirti:**
```
ERROR 1146 (42S02): Table 'tenant_tuufi.ai_conversations' doesn't exist
```

**Çözüm:**
```bash
# Migration çalıştır
php artisan migrate
php artisan tenants:migrate
```

---

#### Senaryo 2: Tablolar Var Ama Boş

**Belirti:**
```bash
# Kontrol
mysql -u root -p tenant_tuufi -e "SELECT COUNT(*) FROM tenant_conversation_flows;"
# Sonuç: 0
```

**Çözüm:**
```bash
# Seed data import et
mysql -u root -p tenant_tuufi < readme/ai-workflow/seed-data/central-ai-data.sql
mysql -u root -p tenant_ixtif < readme/ai-workflow/seed-data/tenant-ai-data.sql
```

---

#### Senaryo 3: AI Chatbot Çalışmıyor

**Belirti:**
- Chatbot açılmıyor
- "flow_id not found" hatası
- 500 Internal Server Error

**Debug:**
```bash
# Log kontrol
tail -100 storage/logs/laravel.log | grep -i "flow\|directive"

# Tinker ile kontrol
php artisan tinker
>>> \App\Models\TenantConversationFlow::count();
// Eğer 0 ise → Seed data eksik!

>>> \App\Models\AITenantDirective::count();
// Eğer 0 ise → Seed data eksik!
```

**Çözüm:**
```bash
# Seed data import et (yukarıdaki adımlar)
```

---

## 📊 TABLO İÇERİKLERİ (Referans)

### Central DB (tenant_tuufi)

| Tablo | Local Kayıt | Production Beklenen |
|-------|-------------|---------------------|
| `tenant_conversation_flows` | 1 | 1 (seed ile) |
| `ai_tenant_directives` | 2 | 2 (seed ile) |
| `ai_workflow_nodes` | 13 | 13 (seed ile) |
| `ai_credit_usage` | 690 | 0 (boş başlar) |
| `ai_credit_packages` | ? | 0 (boş başlar) |
| `ai_conversations` | ? | 0 (boş başlar) |
| `ai_messages` | ? | 0 (boş başlar) |

### Tenant DB (tenant_ixtif)

| Tablo | Local Kayıt | Production Beklenen |
|-------|-------------|---------------------|
| `tenant_conversation_flows` | 1 | 1 (seed ile) |
| `ai_tenant_directives` | 11 | 11 (seed ile) |
| `ai_messages` | 266 | 0 (boş başlar) |
| `ai_workflow_nodes` | 0 | 0 (boş) |
| `ai_conversations` | ? | 0 (boş başlar) |

---

## ✅ BAŞARI KRİTERLERİ

Production'da bunlar TAMAM olmalı:

**1. Tablolar Mevcut:**
```bash
php artisan tinker
>>> \Schema::hasTable('ai_conversations'); // true
>>> \Schema::hasTable('ai_messages'); // true
>>> \Schema::hasTable('tenant_conversation_flows'); // true
>>> \Schema::hasTable('ai_tenant_directives'); // true
>>> \Schema::hasTable('ai_workflow_nodes'); // true
```

**2. Seed Data İmport Edilmiş:**
```bash
>>> \App\Models\TenantConversationFlow::count(); // 1+
>>> \App\Models\AITenantDirective::count(); // 11+ (tenant) veya 2+ (central)
>>> \App\Models\AIWorkflowNode::count(); // 13+
```

**3. AI Chatbot Çalışıyor:**
- https://ixtif.com ana sayfasında chatbot butonu görünüyor
- Butona tıklayınca pencere açılıyor
- Mesaj gönderiliyor
- AI yanıt veriyor

---

## 🎯 HIZLI KONTROL KOMUTLARİ

**Tek seferde tüm kontrolleri yap:**

```bash
# 1. Migration durumu
php artisan migrate:status | grep -E "ai_|conversation_flow"

# 2. Tablo varlığı
mysql -u root -p tenant_tuufi -e "SHOW TABLES LIKE 'ai_%';" | wc -l
# Beklenen: 8+ tablo

# 3. Seed data kontrolü
mysql -u root -p tenant_tuufi -e "
SELECT
    'flows' as tablo, COUNT(*) as kayit FROM tenant_conversation_flows
UNION ALL SELECT 'directives', COUNT(*) FROM ai_tenant_directives
UNION ALL SELECT 'nodes', COUNT(*) FROM ai_workflow_nodes;
"
# Beklenen: Her biri 1+

# 4. Tenant DB kontrolü
mysql -u root -p tenant_ixtif -e "
SELECT
    'flows' as tablo, COUNT(*) as kayit FROM tenant_conversation_flows
UNION ALL SELECT 'directives', COUNT(*) FROM ai_tenant_directives;
"
# Beklenen: flows=1+, directives=11+
```

**Eğer hepsi OK ise:**
✅ Production hazır!
✅ AI chatbot test et

**Eğer eksik varsa:**
❌ Yukarıdaki adımları takip et
❌ Seed data import et

---

## 📞 ACİL DURUM

**Hiçbir şey çalışmıyorsa:**

```bash
# 1. Full reset (SADECE GEREKIRSE!)
php artisan migrate:fresh
php artisan tenants:migrate --fresh

# 2. Seed data import
mysql -u root -p tenant_tuufi < readme/ai-workflow/seed-data/central-ai-data.sql
mysql -u root -p tenant_ixtif < readme/ai-workflow/seed-data/tenant-ai-data.sql

# 3. Cache clear
php artisan cache:clear
php artisan view:clear
curl -s -k https://ixtif.com/opcache-reset.php

# 4. Test
curl -s -k -I "https://ixtif.com/" | grep HTTP
```

**⚠️ DİKKAT:** `migrate:fresh` tüm verileri siler! Sadece son çare!

---

**Son Güncelleme:** 2025-11-08
**Hazırlayan:** Claude AI Assistant
