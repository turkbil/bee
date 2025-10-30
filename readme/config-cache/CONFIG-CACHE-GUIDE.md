# 📦 LARAVEL CONFIG CACHE - KAPSAMLI KILAVUZ

## 🎯 CONFIG CACHE NEDİR?

**Config Cache**, Laravel'in tüm konfigürasyon dosyalarını (.env + config/*.php) **tek bir optimize edilmiş PHP dosyasına** derlemesidir.

### 📁 Dosya Konumu:
```
bootstrap/cache/config.php
```

**Tipik Boyut:** 100-200 KB (tüm config ayarları)
**Format:** PHP array (optimized for OPcache)
**Oluşturma:** `php artisan config:cache`

---

## 🔍 İÇİNDE NELER VAR?

Config cache dosyası **TÜM** Laravel konfigürasyonlarını tek array'de toplar:

### 1. Database Bağlantıları
```php
'database' => [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'tuufi_4ekim',
            'username' => 'tuufi_4ekim',
            'password' => '***********',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]
    ]
]
```

### 2. Application Settings
```php
'app' => [
    'name' => 'Tuufi',
    'env' => 'production',
    'debug' => false,
    'url' => 'https://ixtif.com',
    'key' => 'base64:QnI65Or5jAB2yuHWZyf4PJo1f03Y4aN+9w7OlT70Z08=',
    'timezone' => 'Europe/Istanbul',
    'locale' => 'tr',
]
```

### 3. Diğer Tüm Config Dosyaları
- **Cache** - Redis/Memcached/File ayarları
- **Session** - Session storage, lifetime, cookie
- **Queue** - Redis/Database queue ayarları
- **Mail** - SMTP, Mailgun, SES ayarları
- **Broadcasting** - Pusher, Redis broadcast
- **Filesystems** - S3, Local, Public disk ayarları
- **Logging** - Log channels, stack, levels
- **Auth** - Guards, providers, passwords
- **Services** - 3rd party API keys (AWS, Stripe vb.)
- **Tenancy** - Multi-tenant ayarları
- **AI** - OpenAI, DeepSeek, Gemini keys
- **Custom modules** - Kendi modül config'lerin

---

## ⚡ NEDEN KRİTİK?

### ❌ Config Cache YOKSA (Development):

```php
// Laravel her istekte şunu yapar:

1. .env dosyasını oku ve parse et (Disk I/O)
2. config/*.php dosyalarını tek tek yükle (50+ dosya!)
3. env() fonksiyonunu her yerde çağır
4. Tüm ayarları runtime'da işle
5. Her istekte aynı işi tekrarla

SONUÇ:
├─ Her istekte 50-200ms overhead
├─ Yüksek disk I/O
├─ env() application code'da çalışır (yavaş)
└─ Development için OK, Production için FELAKET!
```

### ✅ Config Cache VARSA (Production):

```php
// Laravel şunu yapar:

1. Tek dosya oku: bootstrap/cache/config.php
2. OPcache'den yükle (RAM'den, disk yok!)
3. Tüm config hazır!

SONUÇ:
├─ Her istekte 1-5ms (10-50x hızlı!)
├─ Sıfır disk I/O (OPcache kullanır)
├─ env() gerekmez, config() kullan
└─ Production zorunluluğu!
```

### 📊 Performans Karşılaştırması:

| Durum | İstek Başı Süre | Disk I/O | Memory |
|-------|----------------|----------|--------|
| ❌ Cache YOK | 50-200ms | 50+ dosya read | Dinamik alloc |
| ✅ Cache VAR | 1-5ms | 1 dosya (OPcache) | Cached array |
| **Kazanç** | **10-50x hız** | **%98 azalma** | **%80 azalma** |

---

## 🚨 CONFIG CACHE OLMAYINCA NE OLUR?

### Sistem Çöküşü Senaryoları:

#### 1. Encryption Key Hatası
```bash
ERROR: "No application encryption key has been specified"

Neden?
├─ config('app.key') = null döner
├─ Laravel şifreleme yapamaz
├─ Session/Cookie encrypt edilemez
└─ Sistem tamamen down!
```

#### 2. Database Bağlantı Hatası
```bash
ERROR: "Access denied for user 'root'@'localhost'"

Neden?
├─ config('database.connections.mysql') = null
├─ Laravel fallback kullanır: root@localhost
├─ Yetki yok, bağlantı başarısız
└─ Tüm DB işlemleri çöker!
```

#### 3. Tenant Sistemi Çöküşü
```bash
ERROR: "Tenant not found"

Neden?
├─ config('tenancy.central_domains') = null
├─ Tenant middleware domain kontrol edemez
├─ Routing çalışmaz
└─ Multi-tenant sistem tamamen bozulur!
```

#### 4. Service Provider Boot Hatası
```bash
ERROR: "file_put_contents(storage/framework/views/XXX.php): Failed to open stream"

Neden?
├─ Service provider boot() içinde DB sorgusu atar
├─ DB config null → Bağlantı başarısız
├─ Exception throw edilir → Boot tamamlanamaz
└─ Blade compile edilemez → 500 Server Error!
```

#### 5. Queue/Job Sistemi Çöker
```bash
ERROR: "Queue connection [redis] not configured"

Neden?
├─ config('queue.connections.redis') = null
├─ Job dispatch edilemez
├─ Queue worker çalışamaz
└─ Async işlemler durur!
```

---

## 🛠️ CONFIG CACHE YÖNETİMİ

### ✅ DOĞRU KOMUTLAR

#### 1. Atomic Refresh (ÖNERİLEN - Production Safe)
```bash
# Composer script ile (tek komut, atomik işlem)
composer config-refresh

# Bu komut şunu yapar:
# 1. php artisan config:clear
# 2. php artisan config:cache
# 3. php artisan route:cache
# 4. php artisan view:cache
```

#### 2. Manuel Atomic Refresh
```bash
# Clear + Cache bir arada (downtime = 0.5-2 saniye)
php artisan config:clear && php artisan config:cache
```

#### 3. Sadece Config Cache Oluştur
```bash
# Eğer cache zaten yoksa
php artisan config:cache
```

#### 4. Config Görüntüleme (Cache'i bozmaz)
```bash
# Belirli config değerini göster
php artisan config:show app.key
php artisan config:show database.connections.mysql
php artisan config:show tenancy.central_domains

# Tüm app config'i göster
php artisan config:show app

# Dosya boyutunu kontrol et
ls -lh bootstrap/cache/config.php
```

### ❌ YANLIŞ (TEHLİKELİ) KOMUTLAR

#### 1. Tek Başına Clear (SİSTEM ÇÖKER!)
```bash
# ❌ ASLA YAPMA!
php artisan config:clear

# SORUN:
# ├─ Config cache silindi
# ├─ Yeni cache oluşturulmadı
# ├─ Sistem 5-30 saniye DOWN!
# └─ Tüm request'ler 500 Error!
```

#### 2. Route Cache (Config'i de siler!)
```bash
# ⚠️ DİKKAT: Route cache bazen config cache'i de temizler!
php artisan route:cache

# GÜVENLİ KULLANIM:
php artisan route:cache && php artisan config:cache
```

#### 3. Cache Clear (Her şeyi siler!)
```bash
# ❌ Production'da asla!
php artisan cache:clear

# Bu komut şunları temizler:
# ├─ Application cache (OK)
# ├─ Config cache (TEHLİKELİ!)
# └─ Route cache (TEHLİKELİ!)

# GÜVENLİ ALTERNATİF:
# Sadece application cache temizle:
php artisan cache:forget cache_key
# veya
php artisan responsecache:clear
```

---

## 🔄 NE ZAMAN YENİLEMEN GEREKİR?

### ✅ Config Cache Yenileme Gereklilikleri:

**1. .env Dosyası Değiştiğinde:**
```bash
# .env güncellendi
DB_HOST=127.0.0.1  →  DB_HOST=192.168.1.100

# Cache yenile
composer config-refresh
```

**2. config/*.php Dosyası Değiştiğinde:**
```bash
# config/database.php güncellendi
'timeout' => 60  →  'timeout' => 120

# Cache yenile
composer config-refresh
```

**3. Yeni Environment Variable Eklediğinde:**
```bash
# .env'ye yeni variable
OPENAI_API_KEY=sk-xxx

# config/ai.php'ye yeni ayar
'openai' => [
    'api_key' => env('OPENAI_API_KEY'),
]

# Cache yenile
composer config-refresh
```

**4. Deployment Sonrası (ZORUNLU!):**
```bash
# Git pull sonrası
git pull origin main

# Cache yenile (otomatik olmalı)
composer config-refresh
```

### ⚠️ Yenilemesen Ne Olur?

```bash
# Senaryo: .env'de DB şifresi değişti, cache yenilenmedi

.env: DB_PASSWORD=yeni_sifre
bootstrap/cache/config.php: 'password' => 'eski_sifre'

SONUÇ:
├─ Laravel eski şifreyi kullanır (cache'den)
├─ Database bağlantısı başarısız
└─ Site çöker!
```

---

## 🤖 OTOMATİZASYON

### 1. Composer Scripts (ÖNERİLEN)

**composer.json:**
```json
{
  "scripts": {
    "config-refresh": [
      "@php artisan config:clear",
      "@php artisan config:cache",
      "@php artisan route:cache",
      "@php artisan view:cache"
    ],
    "cache-production": [
      "@config-refresh",
      "@php artisan event:cache",
      "@php artisan optimize"
    ],
    "post-update-cmd": [
      "@config-refresh"
    ],
    "post-autoload-dump": [
      "@php artisan package:discover --ansi"
    ]
  }
}
```

**Kullanım:**
```bash
# Development: Config yenile
composer config-refresh

# Production: Tüm cache oluştur
composer cache-production

# Composer update sonrası otomatik
composer update  # post-update-cmd çalışır
```

### 2. Git Hooks

**.git/hooks/post-merge:**
```bash
#!/bin/bash

echo "🔄 Git pull sonrası cache yenileniyor..."

# Config cache yenile
composer config-refresh

echo "✅ Cache yenilendi!"
```

**Aktifleştirme:**
```bash
chmod +x .git/hooks/post-merge
```

### 3. Deployment Script

**deploy.sh:**
```bash
#!/bin/bash
set -e

echo "🚀 Deployment başladı..."

# 1. Git pull
git pull origin main

# 2. Composer dependencies
composer install --no-dev --optimize-autoloader

# 3. Cache yenileme (ZORUNLU!)
composer config-refresh

# 4. Storage permissions
chmod -R 775 storage bootstrap/cache

# 5. OPcache reset
curl -s -k https://ixtif.com/opcache-reset.php > /dev/null

echo "✅ Deployment tamamlandı!"
```

### 4. Cron Job (Proaktif Kontrol)

**/etc/cron.daily/laravel-config-check:**
```bash
#!/bin/bash

PROJECT_ROOT="/var/www/vhosts/tuufi.com/httpdocs"

cd "$PROJECT_ROOT"

# Config cache var mı kontrol et
if [ ! -f "bootstrap/cache/config.php" ]; then
    echo "❌ Config cache eksik! Yeniden oluşturuluyor..."
    php artisan config:cache
    echo "✅ Config cache oluşturuldu"
fi

# Config cache 7 günden eski mi?
if [ "$(find bootstrap/cache/config.php -mtime +7)" ]; then
    echo "⚠️  Config cache 7 günden eski, yenileniyor..."
    composer config-refresh
    echo "✅ Config cache yenilendi"
fi
```

---

## 🧪 DEBUG & DOĞRULAMA

### Config Cache Kontrolü

#### 1. Dosya Var mı?
```bash
# Dosya kontrolü
ls -lh bootstrap/cache/config.php

# Çıktı:
# -rw-r--r-- 1 root root 152K Oct 30 21:50 bootstrap/cache/config.php
```

#### 2. İçerik Doğru mu?
```bash
# Specific config değerlerini kontrol et
php artisan config:show app.key
php artisan config:show database.connections.mysql.host
php artisan config:show tenancy.central_domains

# Çıktı görüyorsan → Config cache çalışıyor ✅
# null görüyorsan → Config cache bozuk ❌
```

#### 3. Dosya Permissions Doğru mu?
```bash
# Permission kontrolü
ls -la bootstrap/cache/config.php

# Olması gereken:
# -rw-r--r-- (644) veya -rw-rw-r-- (664)
# Owner: tuufi.com_ veya root
# Group: psacln

# Düzeltme:
chmod 664 bootstrap/cache/config.php
chown tuufi.com_:psacln bootstrap/cache/config.php
```

#### 4. Cache Güncel mi?
```bash
# Config dosyası ve cache'in tarihlerini karşılaştır
stat -c "%y %n" config/database.php bootstrap/cache/config.php

# Eğer config/database.php daha yeni → Cache eski, yenile!
composer config-refresh
```

### Hata Tespiti

#### Test 1: Config Değeri Oku
```bash
# Tinker ile test
php artisan tinker

>>> config('app.key')
=> "base64:QnI65Or5jAB2yuHWZyf4PJo1f03Y4aN+9w7OlT70Z08="  // ✅ OK

>>> config('app.key')
=> null  // ❌ CONFIG CACHE YOK!
```

#### Test 2: Database Bağlantısı
```bash
php artisan tinker

>>> DB::connection()->getPdo()
=> PDO {#xyz}  // ✅ OK

>>> DB::connection()->getPdo()
=> Access denied for user 'root'@'localhost'  // ❌ CONFIG CACHE YOK!
```

#### Test 3: Config Cache İçeriğine Bak
```bash
# İlk 100 satır
head -100 bootstrap/cache/config.php

# Belirli bir key ara
grep -A 10 "'database' =>" bootstrap/cache/config.php

# Dosya boyutu (100KB+ olmalı)
du -h bootstrap/cache/config.php
```

---

## 🎓 TEKNİK DETAYLAR

### Laravel 11+ Config Sistemi

#### env() vs config()

**❌ YANLIŞ (Application Code):**
```php
// Controller/Model/View'de:
$apiKey = env('OPENAI_API_KEY');  // ❌ Production'da null döner!

// Sebep:
// Laravel 11+ env() sadece config dosyalarında çalışır
// Application code'da env() = null
```

**✅ DOĞRU (Application Code):**
```php
// Controller/Model/View'de:
$apiKey = config('ai.openai.api_key');  // ✅ Config cache'den okur

// config/ai.php:
'openai' => [
    'api_key' => env('OPENAI_API_KEY'),  // ✅ Config'de env() OK
]
```

#### Config Cache Load Order

```php
// Laravel boot sequence:

1. bootstrap/cache/config.php var mı?
   ├─ VAR → Yükle, env() disabled
   └─ YOK → config/*.php yükle + env() parse et

2. config() helper:
   ├─ Cache varsa: return $cached['key']
   └─ Cache yoksa: return $config['key'] (runtime)

3. Service providers boot:
   ├─ config() ile ayarları oku
   └─ Database/Cache/Queue bağlantıları kur
```

#### OPcache Optimizasyonu

Config cache dosyası **OPcache** tarafından cache'lenir:

```bash
# OPcache config cache'i yükler:
bootstrap/cache/config.php → OPcache → RAM

# Sonuç:
# ├─ Disk I/O = 0
# ├─ Parse time = 0
# └─ Memory access = ultra hızlı
```

**OPcache Reset Gerekir:**
```bash
# Config cache güncellediysen, OPcache'i reset et
curl -s -k https://ixtif.com/opcache-reset.php

# Veya PHP-FPM restart
systemctl restart plesk-php83-fpm
```

### Cache Dosyası Formatı

**bootstrap/cache/config.php:**
```php
<?php

// Tek satır, optimize edilmiş array return
return [
    'app' => ['name' => 'Tuufi', ...],
    'database' => ['connections' => [...]],
    'cache' => [...],
    'queue' => [...],
    // ...
];
```

**Özellikler:**
- ✅ Tek `return` statement (hızlı parse)
- ✅ OPcache-friendly format
- ✅ Tüm env() çağrıları resolved
- ✅ No closures, no dynamic code

### Multi-Tenant Config Cache

**Tenant sistemi olan projelerde:**

```php
// Central domain'de (tuufi.com):
bootstrap/cache/config.php → Central config

// Tenant domain'de (ixtif.com):
bootstrap/cache/config.php → Aynı dosya!
tenant() helper ile tenant-specific değerler

// Tenant config override:
config(['database.default' => 'tenant']);
config(['app.name' => tenant('name')]);
```

**Tenant context config cache'i etkilemez!**

---

## 📋 DEPLOYMENT CHECKLIST

Her deployment/güncelleme sonrası:

- [ ] `composer config-refresh` çalıştır
- [ ] Config cache dosyasını kontrol et: `ls -lh bootstrap/cache/config.php`
- [ ] Permissions doğru: `chmod 664` + `chown tuufi.com_:psacln`
- [ ] OPcache reset et: `curl https://domain.com/opcache-reset.php`
- [ ] Test: `php artisan config:show app.key` (null değil mi?)
- [ ] Test: HTTP 200 kontrol et: `curl -I https://domain.com/`
- [ ] Laravel log kontrol: `tail -50 storage/logs/laravel.log`

---

## 🚨 ACİL DURUM RECOVERY

### Config Cache Kayboldu - Sistem Down!

**Belirtiler:**
```bash
# 500 Server Error
# "No application encryption key has been specified"
# "Access denied for user 'root'@'localhost'"
```

**Hızlı Fix (30 saniye):**
```bash
# 1. Hemen config cache oluştur
cd /var/www/vhosts/tuufi.com/httpdocs
php artisan config:cache

# 2. Permissions düzelt
chmod 664 bootstrap/cache/config.php
chown tuufi.com_:psacln bootstrap/cache/config.php

# 3. OPcache reset
curl -s -k https://ixtif.com/opcache-reset.php

# 4. Test
curl -I https://ixtif.com/

# 5. Log kontrol
tail -50 storage/logs/laravel.log
```

**Kalıcı Fix (Tekrar olmasın):**
```bash
# Otomatik fix script kullan
bash readme/permission-fix/fix-permissions.sh
```

---

## 📚 İLGİLİ DOKÜMANTASYON

- **Permission Fix Guide**: `readme/permission-fix/PERMISSION-FIX-GUIDE.md`
- **Deployment Guide**: `readme/deployment/DEPLOYMENT-GUIDE.md`
- **Laravel Official Docs**: https://laravel.com/docs/configuration#configuration-caching

---

## 📌 ÖZET

**Config Cache = Laravel'in Beyni**

| Durum | Sonuç |
|-------|-------|
| ✅ Config Cache VAR | Sistem çalışır, hızlı, güvenli |
| ❌ Config Cache YOK | Sistem çöker, yavaş, güvensiz |

**Altın Kurallar:**
1. **Production'da config cache ZORUNLU**
2. **ASLA `config:clear` tek başına yapma**
3. **Her deployment sonrası cache yenile**
4. **composer config-refresh kullan (atomic)**
5. **OPcache reset unutma**

---

**Son Güncelleme:** 2025-10-30
**Yazar:** Claude Code
**Proje:** Tuufi Multi-Tenant SaaS
