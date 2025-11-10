# Muzibu Module - Complete Setup Guide

**Hedef:** Yeni sunucuya Muzibu modülünü baştan sona kurmak için adım adım kılavuz.

---

## 📋 ÖNKOŞULLAR

### Sistem Gereksinimleri:
- **OS:** AlmaLinux 8.10+ / CentOS 8+ / RHEL 8+
- **PHP:** 8.2+
- **Composer:** 2.x
- **Node.js:** 16+ (npm ile)
- **Laravel:** 12.x
- **Redis:** Queue worker için

### Mevcut Laravel Kurulumu:
```bash
# Laravel framework zaten kurulu olmalı
php artisan --version  # Laravel 12.x
```

---

## 🚀 ADIM 1: FFMPEG KURULUMU

FFmpeg **HLS streaming için zorunludur**. Olmadan conversion çalışmaz.

### AlmaLinux 8.10 için:

```bash
# 1. EPEL repository'yi kontrol et/kur
sudo yum install -y epel-release

# 2. RPM Fusion repository'leri ekle (FFmpeg için gerekli)
sudo yum install -y \
  https://download1.rpmfusion.org/free/el/rpmfusion-free-release-8.noarch.rpm \
  https://download1.rpmfusion.org/nonfree/el/rpmfusion-nonfree-release-8.noarch.rpm

# 3. FFmpeg ve development paketlerini kur
sudo yum install -y ffmpeg ffmpeg-devel

# 4. Kurulumu doğrula
ffmpeg -version
# Beklenen output: ffmpeg version 4.2.10 ...
```

### Test Komutu:
```bash
# FFmpeg ile HLS conversion test et
ffmpeg -i test.mp3 -c copy -start_number 0 -hls_time 10 -hls_list_size 0 -f hls /tmp/test-output/playlist.m3u8

# Output kontrol
ls -lh /tmp/test-output/
# Beklenen: playlist.m3u8 + segment-*.ts dosyaları
```

**⚠️ Önemli:** Eğer `ffmpeg: command not found` hatası alırsanız:
```bash
# PATH'i kontrol et
which ffmpeg
# Output: /usr/bin/ffmpeg (olmalı)

# Eğer bulunamazsa:
export PATH=$PATH:/usr/bin
source ~/.bashrc
```

---

## 📦 ADIM 2: PHP GETİD3 PAKETİ

Audio metadata extraction için gerekli.

```bash
# Composer ile getID3 kur
cd /var/www/vhosts/tuufi.com/httpdocs
composer require james-heinrich/getid3

# Kurulumu doğrula
composer show james-heinrich/getid3
# Beklenen: getid3 v1.9.x ...
```

---

## 🎨 ADIM 3: NPM HLS.JS PAKETİ

Frontend HLS player için gerekli.

```bash
# HLS.js kur
npm install hls.js --save

# package.json'da kontrol et
cat package.json | grep hls.js
# Beklenen: "hls.js": "^1.x.x"
```

---

## 🗄️ ADIM 4: VERİTABANI MİGRATIONLARI

### Central Migration:
```bash
# Central database için migration yoksa pas geç
# Sadece tenant migration kullanacağız
```

### Tenant Migration:
```bash
# Tüm tenant'lar için migration çalıştır
php artisan tenants:migrate --path=Modules/Muzibu/database/migrations/tenant/2025_11_11_020022_add_hls_fields_to_muzibu_songs_table.php

# Output:
# Tenant: 1 - DONE
# Tenant: 2 - DONE
# Tenant: 3 - DONE
```

### Manual Migration (Eğer tenant:migrate çalışmazsa):
```bash
# Her tenant için manuel
php artisan tinker

# Tenant 1
>>> tenancy()->initialize(1);
>>> \Artisan::call('migrate', [
      '--path' => 'Modules/Muzibu/database/migrations/tenant/2025_11_11_020022_add_hls_fields_to_muzibu_songs_table.php'
    ]);

# Tenant 2
>>> tenancy()->initialize(2);
>>> \Artisan::call('migrate', [
      '--path' => 'Modules/Muzibu/database/migrations/tenant/2025_11_11_020022_add_hls_fields_to_muzibu_songs_table.php'
    ]);
```

### Kontrole:
```bash
php artisan tinker

>>> tenancy()->initialize(2); // ixtif.com tenant
>>> \DB::select("SHOW COLUMNS FROM muzibu_songs WHERE Field IN ('hls_path', 'hls_converted', 'bitrate', 'metadata')");
# 4 field görüyorsan başarılı!
```

---

## ⚙️ ADIM 5: QUEUE WORKER KONTROLÜ

HLS conversion background'da çalışır, queue worker gerekli.

### Horizon Kontrolü:
```bash
# Horizon çalışıyor mu?
ps aux | grep horizon

# Beklenen output:
# tuufi.com_ ... php artisan horizon
```

### Eğer Horizon Çalışmıyorsa:
```bash
# Horizon başlat
php artisan horizon

# Veya daemon olarak (supervisor ile)
sudo systemctl start horizon
```

### Queue Test:
```bash
php artisan tinker

>>> $song = \Modules\Muzibu\App\Models\Song::first();
>>> \Modules\Muzibu\App\Jobs\ConvertToHLSJob::dispatch($song);
# Job dispatched!

# Horizon dashboard'da kontrol et
# URL: https://yourdomain.com/admin/horizon
```

---

## 📂 ADIM 6: STORAGE KLASÖR İZİNLERİ

HLS output'ları `storage/app/public/muzibu/songs/hls/` altına yazılacak.

```bash
# Storage klasörünü kontrol et
ls -la storage/app/public/

# Eğer 'muzibu' klasörü yoksa oluştur
mkdir -p storage/app/public/muzibu/songs/hls/

# İzinleri ayarla
sudo chown -R tuufi.com_:psaserv storage/app/public/muzibu/
sudo chmod -R 755 storage/app/public/muzibu/

# Test: Job sonrası otomatik oluşacak
```

---

## 🔗 ADIM 7: API ROUTE'LARI

Routes otomatik register olur ama kontrol edelim.

### Route Kontrolü:
```bash
php artisan route:list | grep muzibu

# Beklenen output:
# GET  /api/muzibu/songs/{songId}/stream
# GET  /api/muzibu/songs/{songId}/conversion-status
# POST /api/muzibu/songs/{songId}/play
```

### Eğer Route Görünmüyorsa:
```bash
# Route cache'i temizle
php artisan route:clear
php artisan route:cache

# Config cache'i temizle
php artisan config:clear
php artisan config:cache
```

---

## 🧪 ADIM 8: TEST

### 1. FFmpeg Testi:
```bash
ffmpeg -i "readme/muzibu-modul/Calling on You.mp3" \
  -c copy -start_number 0 -hls_time 10 -hls_list_size 0 \
  -f hls /tmp/hls-test/playlist.m3u8

ls -lh /tmp/hls-test/
# Beklenen: playlist.m3u8 + segment dosyaları
```

### 2. Metadata Extraction Testi:
```bash
php artisan tinker

>>> tenancy()->initialize(2);
>>> $song = \Modules\Muzibu\App\Models\Song::first();
>>> $song->extractMetadata();
# true dönerse başarılı

>>> $song->duration;   // Örnek: 219 (saniye)
>>> $song->bitrate;    // Örnek: 169 (kbps)
>>> $song->metadata;   // JSON array
```

### 3. HLS Conversion Job Testi:
```bash
php artisan tinker

>>> tenancy()->initialize(2);
>>> $song = \Modules\Muzibu\App\Models\Song::first();
>>> \Modules\Muzibu\App\Jobs\ConvertToHLSJob::dispatch($song);

# Horizon'da job'u izle: /admin/horizon

# Conversion sonrası kontrol
>>> $song->refresh();
>>> $song->hls_converted;  // true olmalı
>>> $song->hls_path;       // "muzibu/songs/hls/song-1/playlist.m3u8"
```

### 4. API Endpoint Testi:
```bash
# Stream endpoint
curl -X GET https://yourdomain.com/api/muzibu/songs/1/stream | jq

# Beklenen JSON response:
# {
#   "status": "ready",  // veya "converting"
#   "stream_url": "...",
#   "stream_type": "hls", // veya "mp3"
#   "song": { ... }
# }
```

### 5. Frontend Player Testi:
```bash
# Browser'da aç:
https://yourdomain.com/readme/muzibu/hls-player-component.html

# "Load Test Song (ID: 1)" butonuna tıkla
# Player çalışıyorsa sistem hazır!
```

---

## ⚠️ TROUBLESHOOTING

### Sorun 1: FFmpeg Not Found
```bash
# Çözüm:
which ffmpeg
export PATH=$PATH:/usr/bin
source ~/.bashrc
```

### Sorun 2: Permission Denied (Storage)
```bash
# Çözüm:
sudo chown -R tuufi.com_:psaserv storage/app/public/
sudo chmod -R 755 storage/app/public/
```

### Sorun 3: Queue Job Çalışmıyor
```bash
# Çözüm:
sudo systemctl restart horizon
php artisan queue:restart

# Log kontrol
tail -f storage/logs/laravel.log
```

### Sorun 4: HLS Conversion Failed
```bash
# Log kontrol
tail -f storage/logs/laravel.log | grep "HLS Conversion"

# Manuel FFmpeg test
ffmpeg -i path/to/song.mp3 -c copy -f hls /tmp/test.m3u8

# Hata mesajını oku ve düzelt
```

### Sorun 5: API 404 Error
```bash
# Route cache temizle
php artisan route:clear
php artisan config:clear
php artisan cache:clear

# OPcache reset
curl -s -k https://yourdomain.com/opcache-reset.php
```

---

## ✅ KURULUM KONTROLÜ

### Final Checklist:
```bash
# 1. FFmpeg kurulu mu?
ffmpeg -version
# ✅ ffmpeg version 4.2.10

# 2. getID3 kurulu mu?
composer show james-heinrich/getid3
# ✅ getid3 v1.9.x

# 3. HLS.js kurulu mu?
cat package.json | grep hls.js
# ✅ "hls.js": "^1.x.x"

# 4. Migration uygulandı mı?
php artisan tinker
>>> \DB::select("SHOW COLUMNS FROM muzibu_songs WHERE Field = 'hls_path'");
# ✅ hls_path field var

# 5. Queue worker çalışıyor mu?
ps aux | grep horizon
# ✅ horizon process var

# 6. Routes register oldu mu?
php artisan route:list | grep muzibu
# ✅ 3 route var (stream, conversion-status, play)

# 7. Storage klasörü hazır mı?
ls -la storage/app/public/ | grep muzibu
# ✅ muzibu klasörü var

# 8. API çalışıyor mu?
curl -I https://yourdomain.com/api/muzibu/songs/1/stream
# ✅ HTTP/2 200 OK
```

**Tümü ✅ ise sistem production'a hazır!**

---

## 🚀 PRODUCTION DEPLOYMENT

### Son Adımlar:
```bash
# 1. Cache optimize et
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 2. OPcache reset
curl -s -k https://yourdomain.com/opcache-reset.php

# 3. Horizon restart
sudo systemctl restart horizon

# 4. Test song yükle (admin panel)
# /admin/muzibu/songs/manage

# 5. Frontend entegrasyonu
# Alpine.js component'i sayfaya ekle
```

---

## 📚 DOSYALAR VE KONUMLAR

### Backend Files:
- Job: `Modules/Muzibu/app/Jobs/ConvertToHLSJob.php`
- Controller: `Modules/Muzibu/app/Http/Controllers/Api/SongStreamController.php`
- Model: `Modules/Muzibu/app/Models/Song.php`
- Routes: `Modules/Muzibu/routes/api.php`
- Migration: `Modules/Muzibu/database/migrations/tenant/2025_11_11_020022_add_hls_fields_to_muzibu_songs_table.php`

### Frontend Files:
- Player Component: `readme/muzibu/hls-player-component.html`

### Documentation:
- Requirements: `readme/muzibu/REQUIREMENTS.md`
- Setup Guide: `readme/muzibu/SETUP-GUIDE.md` (bu dosya)
- Architecture: `readme/muzibu/medias/v2/index.html`

### Storage Paths:
- Original MP3: `storage/app/public/muzibu/songs/{filename}.mp3`
- HLS Output: `storage/app/public/muzibu/songs/hls/song-{id}/playlist.m3u8`
- HLS Segments: `storage/app/public/muzibu/songs/hls/song-{id}/segment-*.ts`

---

**🎉 Kurulum tamamlandı! Sorularınız için: readme/muzibu/REQUIREMENTS.md**
