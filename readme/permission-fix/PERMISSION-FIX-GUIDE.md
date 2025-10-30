# 🛠️ LARAVEL PERMISSION & CACHE FIX GUIDE

## 🚨 SORUN

Sürekli tekrar eden permission hataları:
```
file_put_contents(storage/framework/views/XXX.php): Permission denied
No application encryption key has been specified
Access denied for user 'root'@'localhost'
```

## 🎯 KÖK NEDENLER

### 1. **Config Cache Silindi**
- `php artisan config:clear` tek başına çalıştırılırsa
- `php artisan route:cache` config cache'i de silebiliyor
- Config cache olmadan Laravel `.env` parse edemiyor
- Sonuç: Database, encryption, tüm config bozuluyor

### 2. **Permission Mismatch**
- PHP-FPM user: `tuufi.com_`
- Dosya owner: `psacln` (Apache default)
- Group: `psacln`
- PHP-FPM dosya yazamıyor

### 3. **OPcache Stuck**
- Eski permissions cache'lenmiş
- Restart gerekiyor

---

## ✅ KALICI ÇÖZÜM

### ADIM 1: Permissions Düzeltme (Her deployment sonrası)

```bash
# Storage ve cache permissions
find storage -type d -exec chmod 775 {} \;
find storage -type f -exec chmod 664 {} \;
find bootstrap/cache -type d -exec chmod 775 {} \;
find bootstrap/cache -type f -exec chmod 664 {} \;

# Owner düzeltme
chown -R tuufi.com_:psacln storage bootstrap/cache

# Public uploads (eğer varsa)
chown -R tuufi.com_:psacln public/uploads
```

### ADIM 2: Config Cache Yenileme (ATOMIK)

**❌ ASLA YAPMA:**
```bash
php artisan config:clear  # TEK BAŞINA YAPMA!
```

**✅ DOĞRU YÖNTEM:**
```bash
# Composer script kullan (atomic operation)
composer config-refresh

# Veya manuel:
php artisan config:clear && php artisan config:cache
```

### ADIM 3: OPcache Reset

```bash
# Web-based
curl -s -k https://ixtif.com/opcache-reset.php

# Veya PHP-FPM restart
systemctl restart plesk-php83-fpm
```

---

## 🔧 HIZLI FIX KOMUTLARI

### Tek Komutta Fix (Production)

```bash
# 1. Permissions
find storage bootstrap/cache -type d -exec chmod 775 {} \; && \
find storage bootstrap/cache -type f -exec chmod 664 {} \; && \
chown -R tuufi.com_:psacln storage bootstrap/cache

# 2. Cache refresh (atomic)
composer config-refresh

# 3. OPcache reset
curl -s -k https://ixtif.com/opcache-reset.php
```

### Emergency Recovery (Config cache yok)

```bash
# Config cache yeniden oluştur
composer config-refresh

# Veya manuel
php artisan config:cache && \
php artisan route:cache && \
php artisan view:cache
```

---

## 📋 DEPLOYMENT CHECKLIST

Her deployment/güncelleme sonrası:

- [ ] `composer config-refresh` çalıştır
- [ ] Storage permissions kontrol et
- [ ] OPcache reset et
- [ ] HTTP 200 test et

---

## 🚨 ÖNLEME (Prevention)

### 1. Composer Scripts Kullan

`composer.json`:
```json
{
  "scripts": {
    "config-refresh": [
      "@php artisan config:clear",
      "@php artisan config:cache",
      "@php artisan route:cache",
      "@php artisan view:cache"
    ],
    "post-update-cmd": [
      "@config-refresh"
    ]
  }
}
```

### 2. Git Hook (Post-merge)

`.git/hooks/post-merge`:
```bash
#!/bin/bash
echo "Running post-merge fixes..."
composer config-refresh
echo "✅ Config cache refreshed"
```

### 3. Cron Job (Günlük Permission Check)

```bash
# /etc/cron.daily/laravel-permission-fix
0 3 * * * /var/www/vhosts/tuufi.com/httpdocs/readme/permission-fix/fix-permissions.sh
```

---

## 🐛 DEBUG KOMUTLARI

### Config Cache Kontrol

```bash
# Cache var mı?
ls -la bootstrap/cache/config.php

# İçeriği doğru mu?
php artisan config:show app.key
php artisan config:show database.default
```

### Permission Kontrol

```bash
# Storage permissions
ls -la storage/framework/views/

# PHP-FPM user kim?
ps aux | grep php-fpm | head -1

# Manuel write test
sudo -u tuufi.com_ touch storage/framework/views/test.txt
```

### Laravel Log

```bash
# Son hatalar
tail -100 storage/logs/laravel.log | grep ERROR

# Real-time monitor
tail -f storage/logs/laravel.log
```

---

## 📞 SORUN GİDERME

### Hata: "Permission denied"

**Çözüm:**
```bash
chown -R tuufi.com_:psacln storage bootstrap/cache
chmod -R 775 storage/framework
```

### Hata: "No application encryption key"

**Çözüm:**
```bash
composer config-refresh
```

### Hata: "Access denied for user 'root'@'localhost'"

**Çözüm:**
```bash
# Config cache yok!
composer config-refresh
```

### Hata: "View [XXX] not found"

**Çözüm:**
```bash
php artisan view:clear
composer config-refresh
```

---

## 🎓 TEKNİK DETAYLAR

### Neden Config Cache Zorunlu?

Laravel 11+ production'da:
- `env()` sadece config dosyalarında çalışır
- Application code'da `env()` → `null` döner
- Config cache olmadan tüm config `null`
- Database, encryption, routing BOZULUR

### Permission Yapısı

```
Directory: 775 (drwxrwxr-x)
├── Owner: tuufi.com_ (read+write+execute)
├── Group: psacln (read+write+execute)
└── Others: (read+execute)

File: 664 (-rw-rw-r--)
├── Owner: tuufi.com_ (read+write)
├── Group: psacln (read+write)
└── Others: (read only)
```

### PHP-FPM Process

```bash
# tuufi.com_ user ile çalışıyor
ps aux | grep php-fpm.*tuufi

# Grup üyeliği
groups tuufi.com_
# Output: tuufi.com_ psacln psaserv
```

---

## 📌 NOTLAR

- **Config cache** olmadan production çalışmaz
- **Permission** 664/775 + owner:group doğru olmalı
- **OPcache** restart bazen gerekli
- **Composer scripts** otomasyonla hata önlenir

---

**Son Güncelleme:** 2025-10-30
**Yazar:** Claude Code
