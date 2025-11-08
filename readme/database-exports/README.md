# Database Migration & Export Raporu

📅 **Tarih:** 2025-11-08
🤖 **Oluşturan:** Claude Code
🎯 **Amaç:** Sunucuya aktarım için local database değişikliklerini dökümante etme

---

## 📋 İçindekiler

1. [Silinen/Birleştirilen Migration'lar](#silinen-migration-lar)
2. [Central DB'ye Taşınan Tablolar](#central-db-ye-taşınan-tablolar)
3. [Export Edilen Veriler](#export-edilen-veriler)
4. [Sunucuda Yapılması Gerekenler](#sunucuda-yapılması-gerekenler)

---

## 🗑️ Silinen/Birleştirilen Migration'lar {#silinen-migration-lar}

### AI Sistemi (Tenant → Central Taşıma)

**Commit:** `f40cfdc5f` - 🎯 FIX: AI Mimari Düzeltme - Directives Central DB'ye Taşındı

**Tenant'tan Silinen:**
```
database/migrations/tenant/2024_11_04_120001_create_ai_tenant_directives_table.php
database/migrations/tenant/2024_11_04_120002_create_ai_conversations_table.php
```

**Yeni Durum:**
- `ai_tenant_directives` → **Central DB**'de (tenant_id ile filter)
- `ai_conversations` → **Central DB**'de (zaten vardı)

### SEO Settings Temizliği

**Commit:** `bf0c8f87d` - 🧹 CLEANUP: Migration temizliği

**Silinen Duplicate Migration'lar:**
```
database/migrations/2025_09_26_131240_remove_redundant_ai_columns_from_seo_settings_table.php
database/migrations/2025_10_06_214500_add_missing_columns_to_seo_settings_central.php
database/migrations/tenant/2025_10_06_214500_add_missing_columns_to_seo_settings.php
```

### Click Tracking Sistemi Kaldırma

**Commit:** `0284d25d4` - 🗑️ REMOVE: Click tracking sistemi tamamen kaldırıldı

**Silinen:**
```
database/migrations/2025_10_18_210510_create_search_clicks_table.php (Central)
database/migrations/tenant/2025_10_18_210510_create_search_clicks_table.php (Tenant)
```

**Kaldırılan Tablo:** `search_clicks` (artık kullanılmıyor)

---

## 📦 Central DB'ye Taşınan Tablolar {#central-db-ye-taşınan-tablolar}

### 1. **ai_tenant_directives** ⭐ YENİ

**Önceki Durum:**
- Her tenant database'inde ayrı tablo: `ai_tenant_directives`
- Tenant-specific veriler

**Yeni Durum:**
- **Central DB**'de tek tablo: `ai_tenant_directives`
- Tüm tenant'lar için tek tablo
- `tenant_id` kolonu ile filtreleme

**Sebep:**
- Tüm tenant'lar için ortak AI directives
- Merkezi yönetim kolaylığı
- Veri tutarlılığı

**Migration:**
- Tenant migration'ı silindi
- Central migration mevcut

### 2. **ai_conversations** (Değişiklik yok)

**Durum:** Zaten Central DB'deydi
**Tenant Filter:** `tenant_id` kolonu

### 3. **ai_messages** (Değişiklik yok)

**Durum:** Zaten Central DB'deydi
**İlişki:** `conversation_id` üzerinden

---

## 💾 Export Edilen Veriler {#export-edilen-veriler}

### Dosyalar:

#### 1. `central_ai_tables_structure.sql` (15 KB)
**İçerik:** CREATE TABLE statements

**Tablolar:**
- `ai_tenant_directives`
- `ai_conversations`
- `ai_messages`
- `ai_providers`
- `ai_features`

**Kullanım:**
```sql
-- Sunucuda tablolar yoksa oluştur
mysql -u root tuufi_com < central_ai_tables_structure.sql
```

#### 2. `central_ai_tables_data.sql` (12 MB)
**İçerik:** INSERT statements (complete insert format)

**Format Özellikleri:**
- `--complete-insert`: Kolon adları dahil
- `--skip-extended-insert`: Her satır ayrı INSERT
- Güvenli import için optimize edilmiş

**Kullanım:**
```sql
-- Sunucuda verileri import et
mysql -u root tuufi_com < central_ai_tables_data.sql
```

#### 3. `deleted_migrations.txt` (5.2 KB)
**İçerik:** Git history'den silinen tüm migration dosyalarının listesi

**Kullanım:** Referans için, hangi migration'ların kaldırıldığını gösterir

#### 4. `migration-changes-report.md` (2 KB)
**İçerik:** Özet rapor

---

## 🚀 Sunucuda Yapılması Gerekenler {#sunucuda-yapılması-gerekenler}

### Adım 1: Git Pull
```bash
cd /var/www/vhosts/tuufi.com/httpdocs
git pull origin main
```

### Adım 2: Migration Dosyalarını Kontrol Et
```bash
# Silinmiş migration'lar artık yok mu kontrol et
ls database/migrations/tenant/2024_11_04_120001_create_ai_tenant_directives_table.php
# Hata vermeli: No such file or directory
```

### Adım 3: Database Backup (ÖNEMLİ!)
```bash
# Önce backup al!
mysqldump -u root tuufi_com ai_tenant_directives ai_conversations ai_messages > /tmp/backup_before_import_$(date +%Y%m%d_%H%M%S).sql
```

### Adım 4: Central DB Structure Kontrol
```bash
# ai_tenant_directives tablosu var mı kontrol et
mysql -u root tuufi_com -e "SHOW TABLES LIKE 'ai_tenant_directives';"
```

**Eğer tablo yoksa:**
```bash
mysql -u root tuufi_com < readme/database-exports/central_ai_tables_structure.sql
```

### Adım 5: Data Import (Dikkatli!)

**⚠️ UYARI:** Mevcut veriyi silmek istemiyorsan önce kontrol et!

```bash
# Mevcut kayıt sayısını kontrol et
mysql -u root tuufi_com -e "SELECT COUNT(*) FROM ai_tenant_directives;"
```

**Eğer veri yoksa veya override etmek istiyorsan:**
```bash
mysql -u root tuufi_com < readme/database-exports/central_ai_tables_data.sql
```

**Eğer veri varsa ve birleştirmek istiyorsan:**
```bash
# Duplicate kontrolü yap, sonra import et
# Duplicate hatası alırsan ID conflict var demektir
```

### Adım 6: Tenant Database'leri Temizle (Opsiyonel)

**Eğer tenant database'lerinde hala `ai_tenant_directives` tablosu varsa:**

```bash
# Her tenant için
mysql -u root tenant_ixtif -e "DROP TABLE IF EXISTS ai_tenant_directives;"
mysql -u root tenant_ixtif -e "DROP TABLE IF EXISTS ai_conversations;"
```

**⚠️ DİKKAT:** Bunu yapmadan önce:
1. Tenant'taki verilerin central'a taşındığından emin ol
2. Backup al
3. Test tenant'ta dene

### Adım 7: Composer & Cache
```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
php artisan view:clear
curl -s -k https://tuufi.com/opcache-reset.php
```

### Adım 8: Test

**Central DB'de AI Directives Kontrol:**
```bash
mysql -u root tuufi_com -e "SELECT id, tenant_id, directive, feature_slug FROM ai_tenant_directives LIMIT 5;"
```

**Conversation Test:**
```bash
mysql -u root tuufi_com -e "SELECT id, tenant_id, feature_slug, is_active FROM ai_conversations WHERE tenant_id = 2 LIMIT 5;"
```

---

## 📊 Veri İstatistikleri

**Local Database (Development):**
- Export Tarihi: 2025-11-08
- Toplam Satır: 358 satır SQL
- Dosya Boyutu: 12 MB
- Tablolar: 5 adet

**Export Format:**
- Complete Insert: ✅
- Skip Extended Insert: ✅ (Her satır ayrı INSERT)
- No Create Info: ✅ (Sadece data dosyasında)

---

## ⚠️ Önemli Notlar

1. **Backup Almayı Unutma!** Sunucuda herhangi bir import işlemi öncesi mutlaka backup al.

2. **Tenant ID Kontrolü:** Import edilen verilerde `tenant_id` kolonları doğru mu kontrol et.

3. **Migration Sırası:** Sunucuda migration çalıştırırken sıralama önemli. Önce structure, sonra data.

4. **Rollback Planı:** Hata durumunda geri dönüş için backup dosyalarını sakla.

5. **Testing:** Önce test tenant'ta dene, sonra production'a geç.

---

## 🔗 İlgili Commit'ler

- `f40cfdc5f` - 🎯 FIX: AI Mimari Düzeltme - Directives Central DB'ye Taşındı
- `bf0c8f87d` - 🧹 CLEANUP: Migration temizliği, storage dosyaları ve sistem güncellemeleri
- `0284d25d4` - 🗑️ REMOVE: Click tracking sistemi tamamen kaldırıldı

---

## 📞 Sorun mu var?

**Hata alırsan kontrol et:**
1. Database bağlantısı çalışıyor mu?
2. MySQL kullanıcısının yetkileri yeterli mi?
3. Tablo yapısı (structure) mevcut mu?
4. Duplicate key hatası alıyorsan ID conflict var

**Claude'a şunu söyle:**
"Sunucuda database import ederken [hata mesajı] aldım, yardım eder misin?"

---

📝 **Not:** Bu dosyalar local development database'inden alındı. Production database'inde farklı veriler olabilir. Import öncesi mutlaka karşılaştırma yap!
