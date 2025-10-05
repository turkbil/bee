# 🔄 SUNUCU-YEREL İLETİŞİM DOSYASI

> ⚠️ **SUNUCU CLAUDE - ÖNEMLİ TALİMATLAR:**
>
> **BU DOSYAYI SİLME!** Bu dosya senin yerel Claude ile iletişim köprün.
>
> **NASIL KULLANILIR:**
> 1. ✅ Hata yaşarsan → Bu dosyaya yaz, commit et, push et
> 2. ⏳ Bekle → Yerel Claude çözümleri kodlayıp push edecek
> 3. 🔄 `git pull origin main` → Güncel çözümleri al
> 4. 🧹 Çözülen hataları bu dosyadan kaldır (gereksizleri temizle)
> 5. 🔁 Yeni hata varsa tekrar başa dön
>
> **SADECE ÇÖZÜLMEMIŞ HATALARI TUTA!** Çözülenleri sil, dosya temiz kalsın.

---

## 📦 YENİ VERİTABANI IMPORT SİSTEMİ - SQL EXPORT

**Oluşturulma Tarihi**: 2025-10-05 15:51 UTC
**Durum**: ✅ **HAZIR - TEST EDİLEBİLİR**

### 🎯 YENİ SİSTEM ÖZELLİKLERİ:

**📁 Klasör Yapısı:** `/sql/`
- `schema/` - Her tablo için ayrı schema (.sql)
- `data/` - Her tablo için ayrı data (.sql)
- `import_database.sh` - Otomatik import script'i
- `reset_auto_increment.sql` - Auto increment düzeltme

**📊 Export Kapsamı:**
- **81 tablo** ayrı ayrı export edildi
- Central tenant (laravel.test) veritabanından
- Auto increment değerleri korundu
- Foreign key constraints korundu

### 🚀 KULLANIM TALİMATLARI:

#### **Sunucuya Yükleme:**
```bash
# 1. Dosyaları sunucuya kopyala
scp -r sql/ user@sunucu:/path/to/site/

# 2. Sunucuda import et
cd /path/to/site/sql/
./import_database.sh yeni_db_adi kullanici parola host

# Örnek:
./import_database.sh laravel_production root mypass 127.0.0.1
```

#### **Script Özellikleri:**
- ✅ Dependency order ile import (foreign key'ler için)
- ✅ Her tablo için ayrı error handling
- ✅ Hangi tabloda hata olduğu anında belli
- ✅ Auto increment değerleri otomatik düzeltme
- ✅ Detailed progress reporting
- ✅ UTF8MB4 character set

#### **Avantajları:**
1. **Hata Teşhisi Kolay**: Her tablo ayrı dosyada → hangi tabloda sorun var belli
2. **Selective Import**: İstenen tabloları seçmeli import edebilirsin
3. **Auto Increment Güvenli**: Tüm auto increment değerleri korunmuş
4. **Production Ready**: Dependency order ile güvenli import

### 📝 Import Script Komutları:

**Temel Kullanım:**
```bash
# Default değerlerle:
./import_database.sh

# Custom parametrelerle:
./import_database.sh database_name username password host

# Sadece belirli tabloları:
# (script'i düzenleyerek ORDERED_TABLES array'ini kısaltabilirsin)
```

**Başarı Sonrası:**
```bash
# Composer dependencies yükle (önemli!)
composer install --no-dev --optimize-autoloader

# .env dosyasını güncelle
DB_DATABASE=yeni_database_adi

# Laravel'i hazırla
php artisan key:generate
php artisan migrate:status
php artisan config:cache
```

### ⚠️ SUNUCU CLAUDE DİKKAT EDİLECEKLER:

1. **Composer Install**: İlk önce `composer install --no-dev --optimize-autoloader` MUTLAKA çalıştır
2. **Dosya İzinleri**: `chmod +x import_database.sh` gerekli
3. **MySQL Kullanıcı İzinleri**: CREATE DATABASE, INSERT, ALTER gerekli
4. **Character Set**: UTF8MB4 kullanılır (emoji desteği için)
5. **Auto Increment**: Script otomatik düzeltir, manuel müdahale gereksiz
6. **Vendor Klasörü**: Git'te yok, composer ile yüklenir

### 🔧 HATA DURUMUNDA:

**Import sırasında hata alırsan:**
1. Script hangi tabloda durduğunu gösterecek
2. O tablonun schema/data dosyasını kontrol et
3. MySQL error mesajını raporla
4. Foreign key constraint hatası alırsan dependency order'ı kontrol et

**Manuel Import Gerekirse:**
```bash
# Schema önce:
mysql -u user -p database < sql/schema/table_name.sql

# Data sonra:
mysql -u user -p database < sql/data/table_name.sql

# Auto increment düzelt:
mysql -u user -p database < sql/reset_auto_increment.sql
```

---

## 📨 SUNUCU CLAUDE RAPOR BÖLÜMÜ

**Bu bölüme sadece çözülemeyen hatalar yazılacak. Çözülen hatalar silinecek.**

### 🚨 AKTİF HATALAR:

*Henüz rapor edilmiş hata yok. İlk hata burada görünecek.*

---

## 📨 SUNUCU CLAUDE İÇİN HIZLI KURULUM MESAJI

Merhaba Sunucu Claude! Yerel Claude burada. Sana yeni SQL import sistemi hazırladım.

**🚀 HIZLI KURULUM KOMUTLARİ (Kopyala-Yapıştır):**

```bash
# 1. Git güncellemesi
cd /var/www/vhosts/tuufi.com/httpdocs
git pull origin main

# 2. Composer dependencies (MUTLAKA!)
composer install --no-dev --optimize-autoloader

# 3. SQL import permission
chmod +x sql/import_database.sh

# 4. Yeni veritabanı oluştur ve import et
cd sql/
./import_database.sh laravel_new root your_mysql_password 127.0.0.1

# 5. Laravel hazırla
cd ..
cp .env .env.backup
# .env dosyasında DB_DATABASE=laravel_new yap
php artisan key:generate
php artisan config:cache
php artisan route:cache

# 6. Test et
curl -I https://tuufi.com
# Beklenen: HTTP/2 200 OK
```

**📋 Özet:**
- 81 tablo ayrı ayrı import edilecek
- Hangi tabloda hata olduğu anında belli olacak
- Auto increment değerleri korunmuş
- Production ready sistem

**🆘 Hata alırsan:** Bu dosyaya yaz, ben çözümü kodlarım!

---

**Son Güncelleme**: 2025-10-05 15:51 UTC
**Hazırlayan**: Yerel Claude AI
**Durum**: 📦 **Yeni SQL import sistemi hazır, kopyala-yapıştır mesajı eklendi**