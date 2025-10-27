# 🔧 Storage Link Otomatik Düzeltme Sistemi

**Tarih:** 2025-10-26
**Durum:** ✅ Kalıcı çözüm aktif ve test edildi
**Dosya:** `app/Console/Commands/StorageLink.php`
**Son Güncelleme:** 2025-10-26 (GLOB_ONLYDIR bug fix)

---

## 🚨 SORUN TANIMI

### Orijinal Problem:
Laravel'in default `php artisan storage:link` komutu symlink'leri **root:root** owner ile oluşturuyordu.

### Nginx Güvenlik Ayarı:
```nginx
disable_symlinks if_not_owner "from=/var/www/vhosts/tuufi.com";
```

### Sonuç:
- Symlink owner: `root:root`
- Hedef dosya owner: `tuufi.com_:psaserv`
- Owner uyuşmazlığı → **403 Forbidden**

**Etkilenen URL:** https://ixtif.com/storage/tenant2/*/dosya.png

---

## ✅ KALICI ÇÖZÜM

### Custom Artisan Command
Laravel'in default `storage:link` komutu override edildi:

**Dosya:** `app/Console/Commands/StorageLink.php`

### Özellikler:
1. ✅ **Default behavior korundu**: Tüm Laravel symlink'leri normal oluşturulur
2. ✅ **Otomatik tenant owner fix**: Her çalışmada tenant symlink'lerini düzeltir
3. ✅ **Güvenli**: Sadece symlink owner'ını değiştirir, dosyalara dokunmaz
4. ✅ **Sessiz çalışma**: AI veya kullanıcı yanlışlıkla çalıştırsa bile sorun çıkmaz

### Çalışma Prensibi:
```php
public function handle()
{
    // 1. Normal Laravel storage link işlemi
    foreach ($this->links() as $link => $target) {
        File::link($target, $link);
    }

    // 2. Otomatik tenant symlink owner düzeltme
    $this->fixTenantSymlinkOwners();
}

protected function fixTenantSymlinkOwners()
{
    // public/storage dizininin owner'ını tespit et
    $targetOwner = posix_getpwuid(fileowner(public_path('storage')))['name'];
    $targetGroup = posix_getgrgid(filegroup(public_path('storage')))['name'];

    // Tüm tenant* symlink'lerini bul (GLOB_ONLYDIR KULLANMA!)
    $tenantSymlinks = glob(public_path('storage/tenant*'));

    // Her symlink'in owner'ını düzelt
    foreach ($tenantSymlinks as $symlink) {
        if (!is_link($symlink)) continue; // Sadece symlink'leri işle
        exec("chown -h {$targetOwner}:{$targetGroup} " . escapeshellarg($symlink));
    }
}
```

---

## 🐛 GLOB_ONLYDIR BUG FIX (2025-10-26)

### Tespit Edilen Sorun:
Kod başlangıçta `glob($path, GLOB_ONLYDIR)` kullanıyordu. Bu flag **symlink'leri döndürmüyor**, bu yüzden owner fix çalışmıyordu!

### Çözüm:
```php
// ❌ YANLIŞ - Symlink'leri görmez
$tenantSymlinks = glob($publicStoragePath . '/tenant*', GLOB_ONLYDIR);

// ✅ DOĞRU - Tüm dosya/symlink'leri döndürür
$tenantSymlinks = glob($publicStoragePath . '/tenant*');
```

### Güvenlik:
`is_link()` kontrolü eklenerek sadece symlink'ler işlenir:
```php
if (!is_link($symlink)) {
    continue;
}
```

### Test Sonucu:
```bash
php artisan storage:link
# ✅ Fixed owner for: tenant1 → tuufi.com_:psaserv
# ✅ Fixed owner for: tenant2 → tuufi.com_:psaserv
# ✅ Fixed owner for: tenant3 → tuufi.com_:psaserv

curl -I https://ixtif.com/storage/tenant2/19/0ufxpkujohzrh8nahnm9valr5jg8jgxoaqlwfzaj.png
# HTTP/2 200 OK ✅
```

---

## 🧪 TEST SENARYOSU

### Senaryo: Root kullanıcı yanlışlıkla symlink oluşturur

```bash
# 1. Root olarak symlink oluştur (sorunlu)
sudo ln -s /var/www/vhosts/tuufi.com/httpdocs/storage/tenant2/app/public \
           /var/www/vhosts/tuufi.com/httpdocs/public/storage/tenant2

# 2. Owner kontrol
ls -la public/storage/tenant2
# lrwxrwxrwx  1 root:root  → SORUN!

# 3. Test URL
curl -I https://ixtif.com/storage/tenant2/13/hero.png
# HTTP/2 403 Forbidden → SORUN MEVCUT!

# 4. Otomatik düzeltme çalıştır
php artisan storage:link
# 🔧 Fixing tenant symlink owners...
# ✅ Fixed owner for: tenant2 → tuufi.com_:psaserv

# 5. Owner tekrar kontrol
ls -la public/storage/tenant2
# lrwxrwxrwx  1 tuufi.com_:psaserv  → ✅ DÜZELDİ!

# 6. Test URL
curl -I https://ixtif.com/storage/tenant2/13/hero.png
# HTTP/2 200 OK → ✅ ÇALIŞIYOR!
```

### Test Sonuçları:
- ✅ Root owner'lı symlink → 403 Forbidden (sorun tespit edildi)
- ✅ `php artisan storage:link` çalıştırıldı
- ✅ Otomatik owner düzeltme çalıştı
- ✅ HTTP 200 OK (sorun çözüldü)

---

## 🎯 KULLANICI MESAJI

### Önceki Durum:
```
Kullanıcı: "Claude, storage link'ler 403 veriyor"
Claude: "Symlink owner'larını düzelteyim"
[Manuel chown -h komutu çalıştırır]
```

### Yeni Durum:
```
Kullanıcı: "Claude, storage link'ler 403 veriyor"
Claude: "php artisan storage:link çalıştırıyorum"
[Otomatik düzelir, sorun çözülür]
```

**Avantaj:** AI veya başka biri `php artisan storage:link` çalıştırsa bile artık sorun çıkmaz!

---

## 🔍 TEKNİK DETAYLAR

### Neden Owner Uyuşmazlığı Gerekli?

Nginx güvenlik ayarı:
```nginx
disable_symlinks if_not_owner "from=/var/www/vhosts/tuufi.com";
```

Bu ayar şunu demek:
- **Symlink owner ≠ Target owner** → 403 Forbidden (güvenlik)
- **Symlink owner = Target owner** → İzin verilir

### Hedef Owner Tespiti:
```php
// public/storage dizininin owner'ını otomatik tespit eder
$targetOwner = posix_getpwuid(fileowner(public_path('storage')))['name'];
// Örnek: tuufi.com_

$targetGroup = posix_getgrgid(filegroup(public_path('storage')))['name'];
// Örnek: psaserv
```

### Sadece Symlink Owner Değiştirilir:
```bash
# -h flag: Symlink'in kendisini değiştir, target'ı değil!
chown -h tuufi.com_:psaserv public/storage/tenant2
```

**GÜVEN:** Gerçek fotoğraf dosyalarına asla dokunulmaz!

---

## 📋 BAKIMI VE KONTROL

### Komut Kullanımı:
```bash
# Normal kullanım (default Laravel + otomatik fix)
php artisan storage:link

# Force recreate (mevcut symlink'leri yeniden oluştur)
php artisan storage:link --force

# Relative symlinks (göreceli yollar)
php artisan storage:link --relative
```

### Manuel Kontrol:
```bash
# Symlink owner'larını kontrol et
ls -la public/storage/

# Beklenen çıktı:
# lrwxrwxrwx  1 tuufi.com_ psaserv  tenant1 → ...
# lrwxrwxrwx  1 tuufi.com_ psaserv  tenant2 → ...
# lrwxrwxrwx  1 tuufi.com_ psaserv  tenant3 → ...

# Görsellere erişim testi
curl -I https://ixtif.com/storage/tenant2/13/hero.png
# Beklenen: HTTP/2 200 OK
```

### Sorun Giderme:
```bash
# Eğer hala 403 alıyorsan:
php artisan storage:link --force

# Owner'ları kontrol et
ls -la public/storage/tenant*

# Nginx error log kontrol
tail -f /var/log/nginx/error.log
```

---

## 🚀 GELECEKTEKİ YENİ TENANT'LAR

### Yeni Tenant Ekleme Workflow:
1. Plesk'te domain alias ekle (SEO redirect OFF!)
2. Laravel'de tenant oluştur (tinker)
3. **php artisan storage:link** → Otomatik owner düzeltme çalışır
4. Test URL → 200 OK

**NOT:** Artık manuel `chown -h` komutuna gerek yok!

---

## 📌 ÖZET

### Sorun:
- `php artisan storage:link` → root:root owner
- Nginx disable_symlinks if_not_owner → 403 Forbidden

### Çözüm:
- Custom `StorageLink` command override
- Otomatik tenant symlink owner düzeltme
- Her çalışmada otomatik fix

### Sonuç:
- ✅ AI yanlışlıkla çalıştırsa bile sorun çıkmaz
- ✅ Manuel fix'e gerek kalmaz
- ✅ Yeni tenant'lar otomatik düzelir
- ✅ Gerçek dosyalara hiç dokunulmaz

---

**Durum:** 🟢 Aktif ve çalışıyor
**Test Tarihi:** 2025-10-26
**Son Kontrol:** HTTP 200 OK ✅

## ✅ VERİFİKASYON

### Son Test (2025-10-26):
```bash
# Symlink owner kontrolü
ls -la public/storage/tenant*
# lrwxrwxrwx  1 tuufi.com_ psaserv  tenant1 → ✅
# lrwxrwxrwx  1 tuufi.com_ psaserv  tenant2 → ✅
# lrwxrwxrwx  1 tuufi.com_ psaserv  tenant3 → ✅

# HTTP erişim testleri
curl -I https://ixtif.com/storage/tenant2/19/0ufxpkujohzrh8nahnm9valr5jg8jgxoaqlwfzaj.png
# HTTP/2 200 OK ✅

curl -I https://ixtif.com/storage/tenant2/13/hero.png
# HTTP/2 200 OK ✅

curl -I https://ixtif.com/storage/tenant2/20/gdlqlmda4zgwvqbemsgqjb79l4kqpqfstycg1w9f.png
# HTTP/2 200 OK ✅
```

**Sonuç:** Tüm testler başarılı! 🎉
