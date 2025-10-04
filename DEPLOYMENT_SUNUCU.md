# 🖥️ SUNUCUDA YAPILACAKLAR (Plesk'te Diğer Claude)

**Tarih:** 2025-10-04
**Kişi:** Sunucu Claude AI
**Konum:** Plesk Production Server

---

## 📌 ÖN BİLGİ

Bu dosya **Plesk sunucuda** çalışacak Claude AI için hazırlanmıştır.

**Proje:**
- GitHub: https://github.com/turkbil/bee.git
- Framework: Laravel 12
- Multi-Tenant: Stancl/Tenancy
- Modular: nwidart/laravel-modules

**Gereksinimler:**
- PHP 8.2+ (önerilir 8.3 veya 8.4)
- MySQL 8.0+
- Redis
- Composer 2.x
- Node.js 18+
- Nginx (Plesk default)

---

## ✅ TODO LİSTESİ

---

### ☐ ADIM 1: Plesk Ortam Kontrolü

**1.1 PHP Versiyonu Kontrol:**
```bash
php -v
```
**Beklenen:** PHP 8.2.0 veya üzeri

**Eğer farklıysa:**
```bash
# Plesk'te hangi PHP versiyonları var?
ls /opt/plesk/php/

# Örnek: PHP 8.3 kullan
/opt/plesk/php/8.3/bin/php -v
```

---

**1.2 PHP Extensions Kontrol:**
```bash
php -m | grep -E "bcmath|mbstring|pdo_mysql|redis|gd|zip|curl|intl"
```

**Eksik extension varsa:**
- Plesk UI → Domains → domain.com → PHP Settings → Extensions
- Gerekli extension'ları aktif et:
  - ✅ bcmath, ctype, fileinfo, json, mbstring, openssl
  - ✅ pdo, pdo_mysql, tokenizer, xml
  - ✅ gd veya imagick
  - ✅ redis (phpredis)
  - ✅ zip, curl, intl, exif

---

**1.3 Composer Kontrol:**
```bash
composer --version
```
**Beklenen:** Composer version 2.x

**Yoksa:**
```bash
# Plesk genelde composer kurulu gelir
# Tools & Settings → Updates → Composer
```

---

**1.4 Node.js Kontrol:**
```bash
node --version
npm --version
```
**Beklenen:** Node 18+ ve NPM 9+

**Yoksa:**
```bash
# NVM ile kurulum
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
source ~/.bashrc
nvm install 18
nvm use 18
```

---

**1.5 Redis Kontrol:**
```bash
redis-cli ping
```
**Beklenen:** PONG

**Çalışmıyorsa:**
```bash
# Redis'i başlat
sudo systemctl start redis
sudo systemctl enable redis
```

---

### ☐ ADIM 2: MySQL Database Oluştur

**2.1 Plesk UI'den Database Oluştur:**

```
Plesk → Domains → domain.com → Databases → Add Database
```

**Ayarlar:**
```
Database name: laravel_prod
User: laravel_user
Password: [GÜÇLÜ ŞİFRE OLUŞTUR - NOT AL!]
Database server: localhost
```

**Karakter Seti:**
```
Character set: utf8mb4
Collation: utf8mb4_unicode_ci
```

> **ÖNEMLİ:** Database şifresini bir yere kaydet! `.env` dosyasında kullanacaksın.

---

**2.2 Database Oluştuğunu Doğrula:**
```bash
mysql -u laravel_user -p
# Şifreyi gir

SHOW DATABASES;
# laravel_prod görünmeli

exit
```

---

### ☐ ADIM 3: Wildcard Subdomain Ayarla

**3.1 Plesk UI'den Subdomain Ekle:**

```
Plesk → Domains → Add Subdomain
```

**Ayarlar:**
```
Subdomain name: *
Parent domain: domain.com
Document root: /httpdocs/public
```

> **NOT:** Document root MUTLAKA `/public` klasörü olmalı!

---

**3.2 DNS Ayarları (Domain sağlayıcıda):**

```
DNS Type: A Record
Host: *
Value: [Sunucu IP adresi]
TTL: 3600
```

```
DNS Type: A Record
Host: @
Value: [Sunucu IP adresi]
TTL: 3600
```

---

### ☐ ADIM 4: Proje Klasörünü Hazırla

**4.1 Klasöre Git:**
```bash
cd /var/www/vhosts/domain.com/httpdocs/
```

---

**4.2 Mevcut Dosyaları Yedekle (varsa):**
```bash
# Kontrol et
ls -la

# Boş değilse yedekle
mkdir -p ../backup-$(date +%Y%m%d)
mv * ../backup-$(date +%Y%m%d)/ 2>/dev/null
```

---

**4.3 .htaccess Oluştur (gerekirse):**
```bash
# Eğer silinmişse tekrar oluştur
echo "Options -MultiViews -Indexes" > .htaccess
```

---

### ☐ ADIM 5: Git Clone

**5.1 Git Clone Yap:**
```bash
# DEPLOYMENT_YEREL.md'den token'ı kopyala
git clone https://turkbil:[GITHUB_TOKEN]@github.com/turkbil/bee.git .
```

> **NOT:** [GITHUB_TOKEN] yerine gerçek GitHub Personal Access Token'ı yaz

> **DİKKAT:** Komutun sonunda `.` var (mevcut klasöre clone yapar)

---

**5.2 Dosyaları Kontrol Et:**
```bash
ls -la

# Beklenen:
# - app/
# - bootstrap/
# - config/
# - database/
# - Modules/
# - public/
# - artisan
# - composer.json
# - package.json
# - .env.example
```

---

**5.3 Git Branch Kontrol:**
```bash
git branch
# * main olmalı

git log -1 --oneline
# Son commit'i göster
```

---

### ☐ ADIM 6: Composer Install

**6.1 Production Mode Install:**
```bash
composer install --optimize-autoloader --no-dev --no-interaction
```

> **NOT:**
> - `--no-dev`: Development paketlerini yüklemez
> - `--optimize-autoloader`: Autoload'ı optimize eder
> - `--no-interaction`: Onay sorularını atlar

---

**6.2 Memory Hatası Alırsan:**
```bash
php -d memory_limit=512M /usr/bin/composer install --optimize-autoloader --no-dev --no-interaction
```

---

**6.3 Composer Başarılı mı Kontrol:**
```bash
ls -la vendor/

# vendor klasörü oluşmuş olmalı
# autoload.php dosyası olmalı
```

---

### ☐ ADIM 7: NPM Install & Build

**7.1 Node Modules Yükle:**
```bash
npm install
```

---

**7.2 Production Build:**
```bash
npm run production

# VEYA vite kullanıyorsa:
npm run build
```

---

**7.3 Build Başarılı mı Kontrol:**
```bash
ls -la public/build/

# manifest.json olmalı
# CSS ve JS dosyaları olmalı
```

---

### ☐ ADIM 8: .env Dosyası Oluştur

**8.1 .env.example'dan Kopyala:**
```bash
cp .env.example .env
```

---

**8.2 .env Dosyasını Düzenle:**
```bash
nano .env
# veya
vim .env
```

**Yapıştır (YEREL_DEPLOYMENT.md'den kopyala):**
```ini
APP_NAME="Laravel CMS"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_TIMEZONE=Europe/Istanbul
APP_URL=https://domain.com  # GERÇEK DOMAIN'İNİ YAZ

APP_LOCALE=tr
APP_FALLBACK_LOCALE=en

# DATABASE
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=laravel_prod
DB_USERNAME=laravel_user
DB_PASSWORD=[ADIM 2'de oluşturduğun şifre]

# CACHE & SESSION
CACHE_DRIVER=redis
CACHE_STORE=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=720

# QUEUE
QUEUE_CONNECTION=redis

# REDIS
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# RESPONSE CACHE
RESPONSE_CACHE_ENABLED=true
RESPONSE_CACHE_DRIVER=redis
RESPONSE_CACHE_LIFETIME=3600

# FILESYSTEM
FILESYSTEM_DISK=public

# MAIL (Plesk SMTP)
MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=587
MAIL_USERNAME=noreply@domain.com
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@domain.com
MAIL_FROM_NAME="${APP_NAME}"

# LOG
LOG_CHANNEL=stack
LOG_STACK=daily
LOG_LEVEL=error
LOG_DEPRECATIONS_CHANNEL=null

# API KEYS (DEPLOYMENT_YEREL.md'den kopyala)
OPENAI_API_KEY=[Buraya gerçek API key yapıştır]
ANTHROPIC_API_KEY=[Buraya gerçek API key yapıştır]
DEEPSEEK_API_KEY=[Buraya gerçek API key yapıştır]

# MODÜL SİSTEM AYARLARI
SYSTEM_LANGUAGES=tr,en
DEFAULT_LANGUAGE=tr
ADMIN_PER_PAGE=10
FRONT_PER_PAGE=12
MODULE_CACHE_ENABLED=true
CACHE_TTL_LIST=3600
CACHE_TTL_DETAIL=7200
CACHE_TTL_API=1800
RESPONSE_CACHE_ENABLED=true
RESPONSE_CACHE_DRIVER=redis
RESPONSE_CACHE_LIFETIME=3600
MODULE_QUEUE_CONNECTION=redis
MODULE_QUEUE_NAME=tenant_isolated
QUEUE_TIMEOUT=300
QUEUE_TRIES=3
QUEUE_RETRY_AFTER=90
MEDIA_MAX_FILE_SIZE=10240
```

**Kaydet:**
- Nano: `Ctrl+X`, `Y`, `Enter`
- Vim: `ESC`, `:wq`, `Enter`

---

**8.3 .env Doğru mu Kontrol:**
```bash
cat .env | grep -E "DB_DATABASE|APP_URL|APP_DEBUG"

# DB_DATABASE=laravel_prod
# APP_URL=https://domain.com
# APP_DEBUG=false
# Bu değerleri görmelisin
```

---

### ☐ ADIM 9: APP_KEY Oluştur

**9.1 Key Generate:**
```bash
php artisan key:generate
```

**Beklenen Çıktı:**
```
Application key set successfully.
```

---

**9.2 Key Oluştu mu Kontrol:**
```bash
cat .env | grep APP_KEY

# APP_KEY=base64:... (uzun bir string görmelisin)
```

---

### ☐ ADIM 10: Storage Link & Permissions

**10.1 Storage Link Oluştur:**
```bash
php artisan storage:link
```

**Beklenen:**
```
The [public/storage] link has been connected to [storage/app/public].
```

---

**10.2 Link Doğru mu Kontrol:**
```bash
ls -la public/storage

# Symlink olarak storage/app/public'i göstermeli
```

---

**10.3 Permissions Ayarla:**
```bash
# Storage ve bootstrap/cache yazılabilir olmalı
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Grup sahipliği (Plesk kullanıcısı)
chown -R username:psacln storage bootstrap/cache

# username yerine gerçek kullanıcı adını yaz
```

---

**10.4 Permission Kontrol:**
```bash
ls -la storage/
ls -la bootstrap/cache/

# Tümü 775 veya 755 izinlerde olmalı
```

---

### ☐ ADIM 11: Migration & Seeder Çalıştır

**11.1 Database Bağlantısı Test:**
```bash
php artisan db:show
```

**Beklenen:**
```
MySQL 8.0.x
Database: laravel_prod
Connection: mysql
```

---

**11.2 Migration Çalıştır:**
```bash
php artisan migrate --force
```

> **DİKKAT:** `--force` bayrağı production ortamda gerekli

**Beklenen:**
```
Migration table created successfully.
Migrating: 2014_10_12_000000_create_users_table
Migrated:  2014_10_12_000000_create_users_table (XX.XXms)
...
```

---

**11.3 Migration Başarılı mı Kontrol:**
```bash
php artisan migrate:status
```

**Beklenen:**
```
Ran? Migration
Yes  2014_10_12_000000_create_users_table
Yes  2014_10_12_100000_create_password_reset_tokens_table
...
```

---

**11.4 AI Provider Seeder Çalıştır (ZORUNLU):**
```bash
php artisan db:seed --class=\\Modules\\AI\\Database\\Seeders\\AIProviderSeeder --force
```

**Beklenen:**
```
INFO  Seeding database.
```

---

**11.5 Ana Seeder (İSTEĞE BAĞLI - Dikkatli!):**
```bash
# SADECE test/demo verisi gerekiyorsa çalıştır
# Production'da GERÇEK VERİ varsa ÇALIŞTIRMA!

php artisan db:seed --force
```

---

**11.6 Database Doğrula:**
```bash
mysql -u laravel_user -p

SHOW TABLES;
# users, migrations, ai_providers, vb. tablolar olmalı

SELECT name, is_active FROM ai_providers;
# openai, anthropic, deepseek görünmeli

exit
```

---

### ☐ ADIM 12: Cache Oluştur

**12.1 Config Cache:**
```bash
php artisan config:cache
```

---

**12.2 Route Cache:**
```bash
php artisan route:cache
```

---

**12.3 View Cache:**
```bash
php artisan view:cache
```

---

**12.4 Event Cache:**
```bash
php artisan event:cache
```

---

**12.5 Autoload Optimize:**
```bash
composer dump-autoload --optimize --classmap-authoritative
```

---

**12.6 Cache Çalıştığını Kontrol:**
```bash
ls -la bootstrap/cache/

# config.php
# routes-v7.php
# events.php
# Bu dosyalar olmalı
```

---

### ☐ ADIM 13: Plesk Document Root Ayarla

**13.1 Plesk UI'den:**

```
Domains → domain.com → Hosting Settings
```

**Değiştir:**
```
Document root: /httpdocs/public
```

> **ÇOK ÖNEMLİ:** Document root MUTLAKA `/public` olmalı!

---

**13.2 PHP Ayarları:**

```
Domains → domain.com → PHP Settings
```

**Ayarla:**
```
PHP version: 8.3 veya 8.4
PHP handler: FPM application (önerilen)

memory_limit: 512M
max_execution_time: 300
upload_max_filesize: 64M
post_max_size: 64M
max_input_vars: 5000
display_errors: Off
log_errors: On
```

---

### ☐ ADIM 14: Cron Job Ayarla

**14.1 Laravel Scheduler (ZORUNLU):**

```
Plesk → Tools & Settings → Scheduled Tasks → Add Task
```

**Ayarlar:**
```
Command: /opt/plesk/php/8.3/bin/php /var/www/vhosts/domain.com/httpdocs/artisan schedule:run >> /dev/null 2>&1

Run: Custom
Cron expression: * * * * *
Description: Laravel Scheduler
```

---

**14.2 Queue Worker (ÖNERİLİR):**

```
Plesk → Scheduled Tasks → Add Task
```

**Ayarlar:**
```
Command: /opt/plesk/php/8.3/bin/php /var/www/vhosts/domain.com/httpdocs/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600 > /dev/null 2>&1

Run: Reboot
Description: Laravel Queue Worker
```

---

**14.3 Cron Çalıştığını Kontrol:**
```bash
# 1 dakika bekle, sonra kontrol et
php artisan schedule:list

# Storage log'ları kontrol et
tail -50 storage/logs/laravel.log
```

---

### ☐ ADIM 15: SSL Sertifikası Kur

**15.1 Let's Encrypt (ÜCRETSİZ):**

```
Plesk → SSL/TLS Certificates → Let's Encrypt
```

**Ayarlar:**
```
Email: admin@domain.com

Domain names:
☑ domain.com
☑ www.domain.com
☑ *.domain.com  (wildcard - tenant'lar için)

Securing:
☑ Include a 'www' subdomain
☑ Assign the certificate to the mail domain

☑ Keep website secured (redirect HTTP to HTTPS)
```

**Get it free (Install)**

---

**15.2 SSL Başarılı mı Kontrol:**
```bash
curl -I https://domain.com | grep "HTTP"

# HTTP/2 200 görmelisin
```

---

**15.3 Wildcard SSL Test:**
```bash
curl -I https://test.domain.com | grep "HTTP"

# SSL hatası almamalısın
```

---

### ☐ ADIM 16: İLK TEST

**16.1 Ana Sayfa Test:**
```bash
curl -I https://domain.com
```

**Beklenen:**
```
HTTP/2 200
```

---

**16.2 Admin Panel Test:**
```bash
curl -I https://domain.com/admin
```

**Beklenen:**
```
HTTP/2 200 veya 302 (redirect)
```

---

**16.3 Browser Test:**

**Aç:**
- https://domain.com
- https://domain.com/login
- https://domain.com/admin

**Beklenen:**
- ✅ Sayfalar yükleniyor
- ✅ CSS/JS çalışıyor
- ✅ Resimler görünüyor

---

### ☐ ADIM 17: Test Tenant Oluştur

**17.1 Tinker ile Tenant Oluştur:**
```bash
php artisan tinker
```

**Komutlar:**
```php
$tenant = \App\Models\Tenant::create([
    'id' => 'test-tenant',
    'name' => 'Test Tenant'
]);

$tenant->domains()->create([
    'domain' => 'test.domain.com'
]);

exit
```

---

**17.2 Tenant Domain Test:**
```bash
curl -I https://test.domain.com
```

**Beklenen:**
```
HTTP/2 200
```

---

**17.3 Tenant Browser Test:**

**Aç:**
- https://test.domain.com

**Beklenen:**
- ✅ Tenant sayfası açılıyor
- ✅ Farklı database kullanıyor

---

### ☐ ADIM 18: Log Kontrol

**18.1 Laravel Log:**
```bash
tail -100 storage/logs/laravel.log
```

**Bakılacaklar:**
- ❌ ERROR seviyesi log var mı?
- ❌ Exception var mı?
- ✅ Normal işlem logları olmalı

---

**18.2 Nginx Error Log:**
```bash
tail -50 /var/log/nginx/error.log

# veya Plesk'te:
# Domains → domain.com → Logs → Error Log
```

---

**18.3 PHP-FPM Log:**
```bash
tail -50 /opt/plesk/php/8.3/var/log/php-fpm.log
```

---

### ☐ ADIM 19: Performance Optimizasyon

**19.1 OPcache Kontrol:**
```bash
php -i | grep opcache.enable

# opcache.enable => On => On olmalı
```

---

**19.2 Redis Cache Test:**
```bash
redis-cli

KEYS *laravel*
# Cache key'leri görmelisin

PING
# PONG dönmeli

exit
```

---

**19.3 Queue Test:**
```bash
# Bir test job gönder
php artisan tinker

dispatch(new \App\Jobs\TestJob());

exit

# Log'u kontrol et
tail -20 storage/logs/laravel.log
```

---

### ☐ ADIM 20: Güvenlik Kontrolleri

**20.1 .env Erişimi Engellenmiş mi:**
```bash
curl https://domain.com/.env

# 403 Forbidden veya 404 Not Found olmalı
# .env içeriği ASLA görünmemeli
```

---

**20.2 Storage Klasörü Korunuyor mu:**
```bash
curl https://domain.com/storage/logs/laravel.log

# 403 veya 404 olmalı
```

---

**20.3 Debug Mode Kapalı mı:**
```bash
cat .env | grep APP_DEBUG

# APP_DEBUG=false olmalı
```

---

**20.4 Gereksiz Dosyalar Silinmiş mi:**
```bash
ls -la public/

# README.md, phpinfo.php, test.php gibi dosyalar OLMAMALI
```

---

## ✅ DEPLOYMENT TAMAMLANDI!

### 🎉 BAŞARILI CHECKLIST

```
☑ PHP 8.2+ kurulu ve extension'lar aktif
☑ Database oluşturuldu ve test edildi
☑ Wildcard subdomain ayarlandı
☑ Git clone başarılı
☑ Composer install tamamlandı
☑ NPM build tamamlandı
☑ .env dosyası oluşturuldu ve ayarlandı
☑ APP_KEY generate edildi
☑ Storage link ve permissions ayarlandı
☑ Migration başarılı
☑ AI Provider seeder çalıştırıldı
☑ Cache'ler oluşturuldu
☑ Document root /public olarak ayarlandı
☑ PHP ayarları optimize edildi
☑ Cron job'lar kuruldu
☑ SSL sertifikası kuruldu
☑ Ana site çalışıyor
☑ Tenant sistemi test edildi
☑ Log'lar kontrol edildi
☑ Performance test yapıldı
☑ Güvenlik kontrolleri tamamlandı
```

---

## 🔧 SORUN GİDERME

### 500 Internal Server Error

**Kontrol:**
```bash
tail -100 storage/logs/laravel.log
```

**Çözüm:**
- Permission hatası: `chmod -R 775 storage bootstrap/cache`
- .env hatası: `php artisan config:clear`
- Cache hatası: `php artisan optimize:clear`

---

### Migration Hatası

**Kontrol:**
```bash
php artisan migrate:status
```

**Çözüm:**
```bash
# Fresh migration (DİKKAT: Tüm veriyi siler!)
php artisan migrate:fresh --force

# Veya rollback
php artisan migrate:rollback --force
php artisan migrate --force
```

---

### Queue Çalışmıyor

**Kontrol:**
```bash
php artisan queue:work redis --once
```

**Çözüm:**
```bash
# Queue restart
php artisan queue:restart

# Redis kontrol
redis-cli ping
```

---

### Cache Sorunları

**Çözüm:**
```bash
# Tüm cache'leri temizle
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Tekrar oluştur
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

### SSL Hatası

**Kontrol:**
```bash
curl -I https://domain.com
```

**Çözüm:**
- Let's Encrypt'i tekrar kur
- DNS ayarlarını kontrol et
- Firewall'da 443 portunu aç

---

## 📞 DESTEK

**Log Dosyaları:**
- Laravel: `storage/logs/laravel.log`
- Nginx: `/var/log/nginx/error.log`
- PHP-FPM: `/opt/plesk/php/8.3/var/log/php-fpm.log`

**Faydalı Komutlar:**
```bash
# Tüm service'leri restart
sudo systemctl restart nginx
sudo systemctl restart php-fpm
sudo systemctl restart redis

# Cache temizle
php artisan optimize:clear

# Database sıfırla (DİKKAT!)
php artisan migrate:fresh --seed --force
```

---

**HAZIRLAYAN:** Claude AI (Yerel Ortam)
**UYGULAYACAK:** Claude AI (Sunucu)
**TARİH:** 2025-10-04
**DURUM:** Production Ready ✅
