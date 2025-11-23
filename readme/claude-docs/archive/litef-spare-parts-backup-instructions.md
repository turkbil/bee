# 🛡️ LİTEF YEDEK PARÇA AKTARIMI - YEDEKLEME VE KORUMA TALİMATLARI

**Tarih**: 2025-10-13
**ID**: litef-spare-parts-backup

---

## 📦 OLUŞTURULAN DOSYALAR

### Seeder Dosyaları
**Konum**: `/Users/nurullah/Desktop/cms/laravel/Modules/Shop/database/seeders/LitefSpareParts/`

- ✅ **1 Kategori Seeder**: `LitefSparePartsCategoriesSeeder.php`
- ✅ **85 Ürün Seeder**: `LitefSpareParts_*_Seeder.php`
- ✅ **1 Master Seeder**: `LitefSparePartsMasterSeeder.php`

### Fotoğraf Dosyaları
**Konum**: `/Users/nurullah/Desktop/cms/laravel/storage/app/public/litef-spare-parts/`

- ✅ **699 Fotoğraf Kopyalandı**
- 📁 Toplam boyut: ~500 MB (tahmini)

---

## 🚨 ÖNEMLİ UYARILAR

### ⚠️ SEEDER ÇALIŞTIRMADAN ÖNCE MUTLAKA:

1. **Veritabanı Yedekleme**
2. **Fotoğraf Klasörünü Git'ten Hariç Tutma**
3. **Test Ortamında Deneme**
4. **Rollback Planı Hazırlama**

---

## 1️⃣ VERİTABANI YEDEKLEME

### Manuel Yedekleme (Önerilen)

```bash
# Tarihli yedek oluştur
php artisan backup:run --only-db

# VEYA manuel mysqldump
mysqldump -u your_username -p your_database > backup_before_litef_import_$(date +%Y%m%d_%H%M%S).sql
```

### Laravel Backup Package Kullanıyorsanız

```bash
# Tam yedek (DB + Files)
php artisan backup:run

# Sadece veritabanı
php artisan backup:run --only-db
```

### Yedek Kontrolü

```bash
# Yedek dosyasını listele
ls -lh storage/app/backups/

# Yedek boyutunu kontrol et
du -sh storage/app/backups/
```

---

## 2️⃣ .GITIGNORE AYARLARI

### Fotoğraf Klasörünü Git'ten Hariç Tut

**Konum**: `/Users/nurullah/Desktop/cms/laravel/.gitignore`

Aşağıdaki satırı `.gitignore` dosyasına ekleyin:

```gitignore
# Litef Spare Parts Photos (DO NOT COMMIT)
/storage/app/public/litef-spare-parts/
```

### .gitignore Kontrolü

```bash
# .gitignore'u düzenle
nano .gitignore

# VEYA
code .gitignore

# Kontrol et
cat .gitignore | grep "litef-spare-parts"
```

### Git Status Kontrolü

```bash
# Untracked files'ı kontrol et
git status

# Eğer litef-spare-parts klasörü gözüküyorsa:
git rm -r --cached storage/app/public/litef-spare-parts/
git commit -m "chore: Exclude litef-spare-parts from git tracking"
```

---

## 3️⃣ FOTOĞRAF KLASÖRÜ YEDEKLEME

### Lokal Yedek Oluşturma

```bash
# Sıkıştırılmış arşiv oluştur
cd /Users/nurullah/Desktop/cms/laravel/storage/app/public
tar -czf litef-spare-parts-backup-$(date +%Y%m%d_%H%M%S).tar.gz litef-spare-parts/

# Yedek boyutunu kontrol et
ls -lh litef-spare-parts-backup-*.tar.gz

# Güvenli konuma taşı
mv litef-spare-parts-backup-*.tar.gz ~/Desktop/backups/
```

### Alternatif: rsync ile Yedekleme

```bash
# Başka bir konuma kopyala
rsync -av --progress storage/app/public/litef-spare-parts/ ~/Desktop/backups/litef-spare-parts/

# VEYA harici diske
rsync -av --progress storage/app/public/litef-spare-parts/ /Volumes/ExternalDrive/backups/litef-spare-parts/
```

---

## 4️⃣ SEEDER ÇALIŞTIRMA

### Adım 1: Symlink Kontrolü

```bash
# storage:link çalışıyor mu?
php artisan storage:link

# Symlink kontrolü
ls -la public/storage
```

### Adım 2: Test Ortamında Deneme (Önerilen)

```bash
# Test veritabanına bağlan (config/database.php düzenle)
# SONRA:
php artisan db:seed --class="Modules\Shop\Database\Seeders\LitefSpareParts\LitefSparePartsMasterSeeder"
```

### Adım 3: Canlı Ortamda Çalıştırma

```bash
# ÖNCE YEDEKLEMELERİ YAPTIĞINIZDAN EMİN OLUN!

php artisan db:seed --class="Modules\Shop\Database\Seeders\LitefSpareParts\LitefSparePartsMasterSeeder"
```

### Adım 4: Seeder Çıktısını Kaydet

```bash
# Log dosyasına kaydet
php artisan db:seed --class="Modules\Shop\Database\Seeders\LitefSpareParts\LitefSparePartsMasterSeeder" > litef_seeder_output.log 2>&1

# Log'u incele
cat litef_seeder_output.log
```

---

## 5️⃣ DOĞRULAMA ADIMLARI

### Veritabanı Kontrolü

```bash
# MySQL'e bağlan
mysql -u your_username -p your_database

# Eklenen kategorileri kontrol et
SELECT COUNT(*) as total FROM shop_categories WHERE title->>'$.tr' LIKE '%YEDEK PARÇA%';

# Eklenen ürünleri kontrol et
SELECT COUNT(*) as total FROM shop_products WHERE sku LIKE 'LITEF-SP-%';

# Fotoğraf ilişkilerini kontrol et
SELECT COUNT(*) as total FROM media WHERE collection_name IN ('featured_image', 'gallery');
```

### Fotoğraf Kontrolü

```bash
# Fotoğraf sayısı kontrolü
find storage/app/public/litef-spare-parts -type f | wc -l

# Boyut kontrolü
du -sh storage/app/public/litef-spare-parts/
```

### Frontend Kontrolü

1. **Admin Panel**: `http://laravel.test/admin/shop/products`
2. **Filtre**: "YEDEK PARÇA" kategorisi
3. **Kontrol**: Fotoğraflar görünüyor mu?

---

## 6️⃣ ROLLBACK PLANI

### Hata Durumunda Geri Alma

#### A. Veritabanı Geri Yükleme

```bash
# Yedekten geri yükle
mysql -u your_username -p your_database < backup_before_litef_import_YYYYMMDD_HHMMSS.sql

# VEYA Laravel backup package
php artisan backup:restore --backup-name=BACKUP_NAME
```

#### B. Eklenen Kayıtları Manuel Silme

```sql
-- Eklenen ürünleri sil
DELETE FROM shop_products WHERE sku LIKE 'LITEF-SP-%';

-- Eklenen kategorileri sil (dikkatli olun!)
DELETE FROM shop_categories WHERE title->>'$.tr' = 'YEDEK PARÇA';

-- Eklenen medya kayıtlarını sil
DELETE FROM media WHERE collection_name IN ('featured_image', 'gallery')
  AND model_type = 'Modules\\Shop\\app\\Models\\ShopProduct'
  AND model_id IN (SELECT id FROM shop_products WHERE sku LIKE 'LITEF-SP-%');
```

#### C. Fotoğraf Klasörünü Temizleme

```bash
# Sadece litef-spare-parts klasörünü sil
rm -rf storage/app/public/litef-spare-parts/

# VEYA yedekten geri yükle
tar -xzf litef-spare-parts-backup-YYYYMMDD_HHMMSS.tar.gz -C storage/app/public/
```

---

## 7️⃣ SUNUCU YEDEKLEMELERİ

### Crontab Yedekleme (Önerilen)

**Dosya**: `/etc/cron.d/laravel-backup`

```bash
# Her gece saat 02:00'da yedekle
0 2 * * * cd /path/to/laravel && php artisan backup:run --only-db >> /var/log/laravel-backup.log 2>&1

# Her hafta sonu fotoğrafları yedekle
0 3 * * 0 tar -czf /backups/litef-photos-$(date +\%Y\%m\%d).tar.gz /path/to/laravel/storage/app/public/litef-spare-parts/
```

### S3 Yedekleme (Cloud)

```bash
# AWS CLI ile S3'e yükle
aws s3 sync storage/app/public/litef-spare-parts/ s3://your-bucket/litef-spare-parts/ --storage-class STANDARD_IA

# VEYA Laravel Backup'ın S3 entegrasyonu
# config/backup.php'de S3 disk'i aktif edin
```

---

## 8️⃣ DEPLOYMENT CHECKLİST

### Production'a Geçmeden Önce:

- [ ] Veritabanı yedeklendi mi?
- [ ] Fotoğraf klasörü yedeklendi mi?
- [ ] .gitignore'a `litef-spare-parts` eklendi mi?
- [ ] Test ortamında seeder başarıyla çalıştı mı?
- [ ] Fotoğraflar frontend'de görünüyor mu?
- [ ] Kategori hiyerarşisi doğru mu?
- [ ] SKU'lar unique mi? (çakışma var mı?)
- [ ] Rollback planı hazır mı?

### Production Deployment:

```bash
# 1. Bakım modunu aç
php artisan down --message="Yedek parça aktarımı yapılıyor..." --retry=60

# 2. Yedekleri al
php artisan backup:run

# 3. Seeder'ı çalıştır
php artisan db:seed --class="Modules\Shop\Database\Seeders\LitefSpareParts\LitefSparePartsMasterSeeder"

# 4. Cache temizle
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# 5. Doğrulama yap
# ... kontroller ...

# 6. Bakım modunu kapat
php artisan up
```

---

## 9️⃣ OLASI SORUNLAR VE ÇÖZÜMLER

### Sorun 1: Fotoğraflar Görünmüyor

**Çözüm**:
```bash
# Symlink kontrolü
php artisan storage:link

# İzinleri düzelt
chmod -R 755 storage/app/public/litef-spare-parts/
chown -R www-data:www-data storage/app/public/litef-spare-parts/
```

### Sorun 2: SKU Çakışması

**Hata**: `SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry`

**Çözüm**:
```bash
# Mevcut SKU'ları kontrol et
php artisan tinker
>>> ShopProduct::where('sku', 'LIKE', 'LITEF-SP-%')->pluck('sku')->toArray();

# Seeder'da updateOrInsert kullanıldığı için otomatik çözülür
# Ama kontrol etmek isterseniz:
>>> ShopProduct::where('sku', 'LITEF-SP-XXX')->first();
```

### Sorun 3: Seeder Timeout

**Hata**: `Maximum execution time exceeded`

**Çözüm**:
```bash
# php.ini'de max_execution_time'ı artır
# VEYA CLI'da:
php -d max_execution_time=600 artisan db:seed --class="..."

# VEYA seeder'ları parça parça çalıştır:
php artisan db:seed --class="Modules\Shop\Database\Seeders\LitefSpareParts\LitefSparePartsCategoriesSeeder"
php artisan db:seed --class="Modules\Shop\Database\Seeders\LitefSpareParts\LitefSpareParts_SiyahDolguLastik_Seeder"
# ... diğerleri ...
```

### Sorun 4: Memory Limit

**Hata**: `Allowed memory size of ... bytes exhausted`

**Çözüm**:
```bash
# Memory limit'i artır
php -d memory_limit=512M artisan db:seed --class="..."
```

---

## 🔟 GÜVENLİK ÖNERİLERİ

### 1. Veritabanı Yedeklerini Şifrele

```bash
# GPG ile şifrele
gpg --symmetric --cipher-algo AES256 backup_before_litef_import.sql

# Şifreli yedeği sakla, orijinali sil
rm backup_before_litef_import.sql
```

### 2. Fotoğraf Klasörü İzinleri

```bash
# Web server sadece okuyabilsin
chmod -R 644 storage/app/public/litef-spare-parts/*
chmod 755 storage/app/public/litef-spare-parts/
```

### 3. .env Güvenliği

```bash
# .env'de hassas bilgiler var mı kontrol et
cat .env | grep -i "litef"

# Varsa .env.example'a eklemeden .env'de tutun
```

---

## ✅ SON KONTROL LİSTESİ

### Yedekleme Sonrası:

- [ ] Yedek dosyası var mı? (`ls -lh storage/app/backups/`)
- [ ] Yedek çalışıyor mu? (test restore)
- [ ] .gitignore güncellendi mi?
- [ ] Fotoğraflar güvenli konumda mı?

### Seeder Sonrası:

- [ ] Toplam 690 ürün eklendi mi?
- [ ] 699 fotoğraf eksiksiz mi?
- [ ] Kategori hiyerarşisi doğru mu?
- [ ] Frontend'de ürünler görünüyor mu?
- [ ] Performans sorunu var mı?

---

## 📞 DESTEK

### Hata Durumunda:

1. **Log Dosyalarını Kontrol Et**:
   - `storage/logs/laravel.log`
   - `litef_seeder_output.log`

2. **Veritabanı Logları**:
   ```sql
   SHOW ENGINE INNODB STATUS;
   ```

3. **Claude'a Danış**:
   - Log dosyasını paylaş
   - Hata mesajını kopyala
   - Hangi adımda takıldığını belirt

---

## 📚 İLGİLİ DÖKÜMANLAR

- `claudeguncel-2025-10-13-22-30-litef-import.md` - Analiz ve plan
- `app/Console/Commands/GenerateLitefSeedersCommand.php` - Seeder generator command
- `Modules/Shop/database/seeders/LitefSpareParts/` - Oluşturulan seeder dosyaları

---

**Hazırlayan**: Claude
**Tarih**: 2025-10-13
**Durum**: ✅ Seeder'lar oluşturuldu - Yedekleme yapılmayı bekliyor

---

## 🎯 SONRAKI ADIMLAR

1. ✅ Seeder'lar oluşturuldu
2. ✅ Fotoğraflar kopyalandı
3. ⏳ **ŞİMDİ**: Bu dökümanı oku ve yedekleme yap
4. ⏳ Seeder'ları test ortamında dene
5. ⏳ Production'a deploy et

**Kolay gelsin! 🚀**
