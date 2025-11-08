# 🗄️ AI Workflow v2.3 - SQL Deployment Script

**Tarih:** 2025-11-08
**Versiyon:** v2.3
**Commit:** 44bf15fe1c965000637c143af58d3659a60b84e6

---

## 📋 ÖZET

Bu deployment'ta **MANUEL SQL ÇALIŞTIRMAYA GEREK YOK**.

**Sebep:**
- Model değişiklikleri sadece kod seviyesinde (connection yorumu)
- Tablo yapısı değişmedi
- Yeni kolon eklenmedi
- Mevcut migration'lar zaten mevcut

**Yapılacak:**
- Sadece PENDING migration'ları çalıştır (eğer varsa)
- Laravel migration komutlarını kullan (manuel SQL değil)

---

## ✅ MIGRATION DURUMU KONTROLÜ

### Local Durum (laravel.test):

```
✅ ai_tenant_directives_table ......... Ran
⏳ ai_conversations_table ............. Pending
✅ ai_workflow_nodes_table ............ Ran
```

**Pending Migration:**
- `2024_11_04_120002_create_ai_conversations_table.php`

**Bu normal mi?**
Evet! Local ortamda migration çalıştırmadıysak pending olabilir.

---

## 🎯 PRODUCTION'DA YAPILACAK İŞLEM

### ADIM 1: Migration Durumunu Kontrol Et

```bash
# SSH production sunucuya
ssh tuufi.com_@vh163.timeweb.ru
cd /var/www/vhosts/tuufi.com/httpdocs/

# Migration durumu kontrol et
php artisan migrate:status | grep -E "ai_conversations"

# Beklenen çıktılar:
# Senaryo 1: [XX] Ran → Zaten çalışmış, hiçbir şey yapma
# Senaryo 2: Pending → Adım 2'ye geç
```

---

### ADIM 2: Pending Migration'ları Çalıştır (Eğer Varsa)

```bash
# ⚠️ SADECE PENDING VARSA ÇALIŞTIR!

# Önce dry-run (test)
php artisan migrate --pretend

# Çıktıyı oku, sorun yoksa çalıştır
php artisan migrate

# Beklenen çıktı:
# Migrating: 2024_11_04_120002_create_ai_conversations_table
# Migrated:  2024_11_04_120002_create_ai_conversations_table (XX ms)
```

---

### ADIM 3: Doğrulama

```bash
# Migration başarılı mı kontrol et
php artisan migrate:status | grep "ai_conversations"

# Beklenen: [XX] Ran

# Tablo oluştu mu kontrol et
php artisan tinker

>>> \Schema::hasTable('ai_conversations');
// Beklenen: true

>>> \Schema::hasColumn('ai_conversations', 'context_data');
// Beklenen: true

>>> \Schema::hasColumn('ai_conversations', 'state_history');
// Beklenen: true

>>> exit
```

---

## 🔍 TABLO YAPISI (Referans)

### ai_conversations Tablosu

**Kolonlar:**

| Kolon | Tip | Null | Açıklama |
|-------|-----|------|----------|
| `id` | bigint unsigned | NO | Primary key |
| `tenant_id` | int unsigned | NO | Hangi tenant (2=ixtif.com) |
| `flow_id` | bigint unsigned | NO | Hangi workflow akışı |
| `current_node_id` | varchar(50) | YES | Şu anda hangi node'da |
| `session_id` | varchar(100) | NO | Browser session ID (unique) |
| `user_id` | bigint unsigned | YES | Kayıtlı kullanıcı ID (nullable) |
| `context_data` | json | YES | Sohbet verileri (JSON) |
| `state_history` | json | YES | Node geçiş geçmişi (JSON) |
| `created_at` | timestamp | YES | Oluşturulma zamanı |
| `updated_at` | timestamp | YES | Güncellenme zamanı |

**İndeksler:**
- PRIMARY KEY (`id`)
- UNIQUE KEY `session_id` (`session_id`)
- INDEX `tenant_flow` (`tenant_id`, `flow_id`)
- FOREIGN KEY (`flow_id`) REFERENCES `tenant_conversation_flows` (`id`) ON DELETE CASCADE

---

### ai_messages Tablosu

**Kolonlar:**

| Kolon | Tip | Null | Açıklama |
|-------|-----|------|----------|
| `id` | bigint unsigned | NO | Primary key |
| `conversation_id` | bigint unsigned | NO | Hangi konuşma |
| `role` | varchar(50) | NO | user / assistant |
| `content` | text | NO | Mesaj içeriği |
| `created_at` | timestamp | YES | Oluşturulma zamanı |
| `updated_at` | timestamp | YES | Güncellenme zamanı |

**İndeksler:**
- PRIMARY KEY (`id`)
- INDEX `conversation_id` (`conversation_id`)
- FOREIGN KEY (`conversation_id`) REFERENCES `ai_conversations` (`id`) ON DELETE CASCADE

---

## 🚨 ACİL DURUM: Manuel SQL (Son Çare!)

**⚠️ SADECE MIGRATION ÇALIŞMAZSA KULLAN!**

Eğer `php artisan migrate` hata verirse, manuel SQL:

### Central Database (tenant_tuufi):

```sql
-- ai_conversations tablosu
CREATE TABLE IF NOT EXISTS `ai_conversations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int unsigned NOT NULL COMMENT 'Hangi tenant (örn: 2=ixtif.com)',
  `flow_id` bigint unsigned NOT NULL COMMENT 'Hangi akış kullanılıyor - tenant_conversation_flows tablosundan',
  `current_node_id` varchar(50) DEFAULT NULL COMMENT 'Şu anda hangi node\'da - Akış içinde konum (örn: "node_greeting_1")',
  `session_id` varchar(100) NOT NULL COMMENT 'Browser session ID - Her ziyaretçi için unique (cookie/localStorage)',
  `user_id` bigint unsigned DEFAULT NULL COMMENT 'Kayıtlı kullanıcı ID - Varsa users tablosundan, yoksa NULL (guest)',
  `context_data` json DEFAULT NULL COMMENT 'Sohbet sırasında toplanan veriler - Telefon, email, tercihler vb. JSON formatında',
  `state_history` json DEFAULT NULL COMMENT 'Node geçiş geçmişi - Hangi node\'lardan geçti, ne zaman, JSON array [{node_id, timestamp, success}]',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ai_conversations_session_id_unique` (`session_id`),
  KEY `ai_conversations_session_id_index` (`session_id`),
  KEY `ai_conversations_tenant_id_flow_id_index` (`tenant_id`,`flow_id`),
  CONSTRAINT `ai_conversations_flow_id_foreign` FOREIGN KEY (`flow_id`) REFERENCES `tenant_conversation_flows` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ai_messages tablosu
CREATE TABLE IF NOT EXISTS `ai_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint unsigned NOT NULL COMMENT 'Hangi konuşmaya ait',
  `role` varchar(50) NOT NULL COMMENT 'user = Kullanıcı mesajı, assistant = AI yanıtı',
  `content` text NOT NULL COMMENT 'Mesaj içeriği - Markdown formatında',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ai_messages_conversation_id_index` (`conversation_id`),
  CONSTRAINT `ai_messages_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `ai_conversations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Tenant Database (tenant_ixtif):

```sql
-- Aynı SQL'i tenant database'de de çalıştır
-- Database: tenant_ixtif (veya aktif tenant'ınız)

USE tenant_ixtif;

-- ai_conversations tablosu
CREATE TABLE IF NOT EXISTS `ai_conversations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int unsigned NOT NULL COMMENT 'Hangi tenant (örn: 2=ixtif.com)',
  `flow_id` bigint unsigned NOT NULL COMMENT 'Hangi akış kullanılıyor - tenant_conversation_flows tablosundan',
  `current_node_id` varchar(50) DEFAULT NULL COMMENT 'Şu anda hangi node\'da - Akış içinde konum (örn: "node_greeting_1")',
  `session_id` varchar(100) NOT NULL COMMENT 'Browser session ID - Her ziyaretçi için unique (cookie/localStorage)',
  `user_id` bigint unsigned DEFAULT NULL COMMENT 'Kayıtlı kullanıcı ID - Varsa users tablosundan, yoksa NULL (guest)',
  `context_data` json DEFAULT NULL COMMENT 'Sohbet sırasında toplanan veriler - Telefon, email, tercihler vb. JSON formatında',
  `state_history` json DEFAULT NULL COMMENT 'Node geçiş geçmişi - Hangi node\'lardan geçti, ne zaman, JSON array [{node_id, timestamp, success}]',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ai_conversations_session_id_unique` (`session_id`),
  KEY `ai_conversations_session_id_index` (`session_id`),
  KEY `ai_conversations_tenant_id_flow_id_index` (`tenant_id`,`flow_id`),
  CONSTRAINT `ai_conversations_flow_id_foreign` FOREIGN KEY (`flow_id`) REFERENCES `tenant_conversation_flows` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ai_messages tablosu
CREATE TABLE IF NOT EXISTS `ai_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint unsigned NOT NULL COMMENT 'Hangi konuşmaya ait',
  `role` varchar(50) NOT NULL COMMENT 'user = Kullanıcı mesajı, assistant = AI yanıtı',
  `content` text NOT NULL COMMENT 'Mesaj içeriği - Markdown formatında',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ai_messages_conversation_id_index` (`conversation_id`),
  CONSTRAINT `ai_messages_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `ai_conversations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## ✅ BAŞARI KRİTERLERİ

Migration/SQL başarılı sayılır eğer:

```bash
# 1. Tablolar mevcut
php artisan tinker
>>> \Schema::hasTable('ai_conversations');
// true

>>> \Schema::hasTable('ai_messages');
// true

# 2. Kolonlar doğru
>>> \Schema::getColumnListing('ai_conversations');
// ["id", "tenant_id", "flow_id", "current_node_id", "session_id", "user_id", "context_data", "state_history", "created_at", "updated_at"]

# 3. Model çalışıyor
>>> \Modules\AI\App\Models\AIConversation::count();
// Herhangi bir sayı (hata vermemeli)

>>> exit
```

---

## 📊 CHECKLIST

### ✅ Ön Kontrol
- [ ] Migration status kontrol edildi (`php artisan migrate:status`)
- [ ] Pending migration var mı kontrol edildi

### ✅ Migration Çalıştırma (Eğer Pending Varsa)
- [ ] `php artisan migrate --pretend` ile test edildi
- [ ] `php artisan migrate` çalıştırıldı
- [ ] Hata varsa log kontrol edildi

### ✅ Doğrulama
- [ ] Migration status tekrar kontrol edildi (Ran olmalı)
- [ ] Tablolar mevcut (tinker ile kontrol)
- [ ] Kolonlar doğru (Schema::getColumnListing)
- [ ] Model çalışıyor (count query)

### ✅ Fonksiyonel Test
- [ ] AI chatbot açılıyor
- [ ] Mesaj gönderiliyor
- [ ] ai_conversations tablosuna kayıt düşüyor
- [ ] ai_messages tablosuna kayıt düşüyor
- [ ] Conversation history çalışıyor

---

## 🐛 SORUN GİDERME

### Problem 1: Migration "already exists" Hatası

**Belirti:**
```
Base table or view already exists: 1050 Table 'ai_conversations' already exists
```

**Açıklama:** Tablo zaten mevcut, migration tekrar çalıştırılmaya çalışılmış.

**Çözüm:**
```bash
# Migration tablosuna manuel ekle (migration çalışmış sayılsın)
php artisan tinker

>>> DB::table('migrations')->insert([
    'migration' => '2024_11_04_120002_create_ai_conversations_table',
    'batch' => DB::table('migrations')->max('batch') + 1
]);

>>> exit

# Kontrol et
php artisan migrate:status | grep "ai_conversations"
# Artık "Ran" göstermeli
```

---

### Problem 2: Foreign Key Constraint Hatası

**Belirti:**
```
Cannot add foreign key constraint
```

**Sebep:** `tenant_conversation_flows` tablosu mevcut değil.

**Çözüm:**
```bash
# Önce tenant_conversation_flows migration'ını çalıştır
php artisan migrate --path=database/migrations/2024_11_04_120000_create_tenant_conversation_flows_table.php

# Sonra ai_conversations migration'ını çalıştır
php artisan migrate --path=database/migrations/2024_11_04_120002_create_ai_conversations_table.php
```

---

### Problem 3: Tenant Database'de Tablo Yok

**Belirti:**
```
SQLSTATE[42S02]: Base table or view not found: 1146 Table 'tenant_ixtif.ai_conversations' doesn't exist
```

**Sebep:** Tenant migration çalıştırılmamış.

**Çözüm:**
```bash
# Tenant migration'ları çalıştır
php artisan tenants:migrate

# Veya spesifik tenant için
php artisan tenants:migrate --tenants=2
```

---

## 📝 NOTLAR

- Bu deployment'ta **DATABASE YAPISI DEĞİŞMEDİ**
- Sadece model dosyasında **connection yorumu** değişti (kod seviyesinde)
- Migration dosyaları **zaten mevcuttu**, yeni migration yok
- Production'da pending migration varsa **Laravel migration komutuyla çalıştır**
- Manuel SQL **son çare** (sadece migration komutu çalışmazsa)
- Tenant database'lerde de **aynı migration'lar çalıştırılmalı**

---

## 🎯 ÖNERİ

**En güvenli yöntem:**

1. ✅ Production'da migration status kontrol et
2. ✅ Pending varsa `php artisan migrate` çalıştır
3. ✅ Tenant migration'ları da çalıştır: `php artisan tenants:migrate`
4. ✅ Doğrulama yap (tinker ile tablo kontrol)
5. ✅ Fonksiyonel test et (chatbot çalışıyor mu)

**Manuel SQL kullanma!** Laravel migration sistemi daha güvenli.

---

**Son Güncelleme:** 2025-11-08
**Hazırlayan:** Claude AI Assistant
**Commit:** 44bf15fe1c965000637c143af58d3659a60b84e6
