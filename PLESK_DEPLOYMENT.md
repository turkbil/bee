# 🚀 PLESK SUNUCU DEPLOYMENT - CLAUDE TODO LİSTESİ

**Proje:** Laravel 12 Multi-Tenant CMS
**GitHub:** https://github.com/turkbil/bee.git
**Framework:** Laravel 12, Stancl/Tenancy, Modular (nwidart)

---

## 📋 SEN (CLAUDE) BU DOSYAYI OKUYORSUN!

Bu dosyayı Nurullah sana yapıştıracak. Sen bu TODO listesindeki **TÜM ADIMLARI** sırayla yapacaksın.

**GÖREVIN:**
1. Her adımı TodoWrite ile işaretle
2. Komutları çalıştır
3. Hataları düzelt
4. Sonucu raporla

**ÖNEMLİ:**
- Sudo şifresi: `123123`
- Domain: `[Nurullah söyleyecek]`
- Tüm komutları sen çalıştıracaksın
- Onay sorma, direkt yap!

---

## ✅ TODO LİSTESİ

### ☐ 1. Sistem Bilgilerini Al ve Kontrol Et

**1.1 Bulunduğun Yeri Öğren:**
```bash
pwd
hostname
whoami
```

**1.2 PHP Versiyonu:**
```bash
php -v
```
Beklenen: PHP 8.2+

**1.3 PHP Extensions:**
```bash
php -m | grep -E "bcmath|mbstring|pdo_mysql|redis|gd|zip|curl|intl"
```

**1.4 Composer:**
```bash
composer --version
```

**1.5 Node.js:**
```bash
node --version
npm --version
```

**1.6 Redis:**
```bash
redis-cli ping
```
Beklenen: PONG

**1.7 MySQL:**
```bash
mysql --version
```

> **RAPOR ET:** Tüm versiyonları ve eksik olanları Nurullah'a söyle!

---

### ☐ 2. Domain ve Klasör Bilgisini Al

**Nurullah'a SOR:**
> "Hangi domain'e kuruyorum? (örn: example.com)"

**Klasör Yolu Belirle:**
```
/var/www/vhosts/[DOMAIN]/httpdocs/
```

**Klasöre Git:**
```bash
cd /var/www/vhosts/[DOMAIN]/httpdocs/
pwd  # Doğru yerde misin kontrol et
```

---

### ☐ 3. Database Bilgilerini Al

**Nurullah'a SOR:**
> "Database bilgilerini ver:
> - Database name: ?
> - Username: ?
> - Password: ?
>
> Yoksa ben Plesk'ten oluşturayım mı?"

**EĞER SEN OLUŞTURACAKSAN:**

Nurullah'dan Plesk login bilgilerini al, Plesk UI'den database oluştur:
```
Plesk → Databases → Add Database
Name: laravel_prod
User: laravel_user
Password: [güçlü şifre oluştur, KAYDET!]
Charset: utf8mb4
Collation: utf8mb4_unicode_ci
```

---

### ☐ 4. GitHub Token Al

**Nurullah'a SOR:**
> "GitHub Personal Access Token'ı ver"

**VEYA:**

Eğer Nurullah vermezse, şu token'ı kullan (temporary):
```
ghp_[Nurullah verecek]
```

---

### ☐ 5. Mevcut Dosyaları Yedekle

```bash
# Klasörde dosya var mı?
ls -la

# Varsa yedekle
mkdir -p ../backup-$(date +%Y%m%d-%H%M%S)
mv * ../backup-$(date +%Y%m%d-%H%M%S)/ 2>/dev/null || true
mv .* ../backup-$(date +%Y%m%d-%H%M%S)/ 2>/dev/null || true

# Temiz mi kontrol et
ls -la
```

---

### ☐ 6. Git Clone

```bash
# Token ile clone
git clone https://turkbil:[GITHUB_TOKEN]@github.com/turkbil/bee.git .

# Başarılı mı kontrol
ls -la
git log -1 --oneline
```

---

### ☐ 7. Composer Install

```bash
# Production mode
composer install --optimize-autoloader --no-dev --no-interaction

# Memory hatası alırsan:
# php -d memory_limit=512M /usr/bin/composer install --optimize-autoloader --no-dev --no-interaction

# Başarılı mı?
ls -la vendor/
```

---

### ☐ 8. NPM Install & Build

```bash
# Node modules
npm install

# Production build
npm run production
# VEYA
# npm run build

# Başarılı mı?
ls -la public/build/
```

---

### ☐ 9. .env Dosyası Oluştur

```bash
# Kopyala
cp .env.example .env

# Düzenle
nano .env
```

**ŞU DEĞERLERİ AYARLA:**
```ini
APP_NAME="Laravel CMS"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://[DOMAIN]

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=[DATABASE_NAME]
DB_USERNAME=[DATABASE_USER]
DB_PASSWORD=[DATABASE_PASSWORD]

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Nurullah'dan API key'leri al ve yapıştır:
OPENAI_API_KEY=[Nurullah verecek]
ANTHROPIC_API_KEY=[Nurullah verecek]
DEEPSEEK_API_KEY=[Nurullah verecek]

SYSTEM_LANGUAGES=tr,en
DEFAULT_LANGUAGE=tr
ADMIN_PER_PAGE=10
MODULE_CACHE_ENABLED=true
CACHE_TTL_LIST=3600
```

**KAYDET:** `Ctrl+X`, `Y`, `Enter`

**Kontrol:**
```bash
cat .env | grep -E "DB_DATABASE|APP_URL|APP_DEBUG"
```

---

### ☐ 10. APP_KEY Generate

```bash
php artisan key:generate

# Oluştu mu?
cat .env | grep APP_KEY
```

---

### ☐ 11. Storage Link & Permissions

```bash
# Storage link
php artisan storage:link

# Permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Ownership (Plesk kullanıcısı ile)
USER=$(whoami)
chown -R $USER:psacln storage bootstrap/cache 2>/dev/null || chown -R $USER:$USER storage bootstrap/cache

# Kontrol
ls -la storage/
ls -la public/storage
```

---

### ☐ 12. Database Bağlantısı Test

```bash
php artisan db:show

# Bağlantı başarılı mı kontrol et
```

---

### ☐ 13. Migration Çalıştır

```bash
php artisan migrate --force

# Başarılı mı?
php artisan migrate:status
```

---

### ☐ 14. AI Provider Seeder (ZORUNLU)

```bash
php artisan db:seed --class=\\Modules\\AI\\Database\\Seeders\\AIProviderSeeder --force

# Başarılı mı?
php artisan tinker --execute="echo json_encode(\Modules\AI\App\Models\AIProvider::select('name','is_active')->get(), JSON_PRETTY_PRINT);"
```

---

### ☐ 15. Cache Oluştur

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

composer dump-autoload --optimize --classmap-authoritative

# Kontrol
ls -la bootstrap/cache/
```

---

### ☐ 16. Plesk Document Root Kontrol

**Nurullah'a SÖYLEYİP YAPTIR:**
> "Plesk'te şunu ayarla:
>
> Domains → [DOMAIN] → Hosting Settings
> Document root: /httpdocs/public
>
> Mutlaka /public olmalı!"

---

### ☐ 17. Plesk PHP Ayarları

**Nurullah'a SÖYLEYİP YAPTIR:**
> "Plesk'te şunu ayarla:
>
> Domains → [DOMAIN] → PHP Settings
>
> memory_limit: 512M
> max_execution_time: 300
> upload_max_filesize: 64M
> post_max_size: 64M
> max_input_vars: 5000
> display_errors: Off"

---

### ☐ 18. Cron Job Kur

**Nurullah'a SÖYLEYİP YAPTIR:**
> "Plesk'te Scheduled Tasks ekle:
>
> **Laravel Scheduler:**
> Command: /opt/plesk/php/8.3/bin/php /var/www/vhosts/[DOMAIN]/httpdocs/artisan schedule:run >> /dev/null 2>&1
> Cron: * * * * *
>
> **Queue Worker (opsiyonel):**
> Command: /opt/plesk/php/8.3/bin/php /var/www/vhosts/[DOMAIN]/httpdocs/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
> Run: Reboot"

---

### ☐ 19. Wildcard Subdomain Ayarla

**Nurullah'a SÖYLEYİP YAPTIR:**
> "Plesk'te wildcard subdomain ekle:
>
> Domains → Add Subdomain
> Subdomain: *
> Parent: [DOMAIN]
> Document root: /httpdocs/public (aynı klasör)"

---

### ☐ 20. SSL Sertifikası Kur

**Nurullah'a SÖYLEYİP YAPTIR:**
> "Plesk'te Let's Encrypt kur:
>
> SSL/TLS → Let's Encrypt
>
> Domain names:
> ☑ [DOMAIN]
> ☑ www.[DOMAIN]
> ☑ *.[DOMAIN]
>
> ☑ Redirect HTTP to HTTPS"

---

### ☐ 21. İLK TEST

```bash
# Ana sayfa
curl -I https://[DOMAIN]

# Beklenen: HTTP/2 200

# Admin
curl -I https://[DOMAIN]/admin

# Login
curl -I https://[DOMAIN]/login
```

**Nurullah'a SÖYLEYİP KONTROL ETTİR:**
> "Browser'dan şunları aç:
> - https://[DOMAIN]
> - https://[DOMAIN]/admin
> - https://[DOMAIN]/login
>
> Hepsi açılıyor mu?"

---

### ☐ 22. Test Tenant Oluştur

```bash
php artisan tinker

# Tinker içinde:
$tenant = \App\Models\Tenant::create(['id' => 'test', 'name' => 'Test Tenant']);
$tenant->domains()->create(['domain' => 'test.[DOMAIN]']);
exit

# Test et
curl -I https://test.[DOMAIN]
```

**Nurullah'a SÖYLEYİP KONTROL ETTİR:**
> "Browser'dan aç: https://test.[DOMAIN]
> Açılıyor mu?"

---

### ☐ 23. Log Kontrol

```bash
# Laravel log
tail -50 storage/logs/laravel.log

# ERROR var mı?
grep ERROR storage/logs/laravel.log | tail -20
```

**HATA VARSA:** Nurullah'a raporla!

---

### ☐ 24. Final Güvenlik Kontrolleri

```bash
# .env erişimi engelli mi?
curl https://[DOMAIN]/.env
# 403 veya 404 olmalı

# APP_DEBUG kapalı mı?
cat .env | grep APP_DEBUG
# false olmalı

# Storage klasörü korunuyor mu?
curl https://[DOMAIN]/storage/logs/laravel.log
# 403 veya 404 olmalı
```

---

## ✅ TAMAMLANDI!

**BAŞARILI CHECKLIST - Nurullah'a Raporla:**

```
✅ Sistem kontrolleri tamamlandı
✅ Git clone başarılı
✅ Composer install tamamlandı
✅ NPM build tamamlandı
✅ .env dosyası oluşturuldu
✅ APP_KEY generate edildi
✅ Storage link ve permissions ayarlandı
✅ Database bağlantısı başarılı
✅ Migration çalıştırıldı
✅ AI Provider seeder çalıştırıldı
✅ Cache'ler oluşturuldu
✅ Document root ayarlandı (Plesk)
✅ PHP ayarları yapıldı (Plesk)
✅ Cron job kuruldu (Plesk)
✅ Wildcard subdomain ayarlandı (Plesk)
✅ SSL sertifikası kuruldu (Plesk)
✅ Ana site çalışıyor
✅ Tenant sistemi test edildi
✅ Log kontrolleri yapıldı
✅ Güvenlik kontrolleri tamamlandı
```

**DEPLOYMENT BAŞARILI! 🎉**

---

## 🔧 SORUN ÇÖZME REHBERİ

### 500 Error Alırsan:
```bash
tail -100 storage/logs/laravel.log
chmod -R 775 storage bootstrap/cache
php artisan config:clear
```

### Migration Hatası:
```bash
php artisan migrate:status
php artisan migrate --force
```

### Queue Çalışmıyor:
```bash
php artisan queue:work redis --once
redis-cli ping
```

### Cache Problemi:
```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
```

---

**HAZIRLAYAN:** Claude AI (Yerel - Nurullah)
**UYGULAYACAK:** Claude AI (Sunucu - Sen!)
**TARİH:** 2025-10-04
