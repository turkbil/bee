# 💾 DATABASE BACKUP SİSTEMİ

Bu klasör tüm veritabanı yedeklerini organize bir şekilde saklar.

## 📅 SON YEDEKLEME

**Tarih:** 04 Kasım 2025 - 01:19
**Durum:** ✅ Başarılı
**Yedeklenen:**
- Central DB: `tuufi_4ekim` (102MB → 25MB sıkıştırılmış)
- Tenant 2: `tenant_ixtif` (26MB → 8.4MB sıkıştırılmış)
- Tenant 3: `tenant_ixtif_tr` (202KB sıkıştırılmış)

## 🚀 HIZLI KULLANIM

```bash
# Tüm veritabanlarını yedekle
bash readme/backups/backup.sh

# Hızlı yedekleme (minimal script)
bash readme/backups/backup-fast.sh

# Manuel yedek al (tek DB)
mysqldump -u tuufi_4ekim -p'"XZ9Lhb%u8jp9#njf"' tuufi_4ekim | gzip > readme/backups/manuel-backup-$(date +%Y%m%d).sql.gz
```

## 📁 KLASÖR YAPISI

```
readme/backups/
├── backup.sh                                     # Ana backup script
├── backup-fast.sh                                # Hızlı backup script
├── backup-minimal.sh                             # Minimal backup script
├── README.md                                     # Bu döküman
├── full_backup_20251104_010726.tar.gz           # Central DB backup (25MB)
├── complete_backup_with_tenants_20251104_011915.tar.gz  # Tüm DB'ler (8.4MB)
├── 20251028-024200/                              # Eski backup klasörü
├── 20251028-024456/                              # Eski backup klasörü
└── shop_products_body_backup_20251030_005328.sql # Ürün backup'ı
```

## 🎯 KULLANIM SENARYOLARı

### 1️⃣ Manuel Backup (İstediğiniz Zaman)

```bash
bash readme/backups/backup.sh
```

**Çıktı:**
```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
  DATABASE BACKUP TOOL
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

✓ Backup klasörü oluşturuldu: readme/backups/20251028-024200

📊 Central Database Backup
Database: tuufi_4ekim
✓ Central DB yedeklendi: 45M

🏢 Tenant Databases Backup
Tenant ID: 2 → Database: ixtif
✓ Tenant 2 yedeklendi: 38M
Tenant ID: 3 → Database: ixtiftr
✓ Tenant 3 yedeklendi: 12M

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
✅ Backup Tamamlandı!

Backup Klasörü: readme/backups/20251028-024200
Toplam Boyut: 95M
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

### 2️⃣ Tek Veritabanı Yedekle

```bash
# Central DB
mysqldump -u tuufi_4ekim -p'"XZ9Lhb%u8jp9#njf"' tuufi_4ekim | gzip > readme/backups/central-$(date +%Y%m%d).sql.gz

# Tenant 2 (ixtif.com)
mysqldump -u tuufi_4ekim -p'"XZ9Lhb%u8jp9#njf"' tenant_ixtif | gzip > readme/backups/tenant2-$(date +%Y%m%d).sql.gz

# Tenant 3 (ixtif.com.tr)
mysqldump -u tuufi_4ekim -p'"XZ9Lhb%u8jp9#njf"' tenant_ixtif_tr | gzip > readme/backups/tenant3-$(date +%Y%m%d).sql.gz
```

### 3️⃣ Yedekten Geri Yükle (RESTORE)

```bash
# Tar dosyasını aç
tar -xzf readme/backups/complete_backup_with_tenants_20251104_011915.tar.gz

# Central DB restore
mysql -u tuufi_4ekim -p'"XZ9Lhb%u8jp9#njf"' tuufi_4ekim < central_db_20251104_011915.sql

# Tenant 2 restore
mysql -u tuufi_4ekim -p'"XZ9Lhb%u8jp9#njf"' tenant_ixtif < tenant_2_ixtif_20251104_011915.sql

# Tenant 3 restore
mysql -u tuufi_4ekim -p'"XZ9Lhb%u8jp9#njf"' tenant_ixtif_tr < tenant_3_ixtif_tr_20251104_011915.sql
```

### 4️⃣ Eski Yedekleri Temizle

```bash
# 30 günden eski yedekleri sil
find readme/backups/ -type d -mtime +30 -exec rm -rf {} \;

# Manuel silme
rm -rf readme/backups/20251015-*
```

## ⚙️ OTOMATİK BACKUP (CRON)

Günlük otomatik backup için crontab ekleyin:

```bash
# Crontab düzenle
crontab -e

# Her gün saat 03:00'te backup al
0 3 * * * cd /var/www/vhosts/tuufi.com/httpdocs && bash readme/backups/backup.sh >> readme/backups/cron.log 2>&1
```

## 🔐 GÜVENLİK

- ✅ `.gitignore`'da - Git'e gönderilmez
- ✅ Gzip sıkıştırmalı - Az yer kaplar
- ✅ Timestamp'li - Kolay takip
- ⚠️ Şifreler script'te hardcoded - Production'da env kullan

## 📊 BACKUP STRATEJİSİ

**Önerilen:**
- **Günlük:** Otomatik backup (cron)
- **Önemli değişiklik öncesi:** Manuel backup
- **Deploy öncesi:** Manuel backup
- **Major update öncesi:** Manuel backup

**Saklama Süresi:**
- Son 7 gün: Tüm backuplar
- Son 30 gün: Haftalık backuplar
- Daha eski: Ayda 1 backup

## 🆘 ACİL DURUM

**Hata oldu, geri dönmek istiyorsun:**

```bash
# 1. En son backup'ı bul
ls -lt readme/backups/*.tar.gz | head -1

# 2. Backup'ı aç
tar -xzf readme/backups/complete_backup_with_tenants_20251104_011915.tar.gz

# 3. Central DB'yi restore et
mysql -u tuufi_4ekim -p'"XZ9Lhb%u8jp9#njf"' tuufi_4ekim < central_db_20251104_011915.sql

# 4. Tenant DB'leri restore et
mysql -u tuufi_4ekim -p'"XZ9Lhb%u8jp9#njf"' tenant_ixtif < tenant_2_ixtif_20251104_011915.sql
mysql -u tuufi_4ekim -p'"XZ9Lhb%u8jp9#njf"' tenant_ixtif_tr < tenant_3_ixtif_tr_20251104_011915.sql

# 5. Cache temizle
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## 📝 NOTLAR

- Backup süresi: ~2-5 dakika (DB boyutuna göre)
- Disk alanı: Her backup ~50-100MB (gzip ile)
- Git'e gönderilmez (güvenlik için)
- Production'da farklı sunucuya da backup alın!

## 🔗 İLGİLİ DÖKÜMANLAR

- [MySQL Backup Best Practices](https://dev.mysql.com/doc/refman/8.0/en/backup-methods.html)
- [Laravel Database Backups](https://laravel.com/docs/10.x/database)
